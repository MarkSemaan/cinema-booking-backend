<?php

// Add CORS headers to allow cross-origin requests, allow  separate frontend and backend to connect
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: image/jpeg'); // Default content type

// Respond to the browser's safety check before the actual request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow GET requests, the uploading is handled in the controller
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get the image path from the URL
$imagePath = $_GET['path'] ?? '';

if (empty($imagePath)) {
    http_response_code(400);
    echo json_encode(['error' => 'Image path is required']);
    exit();
}

// Security: Prevent directory traversal attacks, can't let anyone access other files
if (strpos($imagePath, '..') !== false || strpos($imagePath, '/') === 0) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid image path']);
    exit();
}

// Construct the full file path
$fullPath = __DIR__ . '/../' . $imagePath;

// Check if file exists
if (!file_exists($fullPath)) {
    http_response_code(404);
    echo json_encode(['error' => 'Image not found']);
    exit();
}

// Get file extension to set correct content type
$extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

if (!in_array($extension, $allowedExtensions)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid file type']);
    exit();
}

// Set appropriate content type
switch ($extension) {
    case 'jpg':
    case 'jpeg':
        header('Content-Type: image/jpeg');
        break;
    case 'png':
        header('Content-Type: image/png');
        break;
    case 'gif':
        header('Content-Type: image/gif');
        break;
}

// Set cache headers for better performance
header('Cache-Control: public, max-age=31536000'); // Cache for 1 year
header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));

// Output the image
readfile($fullPath);
