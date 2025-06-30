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
        //Only allow fillable columns
        $filteredData = array_filter(
            $data,
            fn($key) => in_array($key, $this->fillable),
            ARRAY_FILTER_USE_KEY
        );
        //Prepare the arrays for updating the data
        $setClauses = [];
        $values = [];
        foreach ($filteredData as $column => $value) {
            $setClauses[] = "{$column} = ?";
            $values[] = $value;
        }
        //Check if there is data to update
        if (empty($setClauses)) {
            return false; // No data to update
        }
        //Prepare the statement,SET column $setClause and update the values
        $setClause = implode(", ", $setClauses);
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
        $stmt = $this->mysqli->prepare($sql);
        //Add the id to the end for binding
        $values[] = $id;
        //Get the types of the values
        $types = $this->get_param_types($values);
        //Bind the values to the statement
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
                case is_int($values):
                    $types .= 'i'; // Integer
                    break;
                case is_float($values):
                    $types .= 'd'; // Float
                    break;
                case is_string($values):
                    $types .= 's'; // String
                    break;
                case is_bool($values):
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
