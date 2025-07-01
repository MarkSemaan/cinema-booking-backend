<?php

// Set up cinema booking database


require_once __DIR__ . '/connection/db_connection.php';
require_once __DIR__ . '/migrations/000_create_cinema_booking_db.php';

echo "Starting migration...\n";

// Create the database if it doesn't exist
echo "Creating database...\n";

// Create a temporary connection to create the database
$temp_conn = new mysqli('localhost', 'root', 'root');

// Check if connection worked
if ($temp_conn->connect_error) {
    echo "Error: Could not connect to MySQL\n";
    echo "Error message: " . $temp_conn->connect_error . "\n";
    exit(1);
}

// Create the database
createDB($temp_conn);
$temp_conn->close();

echo "Database created successfully!\n";

// Get our main db connection
echo "Connecting to database...\n";
$db = DBConnection::getInstance();
echo "Connected!\n";

// Run all the migration files
echo "Running migrations...\n";

// Get all migration files from the migrations folder
$migrations_folder = __DIR__ . '/migrations/';
$migration_files = glob($migrations_folder . '*.php');

// Sort them so they run in the right order
sort($migration_files);

// Check if migration files are missing
if (empty($migration_files)) {
    echo "No migration files found!\n";
    exit(0);
}

// Run each migration file
foreach ($migration_files as $file) {
    $filename = basename($file);

    // Database created, skip the file
    if ($filename === '000_create_cinema_booking_db.php') {
        echo "Skipping database creation file...\n";
        continue;
    }

    echo "Running migration: " . $filename . "... ";

    // Include the migration file
    require_once $file;

    // Figure out what function to call based on the filename
    // 001_create_users_table.php -> createUsersTable
    $base_name = basename($file, '.php');  // Remove .php extension
    $parts = explode('_', $base_name);     // Split by underscore
    array_shift($parts);                   // Remove the number (001, 002, etc.)

    // Build the function name
    $function_name = 'create';
    for ($i = 1; $i < count($parts); $i++) {
        $function_name .= ucfirst($parts[$i]);
    }

    // Check if the function exists and run it
    if (function_exists($function_name)) {
        $function_name($db->getConnection());
        echo "Done!\n";
    } else {
        echo "Error: Could not find function " . $function_name . " in " . $filename . "\n";
        exit(1);
    }
}

echo "All migrations completed!\n";
