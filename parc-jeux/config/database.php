<?php
// Connexion à la base de données avec PDO.
function getPDO() {
    $hote = '127.0.0.1';
    $port = '3306';          // mettre 3307 si MySQL utilise un autre port dans XAMPP
    $base = 'parc_jeux';
    $utilisateur = 'root';
    $motDePasse = '';        // vide par défaut sous XAMPP

    if (!extension_loaded('pdo_mysql')) {
        echo '<div style="font-family:Segoe UI,sans-serif;max-width:650px;margin:50px auto;padding:30px;background:#fff1f2;border:1px solid #fecdd3;border-radius:16px;color:#9f1239">';
        echo '<h2 style="margin-top:0">⚠️ extension pdo_mysql non activée</h2>';
        echo '<p>L\'extension PHP <code>pdo_mysql</code> n\'est pas activée dans votre configuration PHP.</p>';
        echo '<p><b>Pour XAMPP :</b> Activez <code>extension=pdo_mysql</code> dans votre fichier <code>php.ini</code> puis redémarrez Apache.</p>';
        echo '</div>';
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=$hote;port=$port;dbname=$base;charset=utf8mb4", $utilisateur, $motDePasse);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    } catch (PDOException $e) {
        echo '<div style="font-family:Segoe UI,sans-serif;max-width:650px;margin:50px auto;padding:30px;background:#fff1f2;border:1px solid #fecdd3;border-radius:16px;color:#9f1239">';
        echo '<h2 style="margin-top:0">🎡 FunPark — Connexion à la base de données</h2>';
        echo '<p>Impossible de se connecter à MySQL : <code>' . htmlspecialchars($e->getMessage()) . '</code></p>';
        echo '<h3>Guide d\'installation rapide :</h3>';
        echo '<ol style="line-height:1.8">';
        echo '<li>Démarrer <b>MySQL</b> dans le panneau XAMPP.</li>';
        echo '<li>Ouvrir <a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a> et créer la base <code>parc_jeux</code>.</li>';
        echo '<li>Importer le fichier SQL : <code>database/parc_jeux.sql</code>.</li>';
        echo '</ol>';
        echo '</div>';
        exit;
    }
}