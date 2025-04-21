<?php
require_once '../db.php'; // Corrected path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!isset($data->username) || empty($data->username) || !isset($data->password) || empty($data->password)) {
        http_response_code(400);
        echo json_encode(["error" => "Username and password are required"]);
        exit;
    }
    
    $username = $data->username;
    $password = password_hash($data->password, PASSWORD_DEFAULT); // Securely hash password
    $email = isset($data->email) ? $data->email : "";
    
    try {
        // Check if username already exists
        $checkStmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $checkStmt->bindParam(':username', $username);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() > 0) {
            http_response_code(409); // Conflict
            echo json_encode(["error" => "Username already exists"]);
            exit;
        }
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $id = $conn->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            "id" => $id,
            "username" => $username,
            "email" => $email,
            "message" => "User created successfully"
        ]);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
?>
