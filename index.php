<?php

// Include routes configuration
require_once __DIR__ . '/core/routes.php';

// Add CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$base_dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove base directory from request path
if (strpos($request, $base_dir) === 0) {
    $request = substr($request, strlen($base_dir));
}

// Handle root request
if ($request == '') {
    $request = '/';
}

if (isset($apis[$request])) {
    $controller_name = $apis[$request]['controller'];
    $method = $apis[$request]['method'];
    require_once __DIR__ . "/controllers/{$controller_name}.php";

    $controller = new $controller_name();
    if (method_exists($controller, $method)) {
        $controller->$method();
    } else {
        http_response_code(404);
        echo json_encode(['error' => "Method {$method} not found in {$controller_name}"]);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}
