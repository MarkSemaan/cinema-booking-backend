<?php

require_once __DIR__ . '/../models/Auditorium.php';

class AuditoriumsController
{
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
    public function create()
    {
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
