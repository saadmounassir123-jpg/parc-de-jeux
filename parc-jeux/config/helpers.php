<?php
// Fonctions utilitaires simples utilisées partout dans l'application.

const CATEGORIES_JEUX = ['Manège', 'Sensations', 'Enfants', 'Famille', 'Aquatique', 'Arcade'];
const ETATS_JEU = ['disponible', 'en_fonctionnement', 'en_maintenance', 'hors_service', 'ferme_temporairement'];
const CATEGORIES_DEPENSE = ['maintenance', 'réparations', 'pièces de rechange', 'électricité', 'nettoyage',
                            'sécurité', 'salaires', 'assurance', 'marketing', 'autre'];

// Texte affiché pour les valeurs stockées en base
function libelle($valeur) {
    $textes = [
        'disponible' => 'Disponible', 'en_fonctionnement' => 'En fonctionnement', 'en_maintenance' => 'En maintenance',
        'hors_service' => 'Hors service', 'ferme_temporairement' => 'Fermé temporairement',
        'admin' => 'Administrateur', 'responsable' => 'Responsable de jeu', 'agent' => 'Agent de vente / accueil',
        'client' => 'Client / visiteur',
        'especes' => 'Espèces', 'carte' => 'Carte', 'paye' => 'Payé', 'en_attente' => 'En attente',
        'confirmee' => 'Confirmée', 'payee' => 'Payée', 'utilisee' => 'Utilisée', 'annulee' => 'Annulée',
        'signalee' => 'Signalée', 'planifiee' => 'Planifiée', 'en_cours' => 'En cours', 'terminee' => 'Terminée',
        'jeu' => 'Lié à un jeu', 'general' => 'Frais généraux',
        'individuel' => 'Individuel', 'famille' => 'Famille', 'groupe' => 'Groupe',
        'actif' => 'Actif', 'inactif' => 'Inactif', 'valide' => 'Valide', 'utilise' => 'Utilisé',
        'billet' => 'Billets', 'reservation' => 'Réservation', 'abonnement' => 'Abonnement', 'autre' => 'Autre',
    ];
    return $textes[$valeur] ?? $valeur;
}

function badge($valeur) {
    return '<span class="badge b-' . e($valeur) . '">' . e(libelle($valeur)) . '</span>';
}

// Protège l'affichage contre les failles XSS
function e($texte) {
    return htmlspecialchars((string)$texte, ENT_QUOTES, 'UTF-8');
}

function montant($valeur) {
    return number_format((float)$valeur, 2, ',', ' ') . ' DH';
}

function dateFr($date) {
    return $date ? date('d/m/Y', strtotime($date)) : '-';
}

function estPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function post($nom, $defaut = '') {
    return (isset($_POST[$nom]) && is_string($_POST[$nom])) ? trim($_POST[$nom]) : $defaut;
}

// Récupère plusieurs champs du formulaire dans un tableau
function champsPost($champs) {
    $resultat = [];
    foreach ($champs as $champ) {
        $resultat[$champ] = post($champ);
    }
    return $resultat;
}

