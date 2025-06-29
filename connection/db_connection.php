<?php
class DBConnection
{
    private $host = 'localhost';
    private $db_name = 'cinema_booking_db';
    private $username = 'root';
    private $password = 'root';
    private $mysqli;

    private static $instance = null;

    // Private constructor to prevent direct instantiation, only one instance of the class can be created
    private function __construct()
    {
        $this->mysqli = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        // Check connection
        if ($this->mysqli->connect_error) {
            die("Connection failed: " . $this->mysqli->connect_error);
        }
    }
    //Method to get the instance of the class
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DBConnection();
        }
        return self::$instance;
    }
    //Method to get the connection from the instance
    public function getConnection()
    {
        return $this->mysqli;
    }
}
