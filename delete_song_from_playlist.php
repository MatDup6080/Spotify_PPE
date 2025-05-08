<?php
session_start();

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "MusicDB");
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données.']));
}

// Vérifier si l'utilisateur est connecté
$utilisateurId = $_SESSION['utilisateur_id'] ?? null;
if (!$utilisateurId) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour effectuer cette action.']);
    exit();
}

// Récupérer les données POST
$songId = intval($_POST['song_id']);
$playlistId = intval($_POST['playlist_id']);

// Vérifier si la playlist appartient à l'utilisateur
$result = $conn->query("SELECT id_u FROM playlists WHERE id = $playlistId");
if ($result->num_rows > 0) {
    $playlistOwnerId = $result->fetch_assoc()['id_u'];

    if ($playlistOwnerId == $utilisateurId) {
        // Supprimer la musique de la playlist
        $stmt = $conn->prepare("DELETE FROM Playlist_Songs WHERE id_p = ? AND id_s = ?");
        $stmt->bind_param("ii", $playlistId, $songId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de la musique.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Vous ne pouvez pas modifier une playlist qui ne vous appartient pas.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Playlist introuvable.']);
}

$conn->close();
?>