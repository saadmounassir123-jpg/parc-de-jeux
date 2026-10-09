<?php
// Connexion à la base de données avec PDO.
function getPDO() {
    $hote = '127.0.0.1';
    $port = '3307';          // mettre 3307 si MySQL utilise un autre port dans XAMPP
    $base = 'parc_jeux';
    $utilisateur = 'root';
    $motDePasse = '';        // vide par défaut sous XAMPP

    try {
        $pdo = new PDO("mysql:host=$hote;port=$port;dbname=$base;charset=utf8mb4", $utilisateur, $motDePasse);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    } catch (PDOException $e) {
        die("Connexion impossible à la base de données : " . htmlspecialchars($e->getMessage()));
    }
}