<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure la configuration de la base de données
include(__DIR__ . '/config.php'); // Assurez-vous que le chemin est correct

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification des champs du formulaire
    if (isset($_POST['surname'])&&($_POST['name_u']) && isset($_POST['mail']) && isset($_POST['mdp'])) {
        // Sécuriser les données
        $name= htmlspecialchars($_POST['name_u']);
        $surname = htmlspecialchars($_POST['surname']);
        $mail = htmlspecialchars($_POST['mail']);
        $mdp =md5($_POST['mdp']); // Hacher le mot de passe

        // Validation de l'email côté serveur
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['error' => "L'adresse email n'est pas valide."]);
            exit();
        }

        // Vérifier si l'email existe déjà dans la base de données
        $stmtCheck = $pdo->prepare("SELECT * FROM users WHERE mail = :mail");
        $stmtCheck->bindParam(':mail', $mail);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            echo json_encode(['error' => "Cette adresse e-mail est déjà utilisée."]);
            exit();
        }

        try {
            // Préparer la requête d'insertion dans la base de données
            $stmt = $pdo->prepare("INSERT INTO users (name_u,surname, mail, mdp) VALUES (:name_u, :surname, :mail, :mdp)");
            $stmt->bindParam(':name_u', $name);
            $stmt->bindParam(':surname', $surname);
            $stmt->bindParam(':mail', $mail);
            $stmt->bindParam(':mdp', $mdp);

            // Exécuter la requête et vérifier le succès
            if ($stmt->execute()) {
                header("Location: connexion.html"); // Rediriger vers success.html
                exit();
            } else {
                echo json_encode(['error' => "Erreur lors de l'insertion dans la base de données."]);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Erreur SQL : ' . $e->getMessage()]);
        }
        exit();
    } else {
        echo json_encode(['error' => "Tous les champs sont requis."]);
        exit();
    }
}
