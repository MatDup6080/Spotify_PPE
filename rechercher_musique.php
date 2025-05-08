<?php

session_start();

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "musicdb");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupérer l'ID de l'utilisateur depuis la session
$utilisateurId = $_SESSION['utilisateur_id'] ?? null;

if ($utilisateurId) {
    // Requête pour récupérer le type d'abonnement
    $stmt = $conn->prepare("
        SELECT subscribe.name_s 
        FROM users
        INNER JOIN subscribe ON users.id_s = subscribe.id
        WHERE users.id = ?
    ");
    $stmt->bind_param("i", $utilisateurId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $abonnement = $row['name_s']; // Récupérer le type d'abonnement
    } else {
        $abonnement = "Inconnu";
    }

    $stmt->close();
} else {
    $abonnement = "Inconnu"; // Si l'utilisateur n'est pas connecté
}

// Gérer l'ajout de musique à une playlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_playlist') {
    $playlistId = intval($_POST['playlist_id']);
    $title = $_POST['title'];

    if (!$utilisateurId) {
        echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour effectuer cette action.']);
        exit();
    }

    // Vérifier si la playlist appartient à l'utilisateur connecté
    $result = $conn->query("SELECT id_u FROM playlists WHERE id = $playlistId");
    if ($result->num_rows > 0) {
        $playlistOwnerId = $result->fetch_assoc()['id_u'];

        if ($playlistOwnerId == $utilisateurId) {
            // Ajouter la musique à la playlist
            $stmt = $conn->prepare("
                INSERT INTO Playlist_Songs (id_p, id_s)
                SELECT ?, id FROM Songs WHERE title = ?
            ");
            $stmt->bind_param("is", $playlistId, $title);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de la musique.']);
            }

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Vous ne pouvez pas ajouter des musiques à une playlist qui ne vous appartient pas.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Playlist introuvable.']);
    }

    $conn->close();
    exit();
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque de Musiques</title>
    <link rel="stylesheet" href="styles_home.css">
</head>
<body>

<!-- Header contenant le bouton d'ajout et le moteur de recherche -->
<header class="header">
    
    <div class="add-music-container">
        <a href="ajout_musique.php" class="add-music-btn">➕ </a>
    </div>
    
    <div class="search-container">
        <input type="text" id="searchTitle" placeholder="Rechercher par titre..." onkeyup="advancedFilterMusic()">
        <input type="text" id="searchArtist" placeholder="Rechercher par artiste..." onkeyup="advancedFilterMusic()">
        <input type="text" id="searchGenre" placeholder="Rechercher par genre..." onkeyup="advancedFilterMusic()">
  
        <!-- Menu déroulant pour trier par likes -->
        <select id="sortByLikes" onchange="handleSortLikes()">
            <option value="">Trier par likes...</option>
            <option value="likes-desc">Nombre de likes (décroissant)</option>
            <option value="likes-asc">Nombre de likes (croissant)</option>
        </select>

        <!-- Menu déroulant pour trier par écoutes -->
        <select id="sortByPlays" onchange="handleSortPlays()">
            <option value="">Trier par écoutes...</option>
            <option value="plays-desc">Nombre d'écoutes (décroissant)</option>
            <option value="plays-asc">Nombre d'écoutes (croissant)</option>
        </select>
    </div>
</header>

<nav class="vertical-menu">
        
        <ul>
            <li>
                <a href="connexion.html" class="menu1">
                    <img src="images/connexion.png" alt="Page de connexion de CGDMusic">
                    <span>Connexion</span>
                </a>
                
            </li>
            <ul>
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
                <a href="playlist.php" class="menu3">
                    <img src="images/playlist.png" alt="Page de playlists de CGDMusic">
                    <span>Playlists</span>
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
<div class="container">
    <?php
    $conn = new mysqli("localhost", "root", "", "MusicDB");
    if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
    }

    // Récupérer les musiques
    $musicTracks = [];
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


    // Récupérer les playlists
    $playlists = [];
    $resultPlaylists = $conn->query("SELECT id, name_p FROM Playlists");
    if ($resultPlaylists->num_rows > 0) {
        while ($row = $resultPlaylists->fetch_assoc()) {
            $playlists[] = [
                "id" => $row["id"],
                "name_p" => $row["name_p"]
            ];
        }
    }

    $conn->close();
    ?>

    <?php foreach ($musicTracks as $index => $track): ?>
        <div class="card">
            <img src="<?= htmlspecialchars($track['cover']) ?>" alt="<?= htmlspecialchars($track['title']) ?>" class="cover">
            <h2><?= htmlspecialchars($track['title']) ?></h2>
            <p>Artiste : <?= htmlspecialchars($track['artist']) ?></p>
            <p>Genre : <?= htmlspecialchars($track['genre']) ?></p>
            <p>Durée : <?= htmlspecialchars($track['duration']) ?></p>
            <button onclick="playMusic('<?= htmlspecialchars($track['audio']) ?>', '<?= htmlspecialchars($track['title']) ?>', <?= $index ?>)">▶️</button>
            
            <!-- Menu déroulant pour sélectionner une playlist -->
            <select id="playlist-select-<?= $index ?>">
                <option value="">Sélectionnez une playlist</option>
                <?php foreach ($playlists as $playlist): ?>
                    <option value="<?= $playlist['id'] ?>"><?= htmlspecialchars($playlist['name_p']) ?></option>
                <?php endforeach; ?>
            </select>

            <!-- Bouton pour ajouter à la playlist -->
            <button onclick="addToPlaylist('<?= htmlspecialchars($track['title']) ?>', <?= $index ?>)">➕ Ajouter à la playlist</button>
            
            <p>Écoutes : <span class="play-count" id="play-count-<?= htmlspecialchars($track['title']) ?>"><?= $track['plays'] ?></span></p>
            <div class="like-dislike-icons">
                <i class="fa fa-thumbs-up <?= isset($track['statut']) && $track['statut'] === 0 ? 'active' : '' ?>" 
                onclick="likeSong('<?= htmlspecialchars($track['id']) ?>')">👍</i>
                <span id="like-count-<?= htmlspecialchars($track['id']) ?>"><?= htmlspecialchars($track['likes_count'] ?? 0) ?></span>
                <i class="fa fa-thumbs-down <?= isset($track['statut']) && $track['statut'] === 1 ? 'active' : '' ?>" 
                onclick="dislikeSong('<?= htmlspecialchars($track['id']) ?>')">👎</i>
                <span id="dislike-count-<?= htmlspecialchars($track['id']) ?>"><?= htmlspecialchars($track['dislikes_count'] ?? 0) ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Footer contenant le lecteur audio -->
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

<script>
    const userAbonnement = "<?php echo $abonnement; ?>";
    const utilisateurId = "<?php echo $utilisateurId; ?>";
</script>
<script src="scripts_site.js"></script>

</body>
</html>