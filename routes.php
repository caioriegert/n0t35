<?php
// Mostrar erros em ambiente de desenvolvimento (remova/ajuste em produção)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/NoteController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = $uri;

switch ($route) {
    case '':
    case '/':
        AuthController::checkSession();
        break;

    case '/login':
        AuthController::login();
        break;

    case '/register':
        AuthController::register();
        break;

    case '/logout':
        AuthController::logout();
        break;

    case '/dashboard':
        NoteController::listNotes();
        break;

    case '/account':
        AuthController::account();
        break;

    case '/account/password-update':
        AuthController::updatePassword();
        break;

    case '/account/delete':
        AuthController::deleteAccount();
        break;

    case '/account/delete-notes':
        AuthController::deleteAllNotes();
        break;

    case '/note/create':
        NoteController::createNote();
        break;

    case '/note/view':
        NoteController::view();
        break;

    case '/note/edit':
        NoteController::editNote();
        break;

    case '/note/delete':
        NoteController::deleteNote();
        break;

    case '/upload-image':
        NoteController::uploadImage();
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Página não encontrada</h1>";
        break;
}               