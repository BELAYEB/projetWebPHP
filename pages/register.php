<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = $connexion;

    // Get and sanitize inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirmPassword'] ?? '');
    $role = "client";

    // Basic validation
    if (!$name || !$password || !$confirmPassword) {
        header("Location: error.html");
        exit;
    }

    if ($password !== $confirmPassword) {
        header("Location: error.html");
        exit;
    }

    try {
        $sql = "INSERT INTO users (name,email,password, role)
                VALUES (:name, :email, :password, :role)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            header("Location: login.html");
            exit;
        } else {
            header("Location: error.html");
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