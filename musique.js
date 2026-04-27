

let currentTrackIndex = -1;
let musicTracks = [];

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".card").forEach((card, index) => {
        let title = card.querySelector("h2").textContent;
        let src = card.querySelector("button").getAttribute("onclick").match(/'([^']+)'/)[1];
        musicTracks.push({ src, title });
    });
});

// permet de jouer la musique
function playMusic(src, title, index) {
    if (userAbonnement === 'free' || !utilisateurId) {
        // Vérifiez si la musique a déjà été écoutée
        if (localStorage.getItem(`played-${title}`)) {
            alert("Vous avez déjà écouté cette musique. Veuillez vous connecter ou changer votre abonnement pour l'écouter à nouveau.");
            return;
        } else {
            localStorage.setItem(`played-${title}`, true); // Marquez la musique comme écoutée
        }
    }

    var audioPlayer = document.getElementById('audioPlayer');
    var audioSource = document.getElementById('audioSource');
    var currentSong = document.getElementById('current-song');
    
    audioSource.src = src;
    audioPlayer.load();
    audioPlayer.play();
    currentSong.textContent = "Lecture en cours : " + title;
    currentTrackIndex = index;
    
    incrementPlayCount(title);

    audioPlayer.onended = function () {
        playNextTrack();
    };
}

//Permet de lire la musique suivant jusqu'à la derniére musique de la bibliothéque.
function playNextTrack() {
    if (currentTrackIndex + 1 < musicTracks.length) {
        currentTrackIndex++;
        let nextTrack = musicTracks[currentTrackIndex];
        playMusic(nextTrack.src, nextTrack.title, currentTrackIndex);
    } else {
        alert("Fin de la bibliothéque.");
    }
}

//Permet de lire la musique précédente jusqu'à la premiére musique de la bibliothéque.
function playPreviousTrack() {
    if (currentTrackIndex > 0) {
        currentTrackIndex--;
        let prevTrack = musicTracks[currentTrackIndex];
        playMusic(prevTrack.src, prevTrack.title, currentTrackIndex);
    } else {
        alert("Début de la bibliothéque.");
    }
}

//Ajouter une musique dans une playlist
function addToPlaylist(title, index) {
    if (userAbonnement === 'free') {
        alert("Les utilisateurs avec un abonnement 'free' ne peuvent pas ajouter de musiques à une playlist. Veuillez changer votre abonnement.");
        return;
    }

    const playlistSelect = document.getElementById(`playlist-select-${index}`);
    const playlistId = playlistSelect.value;

    if (!playlistId) {
        alert("Veuillez sélectionner une playlist.");
        return;
    }

    fetch('', { // Requête envoyée au même fichier PHP
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=add_to_playlist&title=${encodeURIComponent(title)}&playlist_id=${encodeURIComponent(playlistId)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`La musique "${title}" a été ajoutée à votre playlist.`);
        } else {
            alert(`Erreur : ${data.message}`);
        }
    })
    .catch(error => console.error('Erreur:', error));
}

//Permet de filtrer les musiques selon les critéres de l'utilisateur.
function advancedFilterMusic() {
    const searchTitle = document.getElementById("searchTitle").value.toLowerCase();
    const searchArtist = document.getElementById("searchArtist").value.toLowerCase();
    const searchGenre = document.getElementById("searchGenre").value.toLowerCase();

    const cards = document.querySelectorAll(".card");

    cards.forEach(card => {
        const title = card.querySelector("h2").textContent.toLowerCase();
        const artist = card.querySelector("p:nth-of-type(1)").textContent.toLowerCase();
        const genre = card.querySelector("p:nth-of-type(2)").textContent.toLowerCase();

        // Vérifiez si la carte correspond aux critères de recherche
        if (
            (searchTitle === "" || title.includes(searchTitle)) &&
            (searchArtist === "" || artist.includes(searchArtist)) &&
            (searchGenre === "" || genre.includes(searchGenre))
        ) {
            card.style.display = "block"; // Affiche la carte si elle correspond
        } else {
            card.style.display = "none"; // Masque la carte si elle ne correspond pas
        }
    });
}

//
function likeSong(songId) {
    fetch('update_likes_dislikes.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id_s=${encodeURIComponent(songId)}&action=like`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const likeIcon = document.querySelector(`#like-count-${songId}`).previousElementSibling;
            const dislikeIcon = document.querySelector(`#dislike-count-${songId}`).previousElementSibling;

            // Activer l'icône de like et désactiver celle de dislike
            likeIcon.classList.add('active');
            dislikeIcon.classList.remove('active');

            // Mettre à jour les compteurs
            document.getElementById(`like-count-${songId}`).textContent = data.likes_count;
            document.getElementById(`dislike-count-${songId}`).textContent = data.dislikes_count;
        } else {
            alert(data.message); // Afficher un message d'erreur si l'action est invalide
        }
    })
    .catch(error => console.error('Erreur:', error));
}

function dislikeSong(songId) {
    fetch('update_likes_dislikes.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id_s=${encodeURIComponent(songId)}&action=dislike`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const dislikeIcon = document.querySelector(`#dislike-count-${songId}`).previousElementSibling;
            const likeIcon = document.querySelector(`#like-count-${songId}`).previousElementSibling;

            // Activer l'icône de dislike et désactiver celle de like
            dislikeIcon.classList.add('active');
            likeIcon.classList.remove('active');

            // Mettre à jour les compteurs
            document.getElementById(`like-count-${songId}`).textContent = data.likes_count;
            document.getElementById(`dislike-count-${songId}`).textContent = data.dislikes_count;
        } else {
            alert(data.message); // Afficher un message d'erreur si l'action est invalide
        }
    })
    .catch(error => console.error('Erreur:', error));
}

function incrementPlayCount(title) {
    fetch('update_play_count.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `title=${encodeURIComponent(title)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const playCount = document.getElementById(`play-count-${title}`);
            playCount.textContent = parseInt(playCount.textContent) + 1;
        } else {
            console.error('Erreur lors de la mise à jour du compteur d\'écoutes:', data.message);
        }
    })
    .catch(error => console.error('Erreur:', error));
}


function handleSortLikes() {
    const sortValue = document.getElementById("sortByLikes").value;
    const cards = Array.from(document.querySelectorAll(".card"));
    cards.sort((a, b) => {
        const likesA = parseInt(a.querySelector(".like-dislike-icons span").textContent) || 0;
        const likesB = parseInt(b.querySelector(".like-dislike-icons span").textContent) || 0;
        return sortValue === "likes-desc" ? likesB - likesA : likesA - likesB;
    });
    const container = document.querySelector(".container");
    cards.forEach(card => container.appendChild(card));
}

function handleSortPlays() {
    const sortValue = document.getElementById("sortByPlays").value;
    const cards = Array.from(document.querySelectorAll(".card"));
    cards.sort((a, b) => {
        const playsA = parseInt(a.querySelector(".play-count").textContent) || 0;
        const playsB = parseInt(b.querySelector(".play-count").textContent) || 0;
        return sortValue === "plays-desc" ? playsB - playsA : playsA - playsB;
    });
    const container = document.querySelector(".container");
    cards.forEach(card => container.appendChild(card));
}





