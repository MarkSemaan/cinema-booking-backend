<?php
require_once __DIR__ . '/../connection/db_connection.php';

class bookedseat_migration extends migrate
{
    public function run(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS booked_seats (
            id INT PRIMARY KEY AUTO_INCREMENT,
            booking_id INT NOT NULL,
            seat_row INT NOT NULL,
            seat_number VARCHAR(10) NOT NULL,
            FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
            UNIQUE KEY unique_seat_for_showtime (booking_id, seat_row, seat_number)
        )";

        if ($this->mysqli->query($sql) === TRUE) {
            echo "Booked seats table created successfully\n";
        } else {
            echo "Error creating booked seats table: " . $this->mysqli->error . "\n";
        }
    }
}
