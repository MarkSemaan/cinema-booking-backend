<?php
require_once __DIR__ . '/../connection/db_connection.php';


class movie_migration extends migrate
{
    public function run(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS movies (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        director VARCHAR(100) NOT NULL,
        release_year INT(4) NOT NULL,
        genre VARCHAR(50),
        synopsis TEXT,
        poster_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
        if ($this->mysqli->query($sql) === TRUE) {
            echo "Movies table created successfully\n";
        } else {
            echo "Error creating movies table: " . $this->mysqli->error . "\n";
        }
    }
}
