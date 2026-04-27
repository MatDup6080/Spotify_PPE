<?php
session_start();
require_once 'config.php'; // contient $pdo

$mail = $_POST['mail'];
$mdp = $_POST['mdp'];

// Requête pour récupérer id, mot de passe, surname, mail et abonnement de l'utilisateur
$sql = "SELECT u.id,u.name_u,u.mdp, u.surname, u.mail 
        FROM users u
        WHERE u.mail = :mail";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
$stmt->execute();

if ($stmt->rowCount() === 1) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (md5($mdp) === $row['mdp']) {
        // Stocke les infos dans la session
        $_SESSION['utilisateur_id'] = $row['id'];
        $_SESSION['name_u'] = $row['name_u'];
        $_SESSION['surname'] = $row['surname'];
        $_SESSION['mail'] = $row['mail'];
        $_SESSION['abonnement'] = $row['abonnement']; // Ajout de l'abonnement

        // Redirection vers la page d'accueil
        header('Location: rechercher_musique.php');
        exit();
    } else {
        echo "<script>alert('Mot de passe incorrect'); window.location.href='connexion.html';</script>";
    }
} else {
    echo "<script>alert('Adresse mail introuvable'); window.location.href='connexion.html';</script>";
    }
?>