<?php
require_once '../db.php'; // Corrected path

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Check if ID is provided in the URL
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        // If not in URL, check request body
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->id)) {
            $id = $data->id;
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Todo ID is required"]);
            exit;
        }
    }
    
    try {
        // First check if todo exists
        $checkStmt = $conn->prepare("SELECT * FROM todos WHERE id = :id");
        $checkStmt->bindParam(':id', $id);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(["error" => "Todo not found"]);
            exit;
        }
        
        $stmt = $conn->prepare("DELETE FROM todos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        echo json_encode([
            "message" => "Todo deleted successfully",
            "id" => $id
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
