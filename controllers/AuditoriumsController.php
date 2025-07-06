<?php

require_once __DIR__ . '/../models/Auditorium.php';

class AuditoriumsController
{
    //Check if user is admin
    private function isAdmin($userId)
    {
        if (!$userId) return false;

        try {
            require_once __DIR__ . '/../models/User.php';
            $user = new User();
            $userData = $user->find((int)$userId);
            return $userData && (bool)$userData['is_admin'];
        } catch (Exception $e) {
            return false;
        }
    }

    //Require admin access for create, update and delete methods
    private function requireAdmin()
    {
        // Check for user_id in multiple sources
        $userId = $_GET['user_id'] ?? $_POST['user_id'] ?? null;

        // If not found in GET or POST, look for it in JSON
        if (!$userId) {
            $jsonData = json_decode(file_get_contents('php://input'), true);
            $userId = $jsonData['user_id'] ?? null;
        }

        if (!$this->isAdmin($userId)) {
            http_response_code(403);
            echo json_encode(['error' => 'Admin access required']);
            return false;
        }
        return true;
    }

    public function list()
    {
        try {
            $auditorium = new Auditorium();
            $auditoriums = $auditorium->findAll();

            http_response_code(200);
            echo json_encode(['auditoriums' => $auditoriums]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve auditoriums']);
        }
    }
    public function get()
    {
        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid auditorium ID is required']);
            return;
        }

        try {
            $auditorium = new Auditorium();
            $auditoriumData = $auditorium->find((int)$id);

            if ($auditoriumData) {
                http_response_code(200);
                echo json_encode(['auditorium' => $auditoriumData]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Auditorium not found']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve auditorium']);
        }
    }
    public function create()
    {
        if (!$this->requireAdmin()) return;

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['name']) || empty($data['seats_rows']) || empty($data['seats_per_row'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Name, Seat Rows, and Seat Per Row are required']);
            return;
        }

        try {
            $auditorium = new Auditorium();
            $auditoriumId = $auditorium->create([
                'name' => $data['name'],
                'seats_rows' => (int)$data['seats_rows'],
                'seats_per_row' => (int)$data['seats_per_row'],
            ]);

            if ($auditoriumId) {
                http_response_code(201);
                echo json_encode(['message' => 'Auditorium created successfully', 'auditorium_id' => $auditoriumId]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create auditorium']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create auditorium']);
        }
    }
}
