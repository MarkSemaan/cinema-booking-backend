<?php
require_once __DIR__ . '/../connection/db_connection.php';


class booking_migration extends migrate
{
    public function run(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS bookings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            showtime_id INT NOT NULL,
            booking_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE
        )";

        if ($this->mysqli->query($sql) === TRUE) {
            echo "Bookings table created successfully\n";
        } else {
            echo "Error creating bookings table: " . $this->mysqli->error . "\n";
        }
    }
}
