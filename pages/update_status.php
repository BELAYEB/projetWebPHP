<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=support_system;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if (isset($_POST['complete']) && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("UPDATE task SET status = 'completed' WHERE id = ?");
    $stmt->execute([$id]);


    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>