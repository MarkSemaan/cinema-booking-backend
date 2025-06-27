<?php
require_once __DIR__ . '/../connection/db_connection.php';
function createUsersTable($mysqli)
{
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        is_admin BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($mysqli->query($sql) === TRUE) {
        echo "Users table created successfully\n";
    } else {
        echo "Error creating users table: " . $mysqli->error . "\n";
    }
}
