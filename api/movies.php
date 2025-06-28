<?php

require_once __DIR__ . '/../controllers/MoviesController.php';

header('Content-Type: application/json');

$controller = new MoviesController();
$action = $_GET['action'] ?? null;

switch ($action) {
    case 'list':
        $controller->list();
        break;
    case 'get':
        $controller->get();
        break;
    case 'create':
        $controller->create();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Invalid action']);
}
