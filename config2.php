<?php
// filepath: c:\Users\Mathieu\Documents\BTS SIO Amiens\Bloc2\CGDMusic\config.php
$conn = new mysqli("localhost", "root", "", "musicdb");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}
?>