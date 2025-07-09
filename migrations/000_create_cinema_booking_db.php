<?php

class database_migration
{
    private $mysqli;

    public function __construct()
    {
        // Create temp connection to create database
        $this->mysqli = new mysqli('localhost', 'root', 'root');

        if ($this->mysqli->connect_error) {
            die("Connection failed: " . $this->mysqli->connect_error);
        }
    }

    public function run(): void
    {
        $this->mysqli->query("DROP DATABASE IF EXISTS cinema_booking_db");
        $sql = "CREATE DATABASE IF NOT EXISTS cinema_booking_db";
        if ($this->mysqli->query($sql) === TRUE) {
            echo "Database created successfully\n";
        } else {
            echo "Error creating database: " . $this->mysqli->error . "\n";
        }
    }
}
