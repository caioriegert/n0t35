<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../models/Note.php';
require_once __DIR__ . '/../models/User.php';

class NoteController {
    public static function listNotes() {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        global $pdo;

        $user = User::getById($pdo, $_SESSION['user_id']);
        $notes = Note::getNotesByUser($pdo, $_SESSION['user_id']);

        include __DIR__ . '/../views/dashboard.php';
    }

    public static function createNote() {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        global $pdo;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $content = $_POST['content'] ?? '';

            error_log("Título: $title");
            error_log("Conteúdo: " . substr($content, 0, 100) . "...");

            if (empty($title)) {
                $error = "O título é obrigatório.";
                include __DIR__ . '/../views/note_form.php';
                return;
            }

            if (strlen($content) === 0) {
                $error = "O conteúdo não pode estar vazio.";
                include __DIR__ . '/../views/note_form.php';
                return;
            }

            try {
                Note::createNote($pdo, $_SESSION['user_id'], $title, $content, null);
                header("Location: /dashboard");
                exit;
            } catch (Exception $e) {
                $error = "Erro ao salvar nota: " . $e->getMessage();
                error_log("Erro ao criar nota: " . $e->getMessage());
                include __DIR__ . '/../views/note_form.php';
            }
        } else {
            include __DIR__ . '/../views/note_form.php';
        }
    }

    public static function view()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            header('Location: /dashboard');
            exit;
        }

        // Carrega o model
        require_once __DIR__ . '/../models/note.php';

        $note = false;
        $pdo = null;

        // Tenta reutilizar uma conexão PDO já disponível
        if (!empty($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
            $pdo = $GLOBALS['pdo'];
        } elseif (!empty($_SERVER['HOME'])) {
            // fallback: tenta abrir sqlite local (ajuste se seu projeto usa MySQL)
            try {
                $pdo = new PDO('sqlite:' . __DIR__ . '/../../database.sqlite');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $e) {
                // não blockar - tentaremos outros caminhos abaixo
                error_log('Falha ao criar PDO fallback: ' . $e->getMessage());
                $pdo = null;
            }
        }

        // Se o model Note tem o método getNoteById, chame-o passando $pdo (o model espera $pdo)
        if (class_exists('Note') && method_exists('Note', 'getNoteById')) {
            if (!$pdo) {
                // última tentativa: criar PDO sqlite sem depender de HOME
                try {
                    $pdo = new PDO('sqlite:' . __DIR__ . '/../../database.sqlite');
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (Exception $e) {
                    error_log('Não foi possível obter conexão PDO para buscar nota: ' . $e->getMessage());
                    $pdo = null;
                }
            }

            if ($pdo) {
                try {
                    $note = Note::getNoteById($pdo, $id);
                } catch (Exception $e) {
                    error_log('Erro ao chamar Note::getNoteById: ' . $e->getMessage());
                    $note = false;
                }
            }
        }

        // Fallback direto ao DB caso o model não tenha o método esperado
        if (!$note) {
            try {
                if (!$pdo) {
                    $pdo = new PDO('sqlite:' . __DIR__ . '/../../database.sqlite');
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                }
                $stmt = $pdo->prepare('SELECT * FROM notes WHERE id = ? LIMIT 1');
                $stmt->execute([$id]);
                $note = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                error_log('Fallback DB falhou ao buscar nota: ' . $e->getMessage());
                $note = false;
            }
        }

        // Se não encontrou, volta ao dashboard
        if (empty($note)) {
            header('Location: /dashboard');
            exit;
        }

        // (Opcional) proteção: só permitir visualizar nota do próprio usuário
        if (!empty($_SESSION['user_id']) && isset($note['user_id']) && $note['user_id'] != $_SESSION['user_id']) {
            header('Location: /dashboard');
            exit;
        }

        // Disponibiliza $note para a view
        include __DIR__ . '/../views/note_view.php';
    }

    public static function editNote() {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        global $pdo;

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: /dashboard");
            exit;
        }

        $note = Note::getNoteById($pdo, $id);

        if (!$note || $note['user_id'] !== $_SESSION['user_id']) {
            header("Location: /dashboard");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $content = $_POST['content'] ?? '';

            error_log("Editando nota ID: $id, Título: $title");
            error_log("Conteúdo: " . substr($content, 0, 100) . "...");

            if (empty($title)) {
                $error = "O título é obrigatório.";
                include __DIR__ . '/../views/note_form.php';
                return;
            }

            if (strlen($content) === 0) {
                $error = "O conteúdo não pode estar vazio.";
                include __DIR__ . '/../views/note_form.php';
                return;
            }

            try {
                Note::updateNote($pdo, $id, $title, $content, null);
                header("Location: /dashboard");
                exit;
            } catch (Exception $e) {
                $error = "Erro ao atualizar nota: " . $e->getMessage();
                error_log("Erro ao atualizar nota: " . $e->getMessage());
                include __DIR__ . '/../views/note_form.php';
            }
        } else {
            include __DIR__ . '/../views/note_form.php';
        }
    }

    public static function deleteNote() {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        global $pdo;

        $id = $_GET['id'] ?? null;
        if ($id) {
            Note::deleteNote($pdo, $id);
        }

        header("Location: /dashboard");
        exit;
    }

    public static function uploadImage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetFile = $uploadDir . $imageName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = '/uploads/' . $imageName;
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'imagePath' => $imagePath]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Falha ao fazer upload da imagem.']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Nenhuma imagem enviada.']);
        }
        exit;
    }
}