<!-- filepath: e:\BTS_SIO\Bloc_2\Programmation Web\CGDMusic\formulaire_abonnemnt.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisissez votre abonnement</title>
    <link rel="stylesheet" href="styles_home.css">
</head>
<?php include 'menu.php'; ?>
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