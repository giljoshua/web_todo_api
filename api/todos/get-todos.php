<?php
require_once '../db.php';  // Make sure this path is correct

if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    try {
        // Check if specific todo ID is requested
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM todos WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $todo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($todo) {
                // Cast completed to boolean before sending
                $todo['completed'] = (bool)$todo['completed'];
                echo json_encode($todo);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Todo not found"]);
            }
        } else {
            // Return all todos
            $stmt = $conn->prepare("SELECT * FROM todos ORDER BY id DESC");
            $stmt->execute();
            $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Cast completed to boolean for all todos
            foreach ($todos as &$todo) {
                $todo['completed'] = (bool)$todo['completed'];
            }
            unset($todo);
            
            echo json_encode($todos);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
?>
