<?php

require_once __DIR__ . '/../models/User.php';

class UsersController
{
    public function register()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Username, email, and password are required']);
            return;
        }
        $name = $data['username'];
        $email = $data['email'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user = new User();
        if ($user->create([
            'username' => $name,
            'email' => $email,
            'password' => $password
        ])) {
            http_response_code(201);
            echo json_encode(['message' => 'User registered successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to register user']);
        }
    }
    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['email'], $data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing credentials']);
            return;
        }

        $userModel = new User();
        $user = $userModel->findBy('email', $data['email']);

        if ($user && password_verify($data['password'], $user['password'])) {
            echo json_encode([
                'message' => 'Login successful',
                'user_id' => $user['id'],
                'username' => $user['username'],
                'is_admin' => (bool)$user['is_admin']
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
        }
    }
}
