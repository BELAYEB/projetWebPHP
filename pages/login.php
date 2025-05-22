<?php
session_start();

require_once 'config.php';  // same folder as config.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // Requête préparée pour récupérer l'utilisateur par email
        $stmt = $connexion->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Vérification du mot de passe (supposé hashé avec password_hash)

            // if (password_verify($password, $user['password'])) {
            // Authentification réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Redirection vers la page d’accueil selon rôle
            // Redirection selon rôle
            if ($user['role'] === 'admin') {
                header('Location: admin-dashboard.html');
            } elseif ($user['role'] === 'client') {
                header('Location: client-dashboard.php');
            } else {
                // Par défaut pour 'member' ou autres rôles
                header('Location: member-dashboard.html');

            }
            exit;
            // } else {
            //  $error = "Mot de passe incorrect.";
            // }

           // if (password_verify($password, $user['password'])) {
                // Authentification réussie
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                // Redirection vers la page d’accueil selon rôle
                    // Redirection selon rôle
                if ($user['role'] === 'admin') {
                    header('Location: admin-dashboard.php');
                } elseif ($user['role'] === 'client') {
                    header('Location: client-dashboard.php');
                } else {
                    // Par défaut pour 'member' ou autres rôles
                    header('Location: member-dashboard.php');
                }
                //exit;
           // } else {
              //  $error = "Mot de passe incorrect.";
           // }
>>>>>>> 783dfd42c9b9f30e5cf2d16c7fb06d8793fe0d6c
        } else {
            $error = "Utilisateur non trouvé.";
        }
    }
}
