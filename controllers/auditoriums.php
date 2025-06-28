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
        $result = $conn->query("SELECT * FROM auditoriums ORDER BY name");
        $auditoriums = [];
        while ($row = $result->fetch_assoc()) {
            $auditoriums[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $auditoriums]);
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

    $name = trim($data['name'] ?? '');
    $seatsRows = $data['seats_rows'] ?? '';
    $seatsPerRow = $data['seats_per_row'] ?? '';

    // Input validation
    if (empty($name) || empty($seatsRows) || empty($seatsPerRow)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Name, seats rows, and seats per row are required']);
        exit;
    }

    if (!is_numeric($seatsRows) || $seatsRows < 1 || $seatsRows > 50) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Seats rows must be between 1 and 50']);
        exit;
    }

    if (!is_numeric($seatsPerRow) || $seatsPerRow < 1 || $seatsPerRow > 50) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Seats per row must be between 1 and 50']);
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO auditoriums (name, seats_rows, seats_per_row) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $name, $seatsRows, $seatsPerRow);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Auditorium added successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to add auditorium']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
