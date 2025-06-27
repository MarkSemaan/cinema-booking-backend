<?php
require_once '../connection/db_connection.php';
function createBookingsTable($mysqli)
{
    $sql = "CREATE TABLE IF NOT EXISTS bookings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        showtime_id INT NOT NULL,
        booking_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE
    )";

    if ($mysqli->query($sql) === TRUE) {
        echo "Bookings table created successfully\n";
    } else {
        echo "Error creating bookings table: " . $mysqli->error . "\n";
    }
}
