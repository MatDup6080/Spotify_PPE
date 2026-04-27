<!-- filepath: c:\Users\Mathieu\Documents\BTS SIO Amiens\Bloc2\CGDMusic\add_to_playlist.php -->
<?php
header('Content-Type: application/json');

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "MusicDB");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données."]);
    exit;
}

// Récupération des données envoyées par la requête POST
$title = $_POST['title'] ?? '';
$playlist_id = $_POST['playlist_id'] ?? '';

if (empty($title) || empty($playlist_id)) {
    echo json_encode(["success" => false, "message" => "Titre ou playlist non fourni."]);
    exit;
}

// Récupérer l'ID de la musique à partir de son titre
$query = $conn->prepare("SELECT id FROM Songs WHERE title = ?");
$query->bind_param("s", $title);
$query->execute();
$query->bind_result($song_id);
$query->fetch();
$query->close();

if (empty($song_id)) {
    echo json_encode(["success" => false, "message" => "Musique introuvable."]);
    exit;
}

// Vérifier si la musique est déjà dans la playlist
$query = $conn->prepare("SELECT COUNT(*) FROM Playlist_songs WHERE id_p = ? AND id_s = ?");
$query->bind_param("ii", $playlist_id, $song_id);
$query->execute();
$query->bind_result($count);
$query->fetch();
$query->close();

if ($count > 0) {
    echo json_encode(["success" => false, "exists" => true, "message" => "La musique est déjà dans la playlist."]);
    exit;
}

// Ajouter la musique à la playlist
$query = $conn->prepare("INSERT INTO Playlist_songs (id_p, id_s) VALUES (?, ?)");
$query->bind_param("ii", $playlist_id, $song_id);
if ($query->execute()) {
    echo json_encode(["success" => true, "message" => "Musique ajoutée à la playlist."]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout à la playlist."]);
}
$query->close();
$conn->close();
?>