<?php
require_once __DIR__ . '/../connection/db_connection.php';
function createShowtimesTable($mysqli)
{
    $sql = "CREATE TABLE IF NOT EXISTS showtimes (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        movie_id INT(11) NOT NULL,
        auditorium_id INT(11) NOT NULL,
        showtime DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (movie_id) REFERENCES movies(id),
        FOREIGN KEY (auditorium_id) REFERENCES auditoriums(id)
    )";

    if ($mysqli->query($sql) === TRUE) {
        echo "Showtimes table created successfully\n";
    } else {
        echo "Error creating showtimes table: " . $mysqli->error . "\n";
    }
}
