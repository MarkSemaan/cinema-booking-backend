<?php

require_once __DIR__ . '/../controllers/BookingsController.php';

header('Content-Type: application/json');

$controller = new BookingsController();
$action = $_GET['action'] ?? null; //Get the action from the request

// Sort of a mini router, based on the action, call the corresponding method
switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'user_bookings':
        $controller->userBookings();
        break;
    case 'get':
        $controller->get();
        break;
    case 'cancel':
        $controller->cancel();
        break;
    case 'available_seats':
        $controller->availableSeats();
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Invalid action']);
}
