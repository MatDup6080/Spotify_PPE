<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "musicdb");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier si les données sont envoyées
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"], $_POST["academic_email"], $_POST["subscription"], $_POST["birthdate"])) {
    $email = trim($_POST["email"]);
    $academicEmail = trim($_POST["academic_email"]);
    $subscriptionId = intval($_POST["subscription"]);
    $birthdate = trim($_POST["birthdate"]);

    // Validation de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Adresse email invalide.'); window.history.back();</script>";
        exit();
    }

    // Validation de l'adresse académique uniquement pour l'abonnement Student
    if ($subscriptionId === 2) {
        if (empty($academicEmail)) {
            echo "<script>alert('Une adresse académique est requise pour l\'abonnement Student.'); window.history.back();</script>";
            exit();
        }

        $allowedDomains = ['edu', 'univ.fr', 'saint-remi.net']; // Liste des domaines autorisés
        $academicDomain = substr(strrchr($academicEmail, "@"), 1); // Extraire le domaine de l'adresse académique
        $academicLocalPart = substr($academicEmail, 0, strpos($academicEmail, "@")); // Extraire la partie avant le @

        $isValidAcademicEmail = false;
        foreach ($allowedDomains as $domain) {
            if (str_ends_with($academicDomain, $domain)) {
                $isValidAcademicEmail = true;
                break;
            }
        }

        if (!$isValidAcademicEmail) {
            echo "<script>alert('Vous devez fournir une adresse académique valide (par exemple, se terminant par .edu ou @univ.fr ou @saint-remi.net).'); window.history.back();</script>";
            exit();
        }

        // Vérifier que la partie avant le @ contient "nom.prenom"
        if (!preg_match('/^[a-zA-Z]+\.[a-zA-Z]+$/', $academicLocalPart)) {
            echo "<script>alert('L\'adresse académique doit être au format nom.prenom avant le @.'); window.history.back();</script>";
            exit();
        }

    
        // Extraire le nom et le prénom de l'adresse académique
        list($nom, $prenom) = explode('.', $academicLocalPart);
    
        // Vérifier que le nom et le prénom correspondent à surname et name_u
        $stmt = $conn->prepare("SELECT name_u, surname, mail_academique FROM users WHERE mail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 0) {
            die("Utilisateur non trouvé.");
        }
    
        $user = $result->fetch_assoc();
        if (strtolower($nom) !== strtolower($user['surname']) || strtolower($prenom) !== strtolower($user['name_u'])) {
            die("Le nom et le prénom dans l'adresse académique ne correspondent pas à vos informations.");
        }
    
        // Si l'adresse académique est vide dans la base de données, insérez-la
        if (empty($user['mail_academique'])) {
            $stmt = $conn->prepare("UPDATE users SET mail_academique = ? WHERE mail = ?");
            $stmt->bind_param("ss", $academicEmail, $email);
            if (!$stmt->execute()) {
                die("Erreur lors de l'insertion de l'adresse académique : " . $conn->error);
            }
        }
    }

    // Validation de la date de naissance
    $birthdateTimestamp = strtotime($birthdate);
    if (!$birthdateTimestamp) {
        die("Date de naissance invalide.");
    }

    // Calculer l'âge de l'utilisateur
    $currentDate = new DateTime();
    $birthDate = new DateTime($birthdate);
    $age = $currentDate->diff($birthDate)->y;

    // Vérifier si l'utilisateur a le droit à l'abonnement "Student"
    if ($subscriptionId === 2 && ($age < 15 || $age > 23)) {
        alert("Vous devez avoir entre 15 et 23 ans pour souscrire à l'abonnement Student.");
    }

    // Mettre à jour l'abonnement de l'utilisateur
    $stmt = $conn->prepare("UPDATE users SET id_s = ? WHERE mail = ?");
    $stmt->bind_param("is", $subscriptionId, $email);

    if ($stmt->execute()) {
        $_SESSION['abonnement'] = $subscriptionId;
        header("Location: index.html"); // Rediriger vers la page d'accueil
        exit();
    } else {
        echo "Erreur lors de la mise à jour de l'abonnement : " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>