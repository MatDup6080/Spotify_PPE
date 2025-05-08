<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothéque de Musiques</title>
    <link rel="stylesheet" href="styles_home.css">
</head>
<body>

<!--bouton permettant d'accéder au formulaire--> d'ajout*/
<div class="add-music-container">
    <a href="ajout_musique.php" class="add-music-btn">➕ Ajouter une musique</a>
</div>

<nav class="menu">
        <ul>
            <li>
                <a href="accueil.html" class="menu1">
                    <img src="images/accueil.png" alt="Page d'accueil de CGDMusic">
                </a>
            </li>
            <li>
                <a href="playlist.php" class="menu2">
                    <img src="images/playlist.png" alt="Page de playlists de CGDMusic">
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
	
	// tableau contenant les valeurs des différentes variables
    $musicTracks = []; 
	
	
	// requête SQL qui récupére les données stockées dans la base de données musicdb dans la table songs. Une fois la requêt exécutée, si ils existent des données , elles seront affichées à l'écran.
	
    $result = $conn->query("SELECT title, singer, genre, duration, cover, fic_audio,likes,dislikes FROM Songs");
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

    <?php foreach ($musicTracks as $index => $track): ?>
        <div class="card">
            <img src="<?= htmlspecialchars($track['cover']) ?>" alt="<?= htmlspecialchars($track['title']) ?>" class="cover">
            <h2><?= htmlspecialchars($track['title']) ?></h2>
            <p>Artiste : <?= htmlspecialchars($track['artist']) ?></p>
            <p>Genre : <?= htmlspecialchars($track['genre']) ?></p>
            <p>Durée : <?= htmlspecialchars($track['duration']) ?></p>
            <button onclick="playMusic('<?= htmlspecialchars($track['audio']) ?>', '<?= htmlspecialchars($track['title']) ?>', <?= $index ?>)">▶️</button>
       
	<!-- boutons Like/Dislike -->
                <div class="like-dislike-icons">
                    <i class="fa fa-thumbs-up" onclick="likeSong('<?= htmlspecialchars($track['title']) ?>')">👍</i>
                    <span id="like-count-<?= htmlspecialchars($track['title']) ?>"><?= $track['likes'] ?></span>

                    <i class="fa fa-thumbs-down" onclick="dislikeSong('<?= htmlspecialchars($track['title']) ?>')">👎</i>
                    <span id="dislike-count-<?= htmlspecialchars($track['title']) ?>"><?= $track['dislikes'] ?></span>
                </div>
			 </div>
    <?php endforeach; ?>
</div>

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
	
	


<script>
let currentTrackIndex = -1;
let musicTracks = [];

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".card").forEach((card, index) => {
        let title = card.querySelector("h2").textContent;
        let src = card.querySelector("button").getAttribute("onclick").match(/'([^']+)'/)[1];
        musicTracks.push({ src, title });
    });
});

function playMusic(src, title, index) {
    var audioPlayer = document.getElementById('audioPlayer');
    var audioSource = document.getElementById('audioSource');
    var currentSong = document.getElementById('current-song');
    
    audioSource.src = src;
    audioPlayer.load();
    audioPlayer.play();
    currentSong.textContent = "Lecture en cours : " + title;
    currentTrackIndex = index;
    
    audioPlayer.onended = function () {
        playNextTrack();
    };
}

function playNextTrack() {
    if (currentTrackIndex + 1 < musicTracks.length) {
        currentTrackIndex++;
        let nextTrack = musicTracks[currentTrackIndex];
        playMusic(nextTrack.src, nextTrack.title, currentTrackIndex);
    } else {
        alert("Fin de la bibliothéque.");
    }
}

function playPreviousTrack() {
    if (currentTrackIndex > 0) {
        currentTrackIndex--;
        let prevTrack = musicTracks[currentTrackIndex];
        playMusic(prevTrack.src, prevTrack.title, currentTrackIndex);
    } else {
        alert("Début de la bibliothéque.");
    }
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
