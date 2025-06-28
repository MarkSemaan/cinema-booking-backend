<?php

require_once __DIR__ . '/seeder.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Showtime.php';
require_once __DIR__ . '/../models/User.php';

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        echo "Seeding Bookings...\n";
        $bookingModel = new Booking();
        $showtimeModel = new Showtime();
        $userModel = new User();

        // Assuming showtime and user data already exists from previous seeders
        $showtime = $showtimeModel->findAll()[0]; // Get the first showtime
        $test_user = $userModel->findAll()[0]; // Get the first user (testuser)

        if ($showtime && $test_user) {
            $booking_id = $bookingModel->create([
                'user_id' => $test_user['id'],
                'showtime_id' => $showtime['id']
            ]);

            if ($booking_id) {
                echo "Created Booking ID: $booking_id\n";
            } else {
                echo "Failed to create booking.\n";
            }
        } else {
            echo "Could not find showtime or test user to create booking.\n";
        }
    }
}