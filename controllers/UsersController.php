<?php

require_once __DIR__ . '/../models/User.php';

class UsersController
{
    public function register()
    {
        //Get the data from the request
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Username, email, and password are required']);
            return;
        }
        //Create the user
        $name = $data['username'];
        $email = $data['email'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        //Create the user model
        $user = new User();
        //Create the user
        if ($user->create([
            'username' => $name,
            'email' => $email,
            'password' => $password
        ])) {
            //If the user is created successfully, return a success message
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'User registered successfully'
            ]);
        } else {
            //If the user is not created successfully, return an error message
            http_response_code(500);
            echo json_encode(['error' => 'Failed to register user']);
        }
    }
    public function login()
    {
        //Get the data from the request
        $data = json_decode(file_get_contents('php://input'), true);
        //Check if the email and password are set
        if (!isset($data['email'], $data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing credentials']);
            return;
        }

        //Create the user model
        $userModel = new User();
        //Find the user by email
        $user = $userModel->findBy('email', $data['email']);
        //Check if the user exists and the password is correct
        if ($user && password_verify($data['password'], $user['password'])) {
            //If the user exists and the password is correct, return a success message
            http_response_code(200);
            echo json_encode([
                'message' => 'Login successful',
                'user_id' => $user['id'],
                'username' => $user['username'],
                'is_admin' => (bool)$user['is_admin']
            ]);
            return;
        } else {
            //If the user does not exist or the password is incorrect, return an error message
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
        }
    }
}
