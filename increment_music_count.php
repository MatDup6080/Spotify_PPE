<?php
session_start();

if (!isset($_SESSION['utilisateur_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
    exit();
}

// Vérifiez si l'utilisateur a un abonnement "free"
if ($_SESSION['abonnement'] === 'free') {
    $_SESSION['musique_ecoutees'] = ($_SESSION['musique_ecoutees'] ?? 0) + 1;

    if ($_SESSION['musique_ecoutees'] > 15) {
        echo json_encode(['success' => false, 'message' => 'Limite atteinte.']);
        exit();
    }
}

// Si vous utilisez une base de données pour stocker les écoutes
if (isset($_POST['title'])) {
    $title = $_POST['title'];

    $conn = new mysqli("localhost", "root", "", "MusicDB");
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données.']);
        exit();
    }

    $stmt = $conn->prepare("UPDATE Songs SET plays = plays + 1 WHERE title = ?");
    $stmt->bind_param("s", $title);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour des écoutes.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Titre non fourni.']);
}