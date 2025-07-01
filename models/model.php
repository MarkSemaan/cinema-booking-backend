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
        //Only allow fillable columns
        $filteredData = array_filter(
            $data,
            fn($key) => in_array($key, $this->fillable),
            ARRAY_FILTER_USE_KEY
        );
        //Create the columns and placeholders
        $columns = implode(", ", array_keys($filteredData));
        $placeholders = implode(", ", array_fill(0, count($filteredData), '?'));
        //Prepare the statement and place the placeholders into the query
        $stmt = $this->mysqli->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        //Get the values from the filtered data
        $values = array_values($filteredData);
        //Get the types of the values then bind them
        $types = $this->get_param_types($values);
        $stmt->bind_param($types, ...$values);
        //If the statement is executed successfully, return the id of the inserted row
        if ($stmt->execute()) {
            return $this->mysqli->insert_id;
        } else {
            throw new Exception("Error creating record: " . $stmt->error);
        }
    }

    public function update(int $id, array $data)
    {
        // Only update columns that are allowed to be updated
        $allowed_data = [];
        foreach ($data as $column => $value) {
            if (in_array($column, $this->fillable)) {
                $allowed_data[$column] = $value;
            }
        }

        // Check if we have any data to update
        if (empty($allowed_data)) {
            return false; // Nothing to update
        }

        // Build the SQL query parts
        $set_parts = [];
        $values = [];

        foreach ($allowed_data as $column => $value) {
            $set_parts[] = "{$column} = ?";
            $values[] = $value;
        }

        // Create the full SQL query
        $set_clause = implode(", ", $set_parts);
        $sql = "UPDATE {$this->table} SET {$set_clause} WHERE id = ?";

        // Prepare the statement
        $stmt = $this->mysqli->prepare($sql);

        // Add the ID to the values array for the WHERE clause
        $values[] = $id;

        // Figure out what types our values are (string, int, etc.)
        $types = $this->get_param_types($values);

        // Bind all the values to the statement
        $stmt->bind_param($types, ...$values);

        // Try to execute the update
        if ($stmt->execute()) {
            // Check if any rows were actually updated
            return $stmt->affected_rows > 0;
        } else {
            // Something went wrong
            throw new Exception("Failed to update record: " . $stmt->error);
        }
    }

    public function findBy(string $column, $value)
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM {$this->table} WHERE {$column} = ?");
        $types = $this->get_param_types([$value]);
        $stmt->bind_param($types, $value);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Return single record, not array of records
    }

    public function findAllBy(string $column, $value)
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM {$this->table} WHERE {$column} = ?");
        $types = $this->get_param_types([$value]);
        $stmt->bind_param($types, $value);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC); // Return all records as an associative array
    }

    protected function get_param_types(array $values): string
    {
        //Get the types of the values for dynamic binding
        $types = '';
        foreach ($values as $value) {
            switch (true) {
                case is_int($value):
                    $types .= 'i'; // Integer
                    break;
                case is_float($value):
                    $types .= 'd'; // Float
                    break;
                case is_string($value):
                    $types .= 's'; // String
                    break;
                case is_bool($value):
                    $types .= 'b'; // Boolean
                    break;
                default:
                    $types .= 's'; // Default to string for any other type
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
