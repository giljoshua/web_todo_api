<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!isset($data->title) || empty($data->title)) {
        http_response_code(400);
        echo json_encode(["error" => "Title is required"]);
        exit;
    }
    
    $title = $data->title;
    $description = isset($data->description) ? $data->description : "";
    $completed = isset($data->completed) ? $data->completed : false;
    
    try {
        $stmt = $conn->prepare("INSERT INTO todos (title, description, completed) VALUES (:title, :description, :completed)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':completed', $completed, PDO::PARAM_BOOL);
        $stmt->execute();
        
        $id = $conn->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            "id" => $id,
            "title" => $title,
            "description" => $description,
            "completed" => $completed,
            "message" => "Todo created successfully"
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
