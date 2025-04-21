<?php
// filepath: c:\xampp\htdocs\web_todo_api\migrations\setup_database.php

// Database configuration (Manually copy these from api/db.php or include it carefully)
// IMPORTANT: Including db.php directly might try to output JSON headers,
// so it's safer to redefine the connection variables here for this script.
$host = 'localhost';
$dbname = 'todo_db'; // <-- Make sure this matches your intended DB name
$username = 'root';    // <-- Replace with your database username
$password = '';        // <-- Replace with your database password
$charset = 'utf8mb4';

echo "Database Setup Script\n";
echo "---------------------\n";

try {
    // 1. Connect to MySQL server (without selecting the database)
    echo "Connecting to MySQL server ($host)... ";
    $pdo_server = new PDO("mysql:host=$host;charset=$charset", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    echo "Connected.\n";

    // 2. Create the database if it doesn't exist
    echo "Checking/Creating database '$dbname'... ";
    $pdo_server->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET $charset COLLATE utf8mb4_unicode_ci;");
    echo "Done.\n";

    // Close the server connection, we'll reconnect to the specific database now
    $pdo_server = null;

    // 3. Connect to the specific database
    echo "Connecting to database '$dbname'... ";
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    echo "Connected.\n";

    // 4. Create the 'todos' table
    echo "Creating 'todos' table if it doesn't exist... ";
    $conn->exec("CREATE TABLE IF NOT EXISTS `todos` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT NULL,
        `completed` BOOLEAN NOT NULL DEFAULT FALSE, -- Or TINYINT(1)
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=$charset;");
    echo "Done.\n";

    // 5. Create the 'users' table
    echo "Creating 'users' table if it doesn't exist... ";
    $conn->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `email` VARCHAR(255) NULL, -- Allow NULL email for simplicity, adjust if needed
        `password` VARCHAR(255) NOT NULL, -- Store hashed passwords
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=$charset;");
    echo "Done.\n";

    echo "\nDatabase setup completed successfully!\n";

} catch (PDOException $e) {
    die("\nDatabase setup failed: " . $e->getMessage() . "\n");
}

?>
