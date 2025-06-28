<?php

require_once __DIR__ . '/../controllers/ShowtimesController.php';

header('Content-Type: application/json');

$controller = new ShowtimesController();
$action = $_GET['action'] ?? null;

switch ($action) {
    case 'list':
        $controller->list();
        break;
    case 'get':
        $controller->get();
        break;
    case 'by_movie':
        $controller->byMovie();
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
