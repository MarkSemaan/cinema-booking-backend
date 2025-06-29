<?php

require_once __DIR__ . '/../controllers/UsersController.php';

header('Content-Type: application/json');

$controller = new UsersController();
$action = $_GET['action'] ?? null; //Get the action from the request

// Sort of a mini router, based on the action, call the corresponding method
switch ($action) {
    case 'register':
        $controller->register();
        break;
    case 'login':
        $controller->login();
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Invalid action']);
}
