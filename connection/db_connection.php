<?php
class DBConnection
{
    private $host = 'localhost';
    private $db_name = 'cinema_booking_db';
    private $username = 'root';
    private $password = 'root';
    private $mysqli;

    private static $instance = null;

    // Private constructor to prevent direct instantiation
    private function __construct()
    {
        $this->mysqli = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        // Check connection
        if ($this->mysqli->connect_error) {
            die("Connection failed: " . $this->mysqli->connect_error);
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DBConnection();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->mysqli;
    }
}
