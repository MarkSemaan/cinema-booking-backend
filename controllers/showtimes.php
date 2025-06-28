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
        $result = $conn->query("
            SELECT s.*, m.title as movie_title, a.name as auditorium_name 
            FROM showtimes s 
            JOIN movies m ON s.movie_id = m.id 
            JOIN auditoriums a ON s.auditorium_id = a.id 
            ORDER BY s.showtime
        ");
        $showtimes = [];
        while ($row = $result->fetch_assoc()) {
            $showtimes[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $showtimes]);
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

    $movieId = $data['movie_id'] ?? '';
    $auditoriumId = $data['auditorium_id'] ?? '';
    $showtime = trim($data['showtime'] ?? '');

    // Input validation
    if (empty($movieId) || empty($auditoriumId) || empty($showtime)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Movie ID, auditorium ID, and showtime are required']);
        exit;
    }

    if (!is_numeric($movieId) || $movieId < 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid movie ID']);
        exit;
    }

    if (!is_numeric($auditoriumId) || $auditoriumId < 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid auditorium ID']);
        exit;
    }

    // Validate showtime format (YYYY-MM-DD HH:MM:SS)
    if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $showtime)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid showtime format. Use YYYY-MM-DD HH:MM:SS']);
        exit;
    }

    // Check if movie exists
    $stmt = $conn->prepare("SELECT id FROM movies WHERE id = ?");
    $stmt->bind_param("i", $movieId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Movie not found']);
        exit;
    }

    // Check if auditorium exists
    $stmt = $conn->prepare("SELECT id FROM auditoriums WHERE id = ?");
    $stmt->bind_param("i", $auditoriumId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Auditorium not found']);
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO showtimes (movie_id, auditorium_id, showtime) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $movieId, $auditoriumId, $showtime);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Showtime added successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to add showtime']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
