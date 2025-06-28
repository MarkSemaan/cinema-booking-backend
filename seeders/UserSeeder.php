<?php

require_once __DIR__ . '/seeder.php';
require_once __DIR__ . '/../models/User.php';

class UserSeeder extends Seeder
{
    public function run(): void
    {
        echo "Seeding Users...\n";
        $userModel = new User();
        $hashed_password = password_hash('password123', PASSWORD_DEFAULT);

        $test_user_id = $userModel->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => $hashed_password,
            'is_admin' => 0 // Not an admin
        ]);
        $admin_user_id = $userModel->create([
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => $hashed_password,
            'is_admin' => 1 // Is an admin
        ]);
        echo "Created Test User ID: $test_user_id\n";
        echo "Created Admin User ID: $admin_user_id\n\n";
    }
}