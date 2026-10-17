<?php
// Connexion à la base de données avec PDO.
function getPDO() {
    $hote = '127.0.0.1';
    $ports = ['3306'];
    $base = 'parc_jeux';
    $utilisateur = 'root';
    $motDePasse = '';
    $dernierMessage = '';

    foreach ($ports as $port) {
        try {
            $pdo = new PDO("mysql:host=$hote;port=$port;dbname=$base;charset=utf8mb4", $utilisateur, $motDePasse);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $pdo;
        } catch (PDOException $e) {
            $dernierMessage = $e->getMessage();
        }
    }

    die("Connexion impossible à la base de données : " . htmlspecialchars($dernierMessage));
}