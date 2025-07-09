<?php
require_once __DIR__ . '/../connection/db_connection.php';


class auditorium_migration extends migrate
{
    public function run(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS auditoriums (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        seats_rows INT(11) NOT NULL DEFAULT 0,
        seats_per_row INT(11) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

        if ($this->mysqli->query($sql) === TRUE) {
            echo "Auditoriums table created successfully\n";
        } else {
            echo "Error creating auditoriums table: " . $this->mysqli->error . "\n";
        }
    }
}
