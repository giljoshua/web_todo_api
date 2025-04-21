<?php
require_once '../db.php'; // Corrected path

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->id) || empty($data->id)) {
        http_response_code(400);
        echo json_encode(["error" => "Todo ID is required"]);
        exit;
    }

    $id = $data->id;

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

        // Build update query dynamically based on provided fields
        $updateFields = [];
        $params = [':id' => $id];

        if (isset($data->title)) {
            $updateFields[] = "title = :title";
            $params[':title'] = $data->title;
        }

        if (isset($data->description)) {
            $updateFields[] = "description = :description";
            $params[':description'] = $data->description;
        }

        if (isset($data->completed)) {
            $updateFields[] = "completed = :completed";
            // Ensure boolean value is stored correctly (e.g., 0 or 1 for TINYINT)
            $params[':completed'] = filter_var($data->completed, FILTER_VALIDATE_BOOLEAN);
        }

        if (empty($updateFields)) {
            http_response_code(400);
            echo json_encode(["error" => "No fields to update"]);
            exit;
        }

        $updateQuery = "UPDATE todos SET " . implode(", ", $updateFields) . " WHERE id = :id";
        $stmt = $conn->prepare($updateQuery);
        $stmt->execute($params);

        // Fetch the updated todo
        $selectStmt = $conn->prepare("SELECT * FROM todos WHERE id = :id");
        $selectStmt->bindParam(':id', $id);
        $selectStmt->execute();
        $updatedTodo = $selectStmt->fetch(PDO::FETCH_ASSOC);
        // Cast completed to boolean before sending back
        if ($updatedTodo) {
            $updatedTodo['completed'] = (bool)$updatedTodo['completed'];
        }

        echo json_encode([
            "message" => "Todo updated successfully",
            "todo" => $updatedTodo
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
