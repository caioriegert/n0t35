<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    public static function checkSession()
    {
        session_start();
        if (isset($_SESSION['user_id'])) {
            // Usuário está logado, redireciona para o dashboard
            header('Location: /dashboard');
        } else {
            // Usuário não está logado, redireciona para o login
            header('Location: /login');
        }
        exit();
    }

    public static function register()
    {
        global $pdo;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            if (empty($username) || empty($password)) {
                $error = "Preencha todos os campos.";
                include __DIR__ . '/../views/register.php';
                return;
            }

            // Cria usuário
            $created = User::createUser($pdo, $username, $password);

            if ($created) {
                header("Location: /login?registered=1");
                exit;
            } else {
                $error = "Erro ao criar usuário (talvez o nome já exista).";
                include __DIR__ . '/../views/register.php';
            }
        } else {
            include __DIR__ . '/../views/register.php';
        }
    }

    public static function login()
    {
        global $pdo;
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            $user = User::authenticate($pdo, $username, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: /dashboard");
                exit;
            } else {
                $error = "Usuário ou senha inválidos.";
                include __DIR__ . '/../views/login.php';
            }
        } else {
            include __DIR__ . '/../views/login.php';
        }
    }

    public static function logout()
    {
        session_start();
        session_destroy();
        header("Location: /login");
        exit;
    }

    // Helper para obter conexão PDO (fallback sqlite)
    protected static function getPdo()
    {
        if (!empty($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
            return $GLOBALS['pdo'];
        }
        try {
            $pdo = new PDO('sqlite:' . __DIR__ . '/../../database.sqlite');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (Exception $e) {
            error_log('PDO connection error: ' . $e->getMessage());
            return null;
        }
    }

    // Página da conta (corrigida / normaliza username e id)
    public static function account()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $pdo = self::getPdo();
        $user = null;
        $notesCount = 0;

        // Carrega models (usar require_once para evitar redeclarações)
        require_once __DIR__ . '/../models/user.php';
        require_once __DIR__ . '/../models/note.php';

        // Tenta obter via model User (várias assinaturas possíveis)
        if (class_exists('User')) {
            try {
                if (method_exists('User', 'getUserById')) {
                    $user = User::getUserById($pdo, (int)$_SESSION['user_id']);
                } elseif (method_exists('User', 'findById')) {
                    $user = User::findById($pdo, (int)$_SESSION['user_id']);
                } elseif (method_exists('User', 'getById')) {
                    $user = User::getById($pdo, (int)$_SESSION['user_id']);
                }
            } catch (Exception $e) {
                error_log('Erro ao obter usuário via model: ' . $e->getMessage());
                $user = null;
            }
        }

        // Se o model retornou um objeto, transformar em array
        if (is_object($user)) {
            $user = (array) $user;
        }

        // Fallback direto ao DB (busca campos úteis: id, username, name, email)
        if (!$user && $pdo) {
            try {
                $stmt = $pdo->prepare('SELECT id, username, name, email FROM users WHERE id = ? LIMIT 1');
                $stmt->execute([ (int)$_SESSION['user_id'] ]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                error_log('Erro ao buscar usuário: ' . $e->getMessage());
                $user = null;
            }
        }

        // Garantias / normalização: sempre ter 'id' e 'username'
        $user = (array) ($user ?? []);
        if (empty($user['id'])) {
            $user['id'] = (int) $_SESSION['user_id'];
        }
        if (empty($user['username'])) {
            if (!empty($user['name'])) {
                $user['username'] = $user['name'];
            } elseif (!empty($user['email'])) {
                $parts = explode('@', $user['email']);
                $user['username'] = $parts[0];
            } else {
                $user['username'] = 'user' . $user['id'];
            }
        }

        // contar notas do usuário
        if ($pdo) {
            try {
                $stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM notes WHERE user_id = ?');
                $stmt->execute([ (int)$_SESSION['user_id'] ]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $notesCount = $row ? (int)$row['c'] : 0;
            } catch (Exception $e) {
                error_log('Erro ao contar notas: ' . $e->getMessage());
            }
        }

        // Flash messages via session (se existirem)
        $error = $_SESSION['account_error'] ?? null;
        $success = $_SESSION['account_success'] ?? null;
        unset($_SESSION['account_error'], $_SESSION['account_success']);

        // disponibiliza variáveis para a view
        include __DIR__ . '/../views/account.php';
    }

    // Atualizar senha
    public static function updatePassword()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
            header('Location: /account');
            exit;
        }

        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($new) < 6) {
            $_SESSION['account_error'] = 'A nova senha deve ter pelo menos 6 caracteres.';
            header('Location: /account');
            exit;
        }
        if ($new !== $confirm) {
            $_SESSION['account_error'] = 'A confirmação de senha não corresponde.';
            header('Location: /account');
            exit;
        }

        $pdo = self::getPdo();
        if (!$pdo) {
            $_SESSION['account_error'] = 'Erro de conexão.';
            header('Location: /account');
            exit;
        }

        try {
            // Buscar a coluna de senha existente (compatível com diferentes esquemas)
            $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
            $stmt->execute([ (int)$_SESSION['user_id'] ]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                $_SESSION['account_error'] = 'Usuário não encontrado.';
                header('Location: /account');
                exit;
            }

            // Determinar nome da coluna de hash de senha no banco
            if (array_key_exists('password_hash', $row)) {
                $pwdCol = 'password_hash';
            } elseif (array_key_exists('password', $row)) {
                $pwdCol = 'password';
            } else {
                // fallback: tentar achar alguma coluna contendo "pass"
                $pwdCol = null;
                foreach ($row as $k => $v) {
                    if (stripos($k, 'pass') !== false) { $pwdCol = $k; break; }
                }
            }

            if (!$pwdCol) {
                error_log('updatePassword: nenhuma coluna de senha encontrada para user_id=' . (int)$_SESSION['user_id']);
                $_SESSION['account_error'] = 'Erro ao atualizar senha.';
                header('Location: /account');
                exit;
            }

            // Verifica senha atual
            if (!password_verify($current, $row[$pwdCol])) {
                $_SESSION['account_error'] = 'Senha atual incorreta.';
                header('Location: /account');
                exit;
            }

            // Atualiza armazenando na coluna padrão "password_hash" (ou na mesma coluna encontrada)
            $newHash = password_hash($new, PASSWORD_DEFAULT);
            $updateCol = $pwdCol === 'password_hash' ? 'password_hash' : $pwdCol;
            $stmt = $pdo->prepare("UPDATE users SET {$updateCol} = ? WHERE id = ?");
            $stmt->execute([$newHash, (int)$_SESSION['user_id']]);

            $_SESSION['account_success'] = 'Senha atualizada com sucesso.';
            header('Location: /account');
            exit;
        } catch (Exception $e) {
            error_log('Erro ao atualizar senha: ' . $e->getMessage());
            $_SESSION['account_error'] = 'Erro ao atualizar senha.';
            header('Location: /account');
            exit;
        }
    }

    // Deletar todas as notas do usuário
    public static function deleteAllNotes()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
            header('Location: /account');
            exit;
        }

        $pdo = self::getPdo();
        if (!$pdo) {
            $_SESSION['account_error'] = 'Erro de conexão.';
            header('Location: /account');
            exit;
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM notes WHERE user_id = ?');
            $stmt->execute([(int)$_SESSION['user_id']]);
            $_SESSION['account_success'] = 'Todas as notas foram removidas.';
            header('Location: /account');
            exit;
        } catch (Exception $e) {
            error_log('Erro ao apagar notas: ' . $e->getMessage());
            $_SESSION['account_error'] = 'Erro ao apagar notas.';
            header('Location: /account');
            exit;
        }
    }

    // Deletar conta do usuário (e suas notas)
    public static function deleteAccount()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
            header('Location: /account');
            exit;
        }

        $pdo = self::getPdo();
        if (!$pdo) {
            $_SESSION['account_error'] = 'Erro de conexão.';
            header('Location: /account');
            exit;
        }

        try {
            $pdo->beginTransaction();

            // apagar notas
            $stmt = $pdo->prepare('DELETE FROM notes WHERE user_id = ?');
            $stmt->execute([(int)$_SESSION['user_id']]);

            // apagar usuário
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([(int)$_SESSION['user_id']]);

            $pdo->commit();

            // destruir sessão e redirecionar para registro
            session_unset();
            session_destroy();
            header('Location: /register');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log('Erro ao apagar conta: ' . $e->getMessage());
            $_SESSION['account_error'] = 'Erro ao apagar conta.';
            header('Location: /account');
            exit;
        }
    }
}