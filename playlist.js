function deletePlaylist(id) {
    if (confirm("Êtes-vous sûr de vouloir supprimer cette playlist ?")) {
        fetch('delete_playlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id=${encodeURIComponent(id)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Supprime la carte de la playlist de l'écran
                const card = document.getElementById(`playlist-card-${id}`);
                if (card) card.remove();
                alert("Playlist supprimée avec succès.");
            } else {
                alert(`Erreur : ${data.message}`);
            }
        })
        .catch(error => console.error('Erreur:', error));
    }
}
function deleteSongFromPlaylist(songId, playlistId) {
    if (confirm("Êtes-vous sûr de vouloir supprimer cette musique de la playlist ?")) {
        fetch('delete_song_from_playlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `song_id=${encodeURIComponent(songId)}&playlist_id=${encodeURIComponent(playlistId)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Musique supprimée avec succès.");
                location.reload(); // Recharge la page pour mettre à jour la liste des musiques
            } else {
                alert(`Erreur : ${data.message}`);
            }
        })
        .catch(error => console.error('Erreur:', error));
    }
}

let currentTrackIndex = -1;
let musicTracks = [];

document.addEventListener("DOMContentLoaded", function () {
    // Remplir le tableau musicTracks avec les musiques affichées
    document.querySelectorAll(".card").forEach((card, index) => {
        const title = card.querySelector("h2").textContent;
        const audioBtn = card.querySelector("button[onclick^='playMusic']");
        if (audioBtn) {
            const src = audioBtn.getAttribute("onclick").match(/'([^']+)'/)[1];
            musicTracks.push({ src, title });
        }
    });
});

function playMusic(src, title, index) {
    var audioPlayer = document.getElementById('audioPlayer');
    var audioSource = document.getElementById('audioSource');
    var currentSong = document.getElementById('current-song');
    if (!audioPlayer || !audioSource || !currentSong) return;

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
        alert("Fin de la playlist.");
    }
}

function playPreviousTrack() {
    if (currentTrackIndex > 0) {
        currentTrackIndex--;
        let prevTrack = musicTracks[currentTrackIndex];
        playMusic(prevTrack.src, prevTrack.title, currentTrackIndex);
    } else {
        alert("Début de la playlist.");
    }
}