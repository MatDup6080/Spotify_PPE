<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiches Musique</title>
    <link rel="stylesheet" href="styles_spotify.css">
    <style>
        .like-dislike-icons {
            margin-top: 10px;
            font-size: 20px;
        }
        .like-dislike-icons i {
            cursor: pointer;
            margin: 0 10px;
        }
        .like-dislike-icons span {
            margin-left: 5px;
            font-size: 16px;
        }
        .play-icon {
            font-size: 30px;
            color: green;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<!-- Menu de navigation -->
<nav class="menu">
    <ul>
        <li><a href="#">Accueil</a></li>
        <li><a href="playlist.php">Playlist</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</nav>

<div class="container">
    <?php
    // Connexion à la base de données
    $conn = new mysqli("localhost", "root", "", "MusicDB");
    if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
    }
    
    $musicTracks = [];
    $result = $conn->query("SELECT title, singer, genre, duration, cover, fic_audio, likes, dislikes FROM Songs");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $musicTracks[] = [
                "title"  => $row["title"] ?? "Titre inconnu",
                "artist" => $row["singer"] ?? "Artiste inconnu",
                "genre"  => $row["genre"] ?? "Genre inconnu",
                "duration" => $row["duration"] ?? "00:00",
                "cover"  => !empty($row["cover"]) ? $row["cover"] : "default-cover.jpg",
                "audio"  => !empty($row["fic_audio"]) ? $row["fic_audio"] : "", 
                "likes"  => $row["likes"] ?? 0, 
                "dislikes" => $row["dislikes"] ?? 0
            ];
        }
    }
    $conn->close();
    ?>

    <?php if (!empty($musicTracks)): ?>
        <?php foreach ($musicTracks as $index => $track): ?>
            <div class="card">
                <img src="<?= htmlspecialchars($track['cover']) ?>" alt="<?= htmlspecialchars($track['title']) ?>" class="cover">
                <h2><?= htmlspecialchars($track['title']) ?></h2>
                <p>Artiste : <?= htmlspecialchars($track['artist']) ?></p>
                <p>Genre : <?= htmlspecialchars($track['genre']) ?></p>
                <p>Durée : <?= htmlspecialchars($track['duration']) ?></p>

                <!-- Icône Play avec l'index de la chanson -->
                <i class="fa fa-play play-icon" onclick="playMusic('<?= htmlspecialchars($track['audio']) ?>', '<?= htmlspecialchars($track['title']) ?>', <?= $index ?>)"></i>

                <!-- Menu déroulant -->
                <span class="options-btn" onclick="showMenu(event, '<?= htmlspecialchars($track['title']) ?>')">☰</span>
                <div class="menu-options" id="menu-<?= htmlspecialchars($track['title']) ?>">
                    <a href="#" onclick="addToPlaylist('<?= htmlspecialchars($track['title']) ?>')">Ajouter à la playlist</a>
                </div>

                <!-- Like/Dislike -->
                <div class="like-dislike-icons">
                    <i class="fa fa-thumbs-up" onclick="likeSong('<?= htmlspecialchars($track['title']) ?>')">👍</i>
                    <span id="like-count-<?= htmlspecialchars($track['title']) ?>"><?= $track['likes'] ?></span>

                    <i class="fa fa-thumbs-down" onclick="dislikeSong('<?= htmlspecialchars($track['title']) ?>')">👎</i>
                    <span id="dislike-count-<?= htmlspecialchars($track['title']) ?>"><?= $track['dislikes'] ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucune musique disponible.</p>
    <?php endif; ?>
</div>

<!-- Zone du lecteur audio unique -->
<div class="player">
    <p id="current-song">Sélectionnez une musique</p>
    <audio id="audioPlayer" controls>
        <source id="audioSource" src="" type="audio/mp3">
        Votre navigateur ne supporte pas l'audio.
    </audio>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

<script>
// Tableau pour stocker toutes les pistes de musique
let currentTrackIndex = -1;
let musicTracks = []; // Tableau pour les pistes de musique

// Fonction pour lire la musique
function playMusic(src, title, index) {
    var audioPlayer = document.getElementById('audioPlayer');
    var audioSource = document.getElementById('audioSource');
    var currentSong = document.getElementById('current-song');

    // Charger la chanson
    audioSource.src = src;
    audioPlayer.load();
    audioPlayer.play();
    currentSong.textContent = "Lecture en cours : " + title;

    // Mettre à jour l'index de la chanson actuelle
    currentTrackIndex = index;

    // Ajouter la chanson au tableau global des pistes
    musicTracks.push({ src, title });

    // Ajouter un événement 'ended' pour jouer la chanson suivante automatiquement
    audioPlayer.onended = function () {
        playNextTrack();
    };
}

// Fonction pour jouer la chanson suivante
function playNextTrack() {
    if (currentTrackIndex + 1 < musicTracks.length) {
        // Jouer la musique suivante
        var nextTrack = musicTracks[currentTrackIndex + 1];
        playMusic(nextTrack.src, nextTrack.title, currentTrackIndex + 1);
    } else {
        alert("Fin de la playlist.");
    }
}

// Ajouter à la playlist
function addToPlaylist(title) {
    alert("La chanson " + title + " a été ajoutée à la playlist.");
}

// Fonction de like
function likeSong(title) {
    var likeCount = document.getElementById("like-count-" + title);
    var currentLikes = parseInt(likeCount.textContent);
    likeCount.textContent = currentLikes + 1;

    // Mettre à jour la base de données
    updateLikesDislikes(title, "like");
}

// Fonction de dislike
function dislikeSong(title) {
    var dislikeCount = document.getElementById("dislike-count-" + title);
    var currentDislikes = parseInt(dislikeCount.textContent);
    dislikeCount.textContent = currentDislikes + 1;

    // Mettre à jour la base de données
    updateLikesDislikes(title, "dislike");
}

// Fonction pour mettre à jour la base de données
function updateLikesDislikes(title, action) {
    fetch('update_likes_dislikes.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `title=${encodeURIComponent(title)}&action=${encodeURIComponent(action)}`
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
    })
    .catch(error => console.error('Erreur:', error));
}
</script>

</body>
</html>
