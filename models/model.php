<?php

require_once __DIR__ . '/../connection/db_connection.php';

abstract class Model
{
    protected $mysqli;
    protected $table;
    protected $fillable = [];

    public function __construct()
    {
        $this->mysqli = DBConnection::getInstance()->getConnection();
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

        $types = $this->get_param_types($values);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            return $this->mysqli->insert_id;
        } else {
            throw new Exception("Error creating record: " . $stmt->error);
        }
    }

    public function update(int $id, array $data)
    {
        $filteredData = array_filter(
            $data,
            fn($key) => in_array($key, $this->fillable),
            ARRAY_FILTER_USE_KEY
        );

        $setClauses = [];
        $values = [];
        foreach ($filteredData as $column => $value) {
            $setClauses[] = "{$column} = ?";
            $values[] = $value;
        }

        if (empty($setClauses)) {
            return false; // No data to update
        }

        $setClause = implode(", ", $setClauses);
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
        $stmt = $this->mysqli->prepare($sql);

        $values[] = $id; // Add ID to the end for binding
        $types = $this->get_param_types($values);

        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        } else {
            throw new Exception("Error updating record: " . $stmt->error);
        }
    }

    public function findBy(string $column, $value)
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM {$this->table} WHERE {$column} = ?");
        $types = $this->get_param_types([$value]);
        $stmt->bind_param($types, $value);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    protected function get_param_types(array $values): string
    {
        $types = '';
        foreach ($values as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } elseif (is_string($value)) {
                $types .= 's';
            } else {
                $types .= 'b'; // Blob for other types
            }
        }
        return $types;
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
