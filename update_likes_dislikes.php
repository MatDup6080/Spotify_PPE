<?php

session_start();
$conn = new mysqli("localhost", "root", "", "MusicDB");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données.']));
}

if (!isset($_SESSION['utilisateur_id'])) {
    die(json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']));
}

if (isset($_POST['id_s']) && isset($_POST['action'])) {
    $userId = $_SESSION['utilisateur_id'];
    $songId = $_POST['id_s'];
    $action = $_POST['action'];

    // Vérifier si une entrée existe déjà pour cet utilisateur et cette musique
    $stmt = $conn->prepare("SELECT statut FROM likes_dislikes WHERE id_u = ? AND id_s = ?");
    $stmt->bind_param("ii", $userId, $songId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Une entrée existe déjà, mettre à jour le statut
        $newStatut = ($action === 'like') ? 0 : 1;
        $stmt = $conn->prepare("UPDATE likes_dislikes SET statut = ? WHERE id_u = ? AND id_s = ?");
        $stmt->bind_param("iii", $newStatut, $userId, $songId);
        $stmt->execute();
    } else {
        // Aucune entrée n'existe, insérer une nouvelle action
        $statut = ($action === 'like') ? 0 : 1;
        $stmt = $conn->prepare("INSERT INTO likes_dislikes (id_u, id_s, statut) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $userId, $songId, $statut);
        $stmt->execute();
    }

    // Récupérer les nouveaux compteurs de likes et dislikes pour cette musique
    $stmt = $conn->prepare("
        SELECT 
            COUNT(CASE WHEN statut = 0 THEN 1 END) AS likes_count,
            COUNT(CASE WHEN statut = 1 THEN 1 END) AS dislikes_count
        FROM likes_dislikes
        WHERE id_s = ?
    ");
    $stmt->bind_param("i", $songId);
    $stmt->execute();
    $result = $stmt->get_result();
    $counts = $result->fetch_assoc();

    echo json_encode([
        'success' => true,
        'likes_count' => $counts['likes_count'] ?? 0,
        'dislikes_count' => $counts['dislikes_count'] ?? 0
    ]);

    $stmt->close();
}

$conn->close();
?>