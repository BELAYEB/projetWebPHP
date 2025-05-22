<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Use the PDO connection from config.php
    $pdo = $connexion;

    // Sanitize and get POST data
    $title = trim($_POST['requestTitle'] ?? '');
    $type = trim($_POST['requestType'] ?? '');
    $priority = trim($_POST['priority'] ?? '');
    $description = trim($_POST['requestDescription'] ?? '');

    if (!$title || !$type || !$priority || !$description) {
        // Missing required fields, redirect or handle error
        header("Location: error.html");
        exit;
    }

    try {
        $sql = "INSERT INTO request (request_title, request_type, request_priority, description)
                VALUES (:request_title, :request_type, :priority, :description)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':request_title', $title);
        $stmt->bindParam(':request_type', $type);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {


            exit;
        } else {


            exit;
        }
    } catch (PDOException $e) {

        header("Location: error.html");
        exit;
    }
} else {

    header("Location: error.html");
    exit;
}
?>