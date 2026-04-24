<?php
// Paramètres de connexion
$host = "localhost";
$user = "root";
$password = "";
$dbname = "drinkfly";

// Création connexion
$conn = new mysqli($host, $user, $password, $dbname);

// Vérification
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Encodage UTF-8 (important pour accents)
$conn->set_charset("utf8");
?>