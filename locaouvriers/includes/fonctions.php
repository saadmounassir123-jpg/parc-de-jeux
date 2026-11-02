<?php
// Outils communs à toutes les pages
session_start();
require_once __DIR__ . '/config.php';

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$hote;dbname=$base_de_donnees;charset=utf8mb4", $utilisateur, $mot_de_passe);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    die('Connexion à la base impossible. Vérifiez le fichier includes/config.php');
}

// Affiche un texte en toute sécurité (protège contre les failles XSS)
function e($texte) {
    return htmlspecialchars((string)$texte, ENT_QUOTES, 'UTF-8');
}

function prix($nombre) {
    return number_format((float)$nombre, 2, ',', ' ') . ' DH';
}

function date_fr($date) {
    return date('d/m/Y', strtotime($date));
}

// Redirige vers une page du site
function aller_a($page) {
    header('Location: ' . BASE . '/' . $page);
    exit;
}

// Message affiché une seule fois (ok = vert, err = rouge)
function message($texte = null, $type = 'ok') {
    if ($texte !== null) {
        $_SESSION['message'] = [$texte, $type];
        return null;
    }
    $m = $_SESSION['message'] ?? null;
    unset($_SESSION['message']);
    return $m;
}

// Page d'accueil de chaque rôle
function accueil_role($role) {
    if ($role === 'admin')   return 'admin/index.php';
    if ($role === 'ouvrier') return 'ouvrier/planning.php';
    return 'client/reservations.php';
}

// Bloque la page si l'utilisateur n'a pas le bon rôle
function exiger_role($role) {
    if (!isset($_SESSION['user'])) {
        message('Veuillez vous connecter.', 'err');
        aller_a('login.php');
    }
    if ($_SESSION['user']['role'] !== $role) {
        http_response_code(403);
        die('Accès refusé.');
    }
}

// Renvoie une seule valeur d'une requête SQL (ex : un COUNT)
function valeur($sql, $params = []) {
    global $pdo;
    $st = $pdo->prepare($sql);
    $st->execute($params);
    return $st->fetchColumn();
}

// Liste des dates entre deux jours (inclus)
function dates_entre($debut, $fin) {
    $liste = [];
    $jour = new DateTime($debut);
    $dernier = new DateTime($fin);
    while ($jour <= $dernier) {
        $liste[] = $jour->format('Y-m-d');
        $jour->modify('+1 day');
    }
    return $liste;
}

// Ouvriers du métier libres sur toute la période (ni mission, ni absence)
function ouvriers_libres($metier_id, $debut, $fin) {
    global $pdo;
    $sql = "SELECT w.id, u.nom FROM workers w
            JOIN users u ON u.id = w.user_id
            WHERE w.trade_id = ? AND w.statut = 'actif'
              AND w.id NOT IN (SELECT worker_id FROM assignments WHERE date_mission BETWEEN ? AND ?)
              AND w.id NOT IN (SELECT worker_id FROM absences WHERE date_absence BETWEEN ? AND ?)
            ORDER BY u.nom";
    $st = $pdo->prepare($sql);
    $st->execute([$metier_id, $debut, $fin, $debut, $fin]);
    return $st->fetchAll();
}

// Met à jour le statut de la réservation selon l'avancement des missions
function synchroniser_reservation($reservation_id) {
    global $pdo;
    $total      = (int)valeur('SELECT COUNT(*) FROM assignments WHERE reservation_id = ?', [$reservation_id]);
    $finies     = (int)valeur("SELECT COUNT(*) FROM assignments WHERE reservation_id = ? AND statut = 'terminee'", [$reservation_id]);
    $commencees = (int)valeur("SELECT COUNT(*) FROM assignments WHERE reservation_id = ? AND statut IN ('en_cours','terminee')", [$reservation_id]);
    if ($total === 0) return;
    $nouveau = null;
    if ($finies === $total) $nouveau = 'terminee';
    elseif ($commencees > 0) $nouveau = 'en_cours';
    if ($nouveau) {
        $pdo->prepare('UPDATE reservations SET statut = ? WHERE id = ?')->execute([$nouveau, $reservation_id]);
    }
}

// Petite icône selon le métier
function icone_metier($nom) {
    $icones = ['Jardinier' => '🌿', 'Plombier' => '🔧', 'Agent de sécurité' => '🛡️', 'Électricien' => '⚡', 'Peintre' => '🎨'];
    return $icones[$nom] ?? '🛠️';
}

// Étiquette colorée pour un statut
function badge($statut) {
    $noms = [
        'en_attente' => 'En attente', 'acceptee' => 'Acceptée', 'affectee' => 'Ouvrier affecté',
        'en_cours' => 'En cours', 'terminee' => 'Terminée', 'refusee' => 'Refusée', 'annulee' => 'Annulée',
        'planifiee' => 'Planifiée', 'a_remplacer' => 'À remplacer', 'a_payer' => 'À payer', 'paye' => 'Payé',
        'actif' => 'Actif', 'inactif' => 'Inactif', 'suspendu' => 'Suspendu',
    ];
    return '<span class="badge ' . e($statut) . '">' . e($noms[$statut] ?? $statut) . '</span>';
}
