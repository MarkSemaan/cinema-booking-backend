<?php

require_once __DIR__ . '/seeder.php';
require_once __DIR__ . '/../models/BookedSeat.php';
require_once __DIR__ . '/../models/Booking.php';

class BookedSeatSeeder extends Seeder
{
    public function run(): void
    {
        echo "Seeding Booked Seats...\n";
        $bookedSeatModel = new BookedSeat();
        $bookingModel = new Booking();

        // Assuming booking data already exists from previous seeders
        $booking = $bookingModel->findAll()[0]; // Get the first booking

        if ($booking) {
            $bookedSeatModel->create([
                'booking_id' => $booking['id'],
                'seat_row' => 1,
                'seat_number' => 'A1'
            ]);
            $bookedSeatModel->create([
                'booking_id' => $booking['id'],
                'seat_row' => 1,
                'seat_number' => 'A2'
            ]);
            echo "Booked seats for Booking ID " . $booking['id'] . ".\n";
        } else {
            echo "Could not find booking to create booked seats.\n";
        }
    }
}