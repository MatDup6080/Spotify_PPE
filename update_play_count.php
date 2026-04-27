<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';

    if (!empty($title)) {
        $conn = new mysqli("localhost", "root", "", "MusicDB");
        if ($conn->connect_error) {
            die(json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données.']));
        }

        // Mettre à jour le compteur d'écoutes
        $stmt = $conn->prepare("UPDATE Songs SET plays = plays + 1 WHERE title = ?");
        $stmt->bind_param("s", $title);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour.']);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Titre manquant.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
}
?>