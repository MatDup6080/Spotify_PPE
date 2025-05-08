<!-- filepath: e:\BTS_SIO\Bloc_2\Programmation Web\CGDMusic\formulaire_abonnemnt.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisissez votre abonnement</title>
    <link rel="stylesheet" href="styles_home.css">
</head>
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
            <li>
                <a href="index.html" class="menu2">
                    <img src="images/accueil.png" alt="Page d'accueil de CGDMusic">
                    <span>Bibliothéque Musique</span>
                </a>
                
            </li>
            <li>
                <a href="rechercher_musique.php" class="menu2">
                    <img src="images/musique.png" alt="Page d'accueil de CGDMusic">
                    <span>Bibliothéque Musique</span>
                </a>
                
            </li>
            <li>
                <a href="playlist.php" class="menu3">
                    <img src="images/playlist.png" alt="Page de playlists de CGDMusic">
                    <span>Playlists</span>
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
<body>
    <h2>Choisissez votre abonnement</h2>
    <div class="form-container">
    <form action="update_subscription.php" method="POST">
        <label for="email">Votre email :</label>
        <input type="email" id="email" name="email" required>
        <br><br>
        <label for="academic_email">Votre adresse académique :</label>
        <input type="email" id="academic_email" name="academic_email" placeholder="exemple@univ.fr" >
        <br><br>
        <label for="birthdate">Votre date de naissance :</label>
        <input type="date" id="birthdate" name="birthdate" required>
        <br><br>
        <label for="subscription">Type d'abonnement :</label>
        <select id="subscription" name="subscription" required>
            <?php
            // Connexion à la base de données
            $conn = new mysqli("localhost", "root", "", "musicdb");
            if ($conn->connect_error) {
                die("Erreur de connexion : " . $conn->connect_error);
            }

            // Récupérer les abonnements depuis la table `subscribe`
            $result = $conn->query("SELECT id, name_s, price FROM subscribe");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name_s']) . ' - ' . htmlspecialchars($row['price']) . '€</option>';
                }
            } else {
                echo '<option value="">Aucun abonnement disponible</option>';
            }
            $conn->close();
            ?>
        </select>
        <br><br>
        <button type="submit">Mettre à jour l'abonnement</button>
    </form>
    </div>
    <script src="scripts_site.js"></script>
</body>
</html>