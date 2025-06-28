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
$action = $_GET['action'] ?? '';

if ($method === 'GET') {
    try {
        $result = $conn->query("
            SELECT b.*, u.username, m.title as movie_title, s.showtime, a.name as auditorium_name
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN showtimes s ON b.showtime_id = s.id
            JOIN movies m ON s.movie_id = m.id
            JOIN auditoriums a ON s.auditorium_id = a.id
            ORDER BY b.created_at DESC
        ");
        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $bookings]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} elseif ($method === 'POST' && $action === 'create') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
        exit;
    }

    $userId = $data['user_id'] ?? '';
    $showtimeId = $data['showtime_id'] ?? '';
    $seats = $data['seats'] ?? [];

    // Input validation
    if (empty($userId) || empty($showtimeId) || empty($seats)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'User ID, showtime ID, and seats are required']);
        exit;
    }

    if (!is_numeric($userId) || $userId < 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
        exit;
    }

    if (!is_numeric($showtimeId) || $showtimeId < 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid showtime ID']);
        exit;
    }

    if (!is_array($seats) || count($seats) === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'At least one seat must be selected']);
        exit;
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }

    // Check if showtime exists
    $stmt = $conn->prepare("SELECT id FROM showtimes WHERE id = ?");
    $stmt->bind_param("i", $showtimeId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Showtime not found']);
        exit;
    }

    // Check if seats are already booked
    foreach ($seats as $seat) {
        if (!isset($seat['row']) || !isset($seat['number'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid seat data']);
            exit;
        }

        $stmt = $conn->prepare("
            SELECT bs.id FROM booked_seats bs
            JOIN bookings b ON bs.booking_id = b.id
            WHERE b.showtime_id = ? AND bs.seat_row = ? AND bs.seat_number = ?
        ");
        $stmt->bind_param("iis", $showtimeId, $seat['row'], $seat['number']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Seat {$seat['row']}-{$seat['number']} is already booked"]);
            exit;
        }
    }

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, showtime_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $showtimeId);
        $stmt->execute();
        $bookingId = $stmt->insert_id;

        $stmtSeat = $conn->prepare("INSERT INTO booked_seats (booking_id, seat_row, seat_number) VALUES (?, ?, ?)");
        foreach ($seats as $seat) {
            $stmtSeat->bind_param("iis", $bookingId, $seat['row'], $seat['number']);
            $stmtSeat->execute();
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Booking created successfully', 'booking_id' => $bookingId]);
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to create booking']);
    }
} elseif ($method === 'GET' && $action === 'user') {
    $userId = $_GET['user_id'] ?? '';

    if (empty($userId) || !is_numeric($userId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Valid user ID is required']);
        exit;
    }

    try {
        $stmt = $conn->prepare("
            SELECT b.*, m.title as movie_title, s.showtime, a.name as auditorium_name
            FROM bookings b
            JOIN showtimes s ON b.showtime_id = s.id
            JOIN movies m ON s.movie_id = m.id
            JOIN auditoriums a ON s.auditorium_id = a.id
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $bookings]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
