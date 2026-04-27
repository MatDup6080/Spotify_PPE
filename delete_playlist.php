<?php
header('Content-Type: application/json');
require_once 'config2.php';

$id = $_POST['id'] ?? '';
if (empty($id)) {
    echo json_encode(["success" => false, "message" => "ID de la playlist non fourni."]);
    exit;
}

// Supprimer d'abord les liens avec les musiques
$conn->query("DELETE FROM playlist_songs WHERE id_p = $id");

// Puis supprimer la playlist
$query = $conn->prepare("DELETE FROM playlists WHERE id = ?");
$query->bind_param("i", $id);
if ($query->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur lors de la suppression de la playlist."]);
}
$query->close();
$conn->close();
?>