// Vérifie qu'une chaîne est une date valide au format AAAA-MM-JJ
function dateValide($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function selectionne($a, $b) {
    return ((string)$a === (string)$b) ? 'selected' : '';
}

function url($page, $action = 'index', $params = []) {
    return 'index.php?' . http_build_query(array_merge(['page' => $page, 'action' => $action], $params));
}

function redirect($adresse) {
    header('Location: ' . $adresse);
    exit;
}

// Messages (succès / erreur) affichés après une redirection
function flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function bandeauBilletterie() {
    echo '<p class="bandeau-info">La réservation est gratuite en ligne. Le paiement et le retrait des billets se font à la billetterie du parc.</p>';
}

// ---------- Sessions et rôles ----------
function estConnecte() {
    return isset($_SESSION['user']);
}

function roleActuel() {
    return $_SESSION['user']['role'] ?? '';
}

// Personnel du parc (back-office) : admin, responsable, agent
function estPersonnel() {
    return in_array(roleActuel(), ['admin', 'responsable', 'agent'], true);
}

function idUtilisateur() {
    return $_SESSION['user']['id'] ?? null;
}

function idClientSession() {
    return $_SESSION['user']['id_client'] ?? null;
}

function idResponsableSession() {
    return $_SESSION['user']['id_responsable'] ?? null;
}

// Bloque l'accès si le rôle de l'utilisateur n'est pas dans la liste
function exigerRole($rolesAutorises) {
    if (!estConnecte()) {
        flash('erreur', 'Connectez-vous pour accéder à cette page.');
        redirect(url('auth', 'login'));
    }
    if (!in_array(roleActuel(), $rolesAutorises, true)) {
        flash('erreur', "Accès refusé : vous n'avez pas le droit d'accéder à cette page.");
        redirect(estPersonnel() ? url('jeux') : url('accueil'));
    }
}

// ---------- Affichage d'une vue ----------
// $mode : 'public', 'admin' ou 'auth'. Si null, admin pour le personnel, public sinon.
function vue($fichier, $donnees = [], $mode = null) {
    extract($donnees);
    $modeAffichage = $mode ?? (estPersonnel() ? 'admin' : 'public');
    require RACINE . '/views/layout/header.php';
    require RACINE . '/views/layout/navbar.php';
    require RACINE . '/views/' . $fichier . '.php';
    require RACINE . '/views/layout/footer.php';
}

// Pages connexion / inscription (centré, sans menu complet)
function vueAuth($fichier, $donnees = []) {
    extract($donnees);
    $modeAffichage = 'auth';
    require RACINE . '/views/layout/header.php';
    require RACINE . '/views/' . $fichier . '.php';
    require RACINE . '/views/layout/footer.php';
}

// Affiche la liste des erreurs de validation
function afficherErreurs($erreurs) {
    if (!$erreurs) return;
    echo '<div class="message erreur"><ul>';
    foreach ($erreurs as $erreur) echo '<li>' . e($erreur) . '</li>';
    echo '</ul></div>';
}

// Après connexion ou inscription : créer la réservation stockée en session (si le compte est un client)
function finaliserReservationEnAttente($pdo) {
    if (empty($_SESSION['reservation_en_attente'])) {
        return;
    }
    // Un agent ou un admin qui se connecte ne doit pas « voler » la réservation du visiteur
    if (roleActuel() !== 'client' || !idClientSession()) {
        unset($_SESSION['reservation_en_attente']);
        return;
    }
    $d = $_SESSION['reservation_en_attente'];
    $jeux = (isset($d['jeux']) && is_array($d['jeux'])) ? $d['jeux'] : [];
    $res = (new Reservation($pdo))->creer(
        (int)idClientSession(),
        $d['date_visite'] ?? '',
        (int)($d['nombre_personnes'] ?? 0),
        $jeux,
        'en_attente'
    );
    if (isset($res['erreurs'])) {
        flash('erreur', 'La réservation n\'a pas pu être finalisée : ' . implode(' ', $res['erreurs']));
        redirect(url('reservations', 'creer'));
    }
    unset($_SESSION['reservation_en_attente']);
    $resa = (new Reservation($pdo))->trouverComplet($res['id']);
    flash('succes', 'Réservation enregistrée. Présentez-vous à la billetterie du parc avec le numéro ' . $resa['numero_reservation'] . '.');
    redirect(url('reservations', 'confirmation', ['id' => $res['id']]));
}

// Redirection après une connexion réussie (sans réservation en attente)
function redirectApresConnexion() {
    $role = roleActuel();
    if ($role === 'admin') redirect(url('dashboard'));
    if ($role === 'client') redirect(url('accueil'));
    redirect(url('jeux'));
}
