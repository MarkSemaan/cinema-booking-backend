<?php

require_once __DIR__ . '/../connections/db_connection.php';

abstract class Model
{
    protected $mysqli;
    protected $table;
    protected $fillable = [];

    public function __construct()
    {
        $this->mysqli = DBConnection::getInstance();
    }

    public function findAll()
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function find(int $id)
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    public function create(array $data)
    {
        $filteredData = array_filter(
            $data,
            fn($key) => in_array($key, $this->fillable),
            ARRAY_FILTER_USE_KEY
        );
        $columns = implode(", ", array_keys($filteredData));
        $placeholders = implode(", ", array_fill(0, count($filteredData), '?'));

        $stmt = $this->mysqli->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");

        $values = array_values($filteredData);
        $types = str_repeat('s', count($values));

        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            return $this->mysqli->insert_id;
        } else {
            throw new Exception("Error creating record: " . $stmt->error);
        }
    }
    public function delete(int $id)
    {
        $stmt = $this->mysqli->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception("Error deleting record: " . $stmt->error);
        }
    }
}
