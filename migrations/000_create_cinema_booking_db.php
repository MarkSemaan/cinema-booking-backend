<?php
require_once __DIR__ . '/../connection/db_connection.php';

function createDB($mysqli)
{
    $mysqli->query("DROP DATABASE IF EXISTS cinema_booking_db");
    $sql = "CREATE DATABASE IF NOT EXISTS cinema_booking_db";
    if ($mysqli->query($sql) === TRUE) {
        echo "Database created successfully\n";
    } else {
        echo "Error creating database: " . $mysqli->error . "\n";
    }
}
