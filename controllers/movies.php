<?php
require_once '../connection/db_connection.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$db = DBConnection::getInstance();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $result = $conn->query("SELECT * FROM movies ORDER BY release_year DESC");
        $movies = [];
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $movies]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
        exit;
    }

    $title = trim($data['title'] ?? '');
    $director = trim($data['director'] ?? '');
    $releaseYear = $data['release_year'] ?? '';
    $genre = trim($data['genre'] ?? '');
    $synopsis = trim($data['synopsis'] ?? '');
    $posterUrl = trim($data['poster_url'] ?? '');

    // Input validation
    if (empty($title) || empty($director) || empty($releaseYear) || empty($genre)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Title, director, release year, and genre are required']);
        exit;
    }

    if (!is_numeric($releaseYear) || $releaseYear < 1900 || $releaseYear > date('Y') + 10) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid release year']);
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO movies (title, director, release_year, genre, synopsis, poster_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisss", $title, $director, $releaseYear, $genre, $synopsis, $posterUrl);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Movie added successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to add movie']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
