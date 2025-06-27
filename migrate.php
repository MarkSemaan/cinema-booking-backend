<?php

//start migration
require_once __DIR__ . '/connection/db_connection.php';
require_once __DIR__ . '/migrations/000_create_cinema_booking_db.php';

// Create a temporary connection to create the database if it doesn't exist
$temp_mysqli = new mysqli('localhost', 'root', 'root');
if ($temp_mysqli->connect_error) {
    die("Connection failed: " . $temp_mysqli->connect_error);
}
createDB($temp_mysqli);
$temp_mysqli->close();

$mysqli = DBConnection::getInstance();

$migrationsPath = __DIR__ . '/migrations/';
$migrationFiles = glob($migrationsPath . '*.php');

sort($migrationFiles);

if (empty($migrationFiles)) {
    echo "No migration files found.\n";
    exit(0);
}

foreach ($migrationFiles as $file) {
    echo "  - Running " . basename($file) . "... ";
    require_once $file;
    if (basename($file) === '000_create_cinema_booking_db.php') {
        continue; // Skip the database creation file as it's handled separately
    }

    $baseName = basename($file, '.php');
    $parts = explode('_', $baseName);
    array_shift($parts); // Remove the numeric prefix (e.g., '000')
    $functionName = 'create' . implode('', array_map('ucfirst', array_slice($parts, 1)));
    if (function_exists($functionName)) {
        $functionName($mysqli->getConnection());
    } else {
        echo "Error: Migration function $functionName not found in " . basename($file) . "\n";
        exit(1);
    }
    echo "Done.\n";
}
