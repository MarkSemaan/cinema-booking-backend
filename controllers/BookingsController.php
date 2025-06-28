<?php

require_once __DIR__ . '/../models/Booking.php';

class BookingsController
{
    public function create()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['user_id']) || empty($data['showtime_id']) || empty($data['seats'])) {
            http_response_code(400);
            echo json_encode(['error' => 'User ID, showtime ID, and seats are required']);
            return;
        }

        if (!is_array($data['seats']) || empty($data['seats'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Seats must be a non-empty array']);
            return;
        }

        try {
            require_once __DIR__ . '/../connection/db_connection.php';
            $mysqli = DBConnection::getInstance()->getConnection();

            // Start transaction
            $mysqli->begin_transaction();

            // Validate user exists
            $stmt = $mysqli->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->bind_param("i", $data['user_id']);
            $stmt->execute();
            if (!$stmt->get_result()->fetch_assoc()) {
                $mysqli->rollback();
                http_response_code(400);
                echo json_encode(['error' => 'Invalid user ID']);
                return;
            }

            // Validate showtime exists
            $stmt = $mysqli->prepare("SELECT id FROM showtimes WHERE id = ?");
            $stmt->bind_param("i", $data['showtime_id']);
            $stmt->execute();
            if (!$stmt->get_result()->fetch_assoc()) {
                $mysqli->rollback();
                http_response_code(400);
                echo json_encode(['error' => 'Invalid showtime ID']);
                return;
            }

            // Check if seats are already booked
            foreach ($data['seats'] as $seat) {
                if (!isset($seat['row']) || !isset($seat['number'])) {
                    $mysqli->rollback();
                    http_response_code(400);
                    echo json_encode(['error' => 'Each seat must have row and number']);
                    return;
                }

                $stmt = $mysqli->prepare("
                    SELECT bs.id FROM booked_seats bs 
                    JOIN bookings b ON bs.booking_id = b.id 
                    WHERE b.showtime_id = ? AND bs.seat_row = ? AND bs.seat_number = ?
                ");
                $stmt->bind_param("iis", $data['showtime_id'], $seat['row'], $seat['number']);
                $stmt->execute();
                if ($stmt->get_result()->fetch_assoc()) {
                    $mysqli->rollback();
                    http_response_code(409);
                    echo json_encode([
                        'error' => 'Seat already booked',
                        'seat' => "Row {$seat['row']}, Seat {$seat['number']}"
                    ]);
                    return;
                }
            }

            // Create booking
            $booking = new Booking();
            $bookingId = $booking->create([
                'user_id' => (int)$data['user_id'],
                'showtime_id' => (int)$data['showtime_id']
            ]);

            if (!$bookingId) {
                $mysqli->rollback();
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create booking']);
                return;
            }

            // Create booked seats
            $stmt = $mysqli->prepare("INSERT INTO booked_seats (booking_id, seat_row, seat_number) VALUES (?, ?, ?)");
            foreach ($data['seats'] as $seat) {
                $stmt->bind_param("iis", $bookingId, $seat['row'], $seat['number']);
                if (!$stmt->execute()) {
                    $mysqli->rollback();
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to book seats']);
                    return;
                }
            }

            $mysqli->commit();
            http_response_code(201);
            echo json_encode([
                'message' => 'Booking created successfully',
                'booking_id' => $bookingId,
                'seats_booked' => count($data['seats'])
            ]);
        } catch (Exception $e) {
            if (isset($mysqli)) {
                $mysqli->rollback();
            }
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function userBookings()
    {
        $userId = $_GET['user_id'] ?? null;

        if (!$userId || !is_numeric($userId)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid user ID is required']);
            return;
        }

        try {
            require_once __DIR__ . '/../connection/db_connection.php';
            $mysqli = DBConnection::getInstance()->getConnection();

            $stmt = $mysqli->prepare("
                SELECT 
                    b.id as booking_id,
                    b.booking_time,
                    s.showtime,
                    m.title as movie_title,
                    a.name as auditorium_name,
                    GROUP_CONCAT(CONCAT('Row ', bs.seat_row, ' Seat ', bs.seat_number) SEPARATOR ', ') as seats
                FROM bookings b
                JOIN showtimes s ON b.showtime_id = s.id
                JOIN movies m ON s.movie_id = m.id
                JOIN auditoriums a ON s.auditorium_id = a.id
                JOIN booked_seats bs ON b.id = bs.booking_id
                WHERE b.user_id = ?
                GROUP BY b.id
                ORDER BY b.booking_time DESC
            ");

            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $bookings = $result->fetch_all(MYSQLI_ASSOC);

            http_response_code(200);
            echo json_encode(['bookings' => $bookings]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve bookings']);
        }
    }

    public function get()
    {
        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid booking ID is required']);
            return;
        }

        try {
            require_once __DIR__ . '/../connection/db_connection.php';
            $mysqli = DBConnection::getInstance()->getConnection();

            $stmt = $mysqli->prepare("
                SELECT 
                    b.id as booking_id,
                    b.user_id,
                    b.showtime_id,
                    b.booking_time,
                    s.showtime,
                    m.title as movie_title,
                    m.director,
                    a.name as auditorium_name
                FROM bookings b
                JOIN showtimes s ON b.showtime_id = s.id
                JOIN movies m ON s.movie_id = m.id
                JOIN auditoriums a ON s.auditorium_id = a.id
                WHERE b.id = ?
            ");

            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $booking = $result->fetch_assoc();

            if (!$booking) {
                http_response_code(404);
                echo json_encode(['error' => 'Booking not found']);
                return;
            }

            // Get seats for this booking
            $stmt = $mysqli->prepare("SELECT seat_row, seat_number FROM booked_seats WHERE booking_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $seats = $result->fetch_all(MYSQLI_ASSOC);

            $booking['seats'] = $seats;

            http_response_code(200);
            echo json_encode(['booking' => $booking]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve booking']);
        }
    }

    public function cancel()
    {
        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid booking ID is required']);
            return;
        }

        try {
            $booking = new Booking();

            // Check if booking exists
            if (!$booking->find((int)$id)) {
                http_response_code(404);
                echo json_encode(['error' => 'Booking not found']);
                return;
            }

            // Delete booking (cascade will handle booked_seats)
            if ($booking->delete((int)$id)) {
                http_response_code(200);
                echo json_encode(['message' => 'Booking canceled successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to cancel booking']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function availableSeats()
    {
        $showtimeId = $_GET['showtime_id'] ?? null;

        if (!$showtimeId || !is_numeric($showtimeId)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid showtime ID is required']);
            return;
        }

        try {
            require_once __DIR__ . '/../connection/db_connection.php';
            $mysqli = DBConnection::getInstance()->getConnection();

            // Get auditorium capacity
            $stmt = $mysqli->prepare("
                SELECT a.seats_rows, a.seats_per_row 
                FROM auditoriums a 
                JOIN showtimes s ON a.id = s.auditorium_id 
                WHERE s.id = ?
            ");
            $stmt->bind_param("i", $showtimeId);
            $stmt->execute();
            $result = $stmt->get_result();
            $auditorium = $result->fetch_assoc();

            if (!$auditorium) {
                http_response_code(404);
                echo json_encode(['error' => 'Showtime not found']);
                return;
            }

            // Get booked seats
            $stmt = $mysqli->prepare("
                SELECT bs.seat_row, bs.seat_number 
                FROM booked_seats bs 
                JOIN bookings b ON bs.booking_id = b.id 
                WHERE b.showtime_id = ?
            ");
            $stmt->bind_param("i", $showtimeId);
            $stmt->execute();
            $result = $stmt->get_result();
            $bookedSeats = $result->fetch_all(MYSQLI_ASSOC);

            http_response_code(200);
            echo json_encode([
                'auditorium' => [
                    'rows' => (int)$auditorium['seats_rows'],
                    'seats_per_row' => (int)$auditorium['seats_per_row']
                ],
                'booked_seats' => $bookedSeats,
                'total_seats' => $auditorium['seats_rows'] * $auditorium['seats_per_row'],
                'booked_count' => count($bookedSeats),
                'available_count' => ($auditorium['seats_rows'] * $auditorium['seats_per_row']) - count($bookedSeats)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve seat information']);
        }
    }
}
