<?php

require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = $connexion;

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');


    $assigned_to = 3;


    $created_by = 1;


    if (!$title || !$description) {
        header("Location: error.html");
        exit;
    }

    try {
        $sql = "INSERT INTO task (title, description,  assigned_to, created_by, created_at)
                VALUES (:title, :description, 'todo', :assigned_to, :created_by, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':assigned_to', $assigned_to, PDO::PARAM_INT);
        $stmt->bindParam(':created_by', $created_by, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: success.html");
            exit;
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "Erreur SQL : " . implode(" | ", $errorInfo);
            exit;
        }
    } catch (PDOException $e) {
        echo "Erreur base de données : " . $e->getMessage();
        exit;
    }
} else {
    header("Location: error.html");
    exit;
}
