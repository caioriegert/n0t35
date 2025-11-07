<?php
if (!class_exists('Note')) {
    class Note {
        public static function createNote($pdo, $userId, $title, $content, $imagePath) {
            try {
                $stmt = $pdo->prepare("INSERT INTO notes (user_id, title, content, image_path) VALUES (?, ?, ?, ?)");
                $success = $stmt->execute([$userId, $title, $content, $imagePath]);
                if (!$success) {
                    error_log("Falha ao executar query em createNote. User ID: $userId, Title: $title");
                }
                return $success;
            } catch (PDOException $e) {
                error_log("Erro ao criar nota: " . $e->getMessage());
                throw $e;
            }
        }

        public static function getNotesByUser($pdo, $userId) {
            try {
                $stmt = $pdo->prepare("SELECT id, title, content, image_path FROM notes WHERE user_id = ? ORDER BY id DESC");
                $stmt->execute([$userId]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erro ao buscar notas: " . $e->getMessage());
                throw $e;
            }
        }

        public static function getNoteById($pdo, $id) {
            try {
                $stmt = $pdo->prepare("SELECT id, user_id, title, content, image_path FROM notes WHERE id = ?");
                $stmt->execute([$id]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erro ao buscar nota ID $id: " . $e->getMessage());
                throw $e;
            }
        }

        public static function updateNote($pdo, $id, $title, $content, $imagePath) {
            try {
                $stmt = $pdo->prepare("UPDATE notes SET title = ?, content = ?, image_path = ? WHERE id = ?");
                $success = $stmt->execute([$title, $content, $imagePath, $id]);
                if (!$success) {
                    error_log("Falha ao executar query em updateNote. ID: $id, Title: $title");
                }
                return $success;
            } catch (PDOException $e) {
                error_log("Erro ao atualizar nota ID $id: " . $e->getMessage());
                throw $e;
            }
        }

        public static function deleteNote($pdo, $id) {
            try {
                $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
                return $stmt->execute([$id]);
            } catch (PDOException $e) {
                error_log("Erro ao excluir nota ID $id: " . $e->getMessage());
                throw $e;
            }
        }
    }
}