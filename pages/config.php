<?php
$serveur = "localhost";
$utilisateur = 'root';
$pass = "";
$dbname = "projet-web";

try {
    $connexion = new PDO("mysql:host=$serveur;dbname=$dbname", $utilisateur, $pass);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>