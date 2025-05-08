<?php
$host = 'localhost'; // Ou l'adresse de votre serveur
$db = 'musicdb'; // Remplacez par le nom exact de votre BDD
$user = 'root'; // Votre utilisateur MySQL
$password = ''; // Votre mot de passe MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
