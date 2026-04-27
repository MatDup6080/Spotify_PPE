<?php
// Connexion à la base de données
require_once 'config.php';


// Vérification que le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupération des données du formulaire
    $title = $_POST['title'] ?? '';
    $artist = $_POST['artist'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $duration = $_POST['duration'] ?? '';

    // Gestion des fichiers (cover et audio)
    $cover = $_FILES['cover'] ?? null;
    $audio = $_FILES['audio'] ?? null;

    // Vérification des fichiers uploadés
    if ($cover && $audio) {
        // Définir la taille maximale autorisée (2 Go)
        $maxFileSize = 2 * 1024 * 1024 * 1024; // 2 Go

        // Vérifier la taille des fichiers
        if ($cover['size'] > $maxFileSize || $audio['size'] > $maxFileSize) {
            die("Les fichiers ne doivent pas dépasser 2 Go.");
        }

        // Définir les chemins de destination pour les fichiers
        $coverPath = 'images/' . basename($cover['name']);
        $audioPath = 'music/' . basename($audio['name']);

        // Vérifier si les fichiers ont été déplacés avec succès
        if (move_uploaded_file($cover['tmp_name'], $coverPath) && move_uploaded_file($audio['tmp_name'], $audioPath)) {
            // Préparer la requête SQL pour insérer les données
            $stmt = $conn->prepare("INSERT INTO Songs (title, singer, genre, duration, cover, fic_audio, likes, dislikes) VALUES (?, ?, ?, ?, ?, ?, 0, 0)");
            if (!$stmt) {
                die("Erreur de préparation de la requête : " . $conn->error);
            }
            $stmt->bind_param("ssssss", $title, $artist, $genre, $duration, $coverPath, $audioPath);

            // Exécuter la requête
            if ($stmt->execute()) {
                echo "Musique ajoutée avec succès.";
            } else {
                echo "Erreur lors de l'insertion : " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Erreur lors du déplacement des fichiers.";
        }
    } else {
        echo "Les fichiers cover et audio sont requis.";
    }
}

// Fermer la connexion à la base de données
header("Location: rechercher_musique.php");
exit;
$conn->close();
?>