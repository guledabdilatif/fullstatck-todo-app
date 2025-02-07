<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

include 'config.php';

// Handle OPTIONS request (for CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Fetch all tasks
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM tasks ORDER BY id DESC");
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit();
}

// Add a new task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!empty($data['title'])) {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, completed) VALUES (:title, 0)");
        $stmt->execute(['title' => $data['title']]);
        echo json_encode(["message" => "Task added successfully", "id" => $pdo->lastInsertId()]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Title is required"]);
    }
    exit();
}

// Delete a task
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);  // Read JSON from body
    if (!empty($data['id'])) {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $data['id']]);
        echo json_encode(["message" => "Task deleted"]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Task ID is required"]);
    }
    exit();
}

// Mark task as completed (Toggle completion)
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);  // Read JSON from body
    if (!empty($data['id'])) {
        $stmt = $pdo->prepare("UPDATE tasks SET completed = NOT completed WHERE id = :id");
        $stmt->execute(['id' => $data['id']]);
        echo json_encode(["message" => "Task status updated"]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Task ID is required"]);
    }
    exit();
}

// If no valid request method is matched
http_response_code(405);
echo json_encode(["error" => "Invalid request method"]);
?>
