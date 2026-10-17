<?php
// Point d'entrée unique : index.php?page=jeux&action=ajouter
// "page" choisit le contrôleur, "action" choisit la méthode à exécuter.
define('RACINE', __DIR__);
session_start();

require RACINE . '/config/database.php';
require RACINE . '/config/helpers.php';

// Chargement des modèles
foreach (['User', 'Jeu', 'Responsable', 'Client', 'Billet', 'Reservation', 'Recette', 'Depense', 'Maintenance', 'Promotion', 'Achat'] as $modele) {
    require RACINE . '/models/' . $modele . '.php';
}

$controleurs = [
    'accueil' => 'AccueilController',
    'auth' => 'AuthController', 'dashboard' => 'DashboardController', 'jeux' => 'JeuController',
    'responsables' => 'ResponsableController', 'clients' => 'ClientController', 'billets' => 'BilletController',
    'reservations' => 'ReservationController', 'depenses' => 'DepenseController',
    'maintenance' => 'MaintenanceController', 'utilisateurs' => 'UtilisateurController',
    'promotions' => 'PromotionController', 'achats' => 'AchatController', 'paiement' => 'PaiementController',
];

$page = $_GET['page'] ?? 'accueil';
$action = $_GET['action'] ?? 'index';

// Actions consultables sans être connecté (le reste exige une session)
$actionsPubliques = [
    'accueil' => ['index'],
    'jeux' => ['index', 'detail'],
    'billets' => ['types', 'acheter'],
    'reservations' => ['creer'],
    'auth' => ['login', 'logout', 'inscription'],
    'promotions' => ['index', 'verifier'],
    'achats' => ['billetterie', 'creer'],
];

$estPublique = isset($actionsPubliques[$page]) && in_array($action, $actionsPubliques[$page], true);

if (!$estPublique && !estConnecte()) {
    flash('erreur', 'Connectez-vous pour accéder à cette page.');
    redirect(url('auth', 'login'));
}

if (!isset($controleurs[$page]) || !preg_match('/^[a-zA-Z]+$/', $action)) {
    flash('erreur', 'Page introuvable.');
    redirect(url('accueil'));
}

$nomClasse = $controleurs[$page];
require RACINE . '/controllers/' . $nomClasse . '.php';

try {
    $controleur = new $nomClasse(getPDO());
    $methode = 'action' . ucfirst($action);
    if (!method_exists($controleur, $methode)) {
        flash('erreur', 'Action introuvable.');
        redirect(url('accueil'));
    }
    $controleur->$methode();
} catch (PDOException $e) {
    die("Erreur de base de données : " . e($e->getMessage()));
}
