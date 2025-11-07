<?php
if (!class_exists('User')) {
    class User {
        public static function createUser($pdo, $username, $password) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            return $stmt->execute([$username, $hash]);
        }

        public static function authenticate($pdo, $username, $password) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                return $user;
            }
            return false;
        }

        public static function getById($pdo, $id) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}
