<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = $connexion;

    $title = trim($_POST['requestTitle'] ?? '');
    $type = trim($_POST['requestType'] ?? '');
    $priority = trim($_POST['priority'] ?? '');
    $description = trim($_POST['requestDescription'] ?? '');

    if (!$title || !$type || !$priority || !$description) {
        header("Location: error.html?error=missing_fields");
        exit;
    }

    try {
        $sql = "INSERT INTO request (title, type, priority, description, client_id)
                VALUES (:request_title, :request_type, :priority, :description, :client_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':request_title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':request_type', $type, PDO::PARAM_STR);
        $stmt->bindParam(':priority', $priority, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':client_id', 2, PDO::PARAM_INT); // Fixed client_id

        if ($stmt->execute()) {
            header("Location: request-service.html");
            exit;
        } else {
            header("Location: error.html?error=db_failed");
            exit;
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        header("Location: error.html?error=exception");
        exit;
    }
} else {
    header("Location: error.html?error=invalid_method");
    exit;
}
?>