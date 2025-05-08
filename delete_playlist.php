<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "MusicDB");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données."]);
    exit;
}

$id = $_POST['id'] ?? '';
if (empty($id)) {
    echo json_encode(["success" => false, "message" => "ID de la playlist non fourni."]);
    exit;
}

// Supprimer la playlist
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