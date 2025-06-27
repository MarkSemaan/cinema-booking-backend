<?php
require_once '../connection/db_connection.php';

function createBookedSeatsTable($mysqli)
{
    $sql = "CREATE TABLE IF NOT EXISTS booked_seats (
        id INT PRIMARY KEY AUTO_INCREMENT,
        booking_id INT NOT NULL,
        seat_row INT NOT NULL,
        seat_number VARCHAR(10) NOT NULL,
        FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
        UNIQUE KEY unique_seat_for_showtime (booking_id, seat_row, seat_number)
    )";

    if ($mysqli->query($sql) === TRUE) {
        echo "Booked seats table created successfully\n";
    } else {
        echo "Error creating booked seats table: " . $mysqli->error . "\n";
    }
}
