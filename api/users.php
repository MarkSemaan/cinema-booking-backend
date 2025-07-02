<?php

require_once __DIR__ . '/../controllers/UsersController.php';

// Add CORS headers to allow cross-origin requests, allow  separate frontend and backend to connect
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Respond to the browser's safety check before the actual request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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
