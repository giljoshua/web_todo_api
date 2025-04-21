<?php

$host = 'localhost';
$dbname = 'todo_db'; 
$username = 'root';    
$password = '';        
$charset = 'utf8mb4';

echo "Database Setup Script\n";
echo "---------------------\n";

try {
    echo "Connecting to MySQL server ($host)... ";
    $pdo_server = new PDO("mysql:host=$host;charset=$charset", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    echo "Connected.\n";

    echo "Checking/Creating database '$dbname'... ";
    $pdo_server->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET $charset COLLATE utf8mb4_unicode_ci;");
    echo "Done.\n";

    $pdo_server = null;

    echo "Connecting to database '$dbname'... ";
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    echo "Connected.\n";

    echo "Creating 'todos' table if it doesn't exist... ";
    $conn->exec("CREATE TABLE IF NOT EXISTS `todos` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT NULL,
        `completed` BOOLEAN NOT NULL DEFAULT FALSE, -- Or TINYINT(1)
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=$charset;");
    echo "Done.\n";

    echo "Creating 'users' table if it doesn't exist... ";
    $conn->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `email` VARCHAR(255) NULL, 
        `password` VARCHAR(255) NOT NULL, 
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=$charset;");
    echo "Done.\n";

    echo "\nDatabase setup completed successfully!\n";

} catch (PDOException $e) {
    die("\nDatabase setup failed: " . $e->getMessage() . "\n");
}

?>
