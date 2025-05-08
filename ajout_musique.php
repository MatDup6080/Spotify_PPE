<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Musique</title>
    <link rel="stylesheet" href="styles_home.css">
  
</head>
<body>
	
<!--menu du site internet -->
<nav class="menu">
<button class="toggle-btn" onclick="toggleSidebar()">&#9664;</button>
        <ul>
            <li>
                <a href="accueil.html" class="menu1">
                    <img src="images/accueil.png" alt="Page d'accueil de CGDMusic">
                </a>
            </li>
            <li>
                <a href="rechercher_musique.php" class="menu3">
                    <img src="images/musique.png" alt="Page bibliothèque de musiques de CGDMusic">
                </a>
            <li>
                <a href="playlist.php" class="menu2">
                    <img src="images/playlist.png" alt="Page de playlists de CGDMusic">
                </a>
            </li>

        </ul>
    </nav>
	
<!--formulaire d'ajout d'une musique dans la 
	bibliothéque existante.-->
	
    <div class="form-container">
        <h2> New music </h2>
        <form action="insertion_musique.php" method="post" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="title" required>
            <input type="text" name="artist" placeholder="Singer" required>
            <input type="text" name="genre" placeholder="Genre" required>
            <input type="text" name="duration" placeholder="Duration (h:min:sec)" required>
            <input type="file" name="cover" accept="images/*" required>
            <input type="file" name="audio" accept="music/*" required>
            <button type="submit">Add</button>
        </form>
    </div>
    <script src="scripts.js"></script>
</body>
</html> 





