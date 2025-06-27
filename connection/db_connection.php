<?php

class DBConnection
{
    private $host = 'localhost';
    private $db_name = 'cinema_booking_db';
    private $username = 'root';
    private $password = '';
    private $mysqli;

    // Get the database connection
    public function __construct()
    {
        $this->mysqli = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        // Check connection
        if ($this->mysqli->connect_error) {
            die("Connection failed: " . $this->mysqli->connect_error);
        }
    }

    private function __clone()
    {
        // Prevent cloning of the instance
    }

    private function __wakeup()
    {
        // Prevent unserializing of the instance
    }

    public static function getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new DBConnection();
        }
        return $instance->mysqli;
    }
}
