<?php
session_start();

require_once 'config.php';  // same folder as config.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $userType = $_POST['userType'];

    $table = $userType === 'admin' ? 'admin' : 'client';

    $sql = "SELECT * FROM `$table` WHERE email = :email LIMIT 1";
    $stmt = $connexion->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Since passwords are NOT hashed, compare plaintext
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $userType;
            $_SESSION['email'] = $user['email'];

            // Redirect to dashboard (adjust path as needed)
            header("Location: admin-dashboard.html");
            exit;
        } else {
            header("Location: login.html?error=invalid_password");
            exit;
        }
    } else {
        header("Location: login.html?error=user_not_found");
        exit;
    }
} else {
    header("Location: login.html?error=invalid_request");
    exit;
}
