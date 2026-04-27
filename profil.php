<?php
session_start();

require_once 'config2.php';


// Récupérer l'ID de l'utilisateur depuis la session
$utilisateurId = $_SESSION['utilisateur_id'] ?? null;

$user = null; // Initialiser la variable utilisateur
$error = null; // Initialiser la variable d'erreur

if ($utilisateurId) {
    // Requête pour récupérer les informations de l'utilisateur
    $sql = "SELECT u.id, u.name_u, u.surname, u.mail, s.name_s AS abonnement 
    FROM users u
    LEFT JOIN subscribe s ON u.id_s = s.id
    WHERE u.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $utilisateurId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $error = "Aucun utilisateur trouvé avec cet ID.";
    }

    $stmt->close();
} else {
    $error = "Utilisateur non connecté.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles_home.css">
    <title>Profil | CGD Music</title>
</head>
<?php include 'menu.php'; ?>
<body>
    <div class="profile-container">
        <?php if ($user): ?>
            <h2>Profil de l'utilisateur :</h2>
            <p><strong>Nom :</strong> <?= htmlspecialchars($user['name_u']); ?></p>
            <p><strong>Prénom :</strong> <?= htmlspecialchars($user['surname']); ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($user['mail']); ?></p>
            <p><strong>Type d'abonnement  :</strong> <?= htmlspecialchars($user['abonnement']); ?></p>
        <?php else: ?>
            <p><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</body>

</html>