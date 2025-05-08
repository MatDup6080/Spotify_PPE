<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "MusicDB");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Créer une playlist pour chaque utilisateur s'ils n'en ont pas déjà une
$resultUsers = $conn->query("SELECT id, surname FROM users");
if ($resultUsers->num_rows > 0) {
    while ($user = $resultUsers->fetch_assoc()) {
        $userId = $user['id'];
        $userSurname = $user['surname'];

        // Vérifier si l'utilisateur a déjà une playlist
        $resultPlaylist = $conn->query("SELECT id FROM playlists WHERE id_u = $userId");
        if ($resultPlaylist->num_rows === 0) {
            // Créer une nouvelle playlist pour l'utilisateur
            $playlistName = "Playlist de " . $userSurname;
            $stmt = $conn->prepare("INSERT INTO playlists (name_p, id_u, cover) VALUES (?, ?, ?)");
            $defaultCover = "default-cover.jpg";
            $stmt->bind_param("sis", $playlistName, $userId, $defaultCover);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Initialisation des variables
$musicTracks = [];
$playlistName = "Toutes les playlists";

// Vérifier si un ID de playlist est passé dans l'URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $playlistId = intval($_GET['id']);

    // Récupérer le nom de la playlist
    $resultPlaylist = $conn->query("SELECT name_p FROM playlists WHERE id = $playlistId");
    if ($resultPlaylist->num_rows > 0) {
        $playlistName = $resultPlaylist->fetch_assoc()['name_p'];
    }

    // Récupérer les musiques associées à cette playlist
    $result = $conn->query("
        SELECT 
            Songs.id AS song_id,
            Songs.title, 
            Songs.singer, 
            Songs.genre, 
            Songs.duration, 
            Songs.cover, 
            Songs.fic_audio, 
            Songs.plays, 
            COUNT(CASE WHEN likes_dislikes.statut = 0 THEN 1 END) AS likes_count,
            COUNT(CASE WHEN likes_dislikes.statut = 1 THEN 1 END) AS dislikes_count,
            COALESCE(likes_dislikes.statut, -1) AS statut
        FROM Songs
        LEFT JOIN likes_dislikes 
            ON Songs.id = likes_dislikes.id_s 
        JOIN Playlist_Songs ps ON Songs.id = ps.id_s
        WHERE ps.id_p = $playlistId
        GROUP BY Songs.id;
    ");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $musicTracks[] = [
                "id"      => $row["song_id"],
                "title"   => $row["title"] ?? "Titre inconnu",
                "artist"  => $row["singer"] ?? "Artiste inconnu",
                "genre"   => $row["genre"] ?? "Genre inconnu",
                "duration"=> $row["duration"] ?? "00:00",
                "cover"   => !empty($row["cover"]) ? $row["cover"] : "default-cover.jpg",
                "audio"   => !empty($row["fic_audio"]) ? $row["fic_audio"] : "",
                "plays"   => $row["plays"] ?? 0,
                "likes_count"   => $row["likes_count"] ?? 0,
                "dislikes_count"=> $row["dislikes_count"] ?? 0,
            ];
        }
    }
} else {
    // Si aucun ID n'est passé, afficher toutes les playlists
    $result = $conn->query("SELECT id, name_p, cover FROM playlists");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $musicTracks[] = [
                "id" => $row["id"],
                "playlist_name" => $row["name_p"] ?? "Nom inconnu",
                "cover" => $row["cover"] ?? "default-cover.jpg"
            ];
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playlists</title>
    <link rel="stylesheet" href="styles_home.css">
</head>
<body>

<nav class="vertical-menu">
        
        <ul>
            <li>
                <a href="connexion.html" class="menu1">
                    <img src="images/connexion.png" alt="Page de connexion de CGDMusic">
                    <span>Connexion</span>
                </a>
                
            </li>
            <li>
                <a href="profil.php" class="menu1">
                    <img src="images/profil.png" alt="Page de connexion de CGDMusic">
                    <span>Profil</span>
                </a>
                
            </li>
            <li>
                <a href="index.html" class="menu2">
                    <img src="images/accueil.png" alt="Page d'accueil de CGDMusic">
                    <span>Accueil</span>
                </a>
                
            </li>
            <li>
                <a href="rechercher_musique.php" class="menu2">
                    <img src="images/musique.png" alt="Page d'accueil de CGDMusic">
                    <span>Bibliothéque Musique</span>
                </a>
                
            </li>
            
            <li>
                <a href="formulaire_abonnemnt.php" class="menu4">
                    <img src="images/paiement-securise.png" alt="Page de téléchargement de CGDMusic">
                    <span>Abonnement</span>
                </a>
                
            </li>
            <li>
                <a href="logout.php" class="menu5">
                    <img src="images/se-deconnecter.png" alt="Déconnexion">
                    <span>Déconnexion</span>
                </a>
            </li>
        </ul>
    </nav>

<h2><?= htmlspecialchars($playlistName) ?></h2>
<div class="container">
    
    <?php if (!empty($musicTracks)): ?>
        <?php foreach ($musicTracks as $index => $track): ?>
            <div class="card">
                <?php if (isset($_GET['id']) && is_numeric($_GET['id'])): ?>
                    <!-- Affichage des musiques d'une playlist -->
                    <h2><?= htmlspecialchars($track['title']) ?></h2>
                    <p>Artiste : <?= htmlspecialchars($track['artist']) ?></p>
                    <p>Genre : <?= htmlspecialchars($track['genre']) ?></p>
                    <p>Durée : <?= htmlspecialchars($track['duration']) ?></p>
                    <img src="<?= htmlspecialchars($track['cover']) ?>" alt="Cover de la musique">
                    
                    <!-- Bouton Play -->
                    <button onclick="playMusic('<?= htmlspecialchars($track['audio']) ?>', '<?= htmlspecialchars($track['title']) ?>', <?= $index ?>)">▶️ Play</button>
                    <!-- Bouton de suppression -->
        <button class="delete-song-btn" onclick="deleteSongFromPlaylist(<?= htmlspecialchars($track['id']) ?>, <?= $playlistId ?>)">🗑️ Supprimer</button>
                    <!-- Boutons Like/Dislike -->
                    <div class="like-dislike-icons">
                <i class="fa fa-thumbs-up <?= isset($track['statut']) && $track['statut'] === 0 ? 'active' : '' ?>" 
                onclick="likeSong('<?= htmlspecialchars($track['id']) ?>')">👍</i>
                <span id="like-count-<?= htmlspecialchars($track['id']) ?>"><?= htmlspecialchars($track['likes_count'] ?? 0) ?></span>
                <i class="fa fa-thumbs-down <?= isset($track['statut']) && $track['statut'] === 1 ? 'active' : '' ?>" 
                onclick="dislikeSong('<?= htmlspecialchars($track['id']) ?>')">👎</i>
                <span id="dislike-count-<?= htmlspecialchars($track['id']) ?>"><?= htmlspecialchars($track['dislikes_count'] ?? 0) ?></span>
            </div>
                <?php else: ?>
                    <!-- Affichage des playlists -->
                    <h2><?= htmlspecialchars($track['playlist_name']) ?></h2>
                    
                    <a href="playlist.php?id=<?= $track['id'] ?>" class="playlist-link">Voir la playlist</a>
                    
                    <!-- Bouton de suppression -->
                    <button class="delete-playlist-btn" onclick="deletePlaylist(<?= $track['id'] ?>)">🗑️ Supprimer</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun contenu disponible.</p>
    <?php endif; ?>
</div>

<?php if (isset($_GET['id']) && is_numeric($_GET['id'])): ?>
<footer class="footer">
    <div class="player">
        <p class="current-song" id="current-song">Sélectionnez une musique</p>
        <audio id="audioPlayer" controls>
            <source id="audioSource" src="" type="audio/mp3">
            Votre navigateur ne supporte pas l'audio.
        </audio>
        <div class="player-controls">
            <button onclick="playPreviousTrack()">⏮️</button>
            <button onclick="playNextTrack()">⏭️</button>
        </div>
    </div>
</footer>
<?php endif; ?>

<script>
    const userAbonnement = "<?php echo $abonnement; ?>";
    const utilisateurId = "<?php echo $utilisateurId; ?>";
</script>
<script src="scripts_site.js"></script>

</body>
</html>