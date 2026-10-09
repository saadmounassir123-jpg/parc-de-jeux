<?php
// Navigation : barre publique (visiteur / client) ou barre latérale (personnel du parc)
$role = roleActuel();
$modeAffichage = $modeAffichage ?? (estPersonnel() ? 'admin' : 'public');
?>
<?php if ($modeAffichage === 'admin'): ?>
<aside class="sidebar" id="sidebar">
    <a class="logo" href="<?= e(url('accueil')) ?>">🎡 FunPark</a>
    <nav>
        <?php if ($role === 'admin'): ?>
            <a href="<?= e(url('dashboard')) ?>">Tableau de bord</a>
            <a href="<?= e(url('jeux')) ?>">Jeux</a>
            <a href="<?= e(url('responsables')) ?>">Responsables</a>
            <a href="<?= e(url('clients')) ?>">Clients</a>
            <a href="<?= e(url('billets', 'types')) ?>">Tarifs</a>
            <a href="<?= e(url('billets', 'achats')) ?>">Billetterie</a>
            <a href="<?= e(url('billets', 'verifier')) ?>">Vérifier un billet</a>
            <a href="<?= e(url('reservations')) ?>">Réservations</a>
            <a href="<?= e(url('depenses')) ?>">Dépenses</a>
            <a href="<?= e(url('maintenance')) ?>">Maintenance</a>
            <a href="<?= e(url('utilisateurs')) ?>">Utilisateurs</a>
        <?php elseif ($role === 'responsable'): ?>
            <a href="<?= e(url('jeux')) ?>">Mes jeux</a>
            <a href="<?= e(url('reservations')) ?>">Réservations</a>
            <a href="<?= e(url('depenses')) ?>">Dépenses</a>
            <a href="<?= e(url('maintenance')) ?>">Maintenance</a>
            <a href="<?= e(url('billets', 'types')) ?>">Tarifs</a>
        <?php elseif ($role === 'agent'): ?>
            <a href="<?= e(url('jeux')) ?>">Jeux</a>
            <a href="<?= e(url('billets', 'types')) ?>">Tarifs</a>
            <a href="<?= e(url('clients')) ?>">Clients</a>
            <a href="<?= e(url('billets', 'achats')) ?>">Billetterie</a>
            <a href="<?= e(url('billets', 'verifier')) ?>">Vérifier un billet</a>
            <a href="<?= e(url('reservations')) ?>">Réservations</a>
        <?php endif; ?>
    </nav>
    <div class="sidebar-user">
        <strong><?= e($_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom']) ?></strong>
        <span><?= e(libelle($role)) ?></span>
        <a href="<?= e(url('auth', 'logout')) ?>">Déconnexion</a>
    </div>
</aside>
<div class="admin-enveloppe">
    <header class="admin-topbar">
        <button class="burger" type="button" id="burger" aria-label="Ouvrir le menu">☰</button>
        <span class="admin-topbar-titre"><?= e($titrePage ?? 'Espace interne') ?></span>
    </header>
    <main class="contenu contenu-admin">
<?php else: ?>
<header class="nav-publique">
    <a class="logo" href="<?= e(url('accueil')) ?>">🎡 FunPark</a>
    <button class="burger" type="button" id="burger" aria-label="Ouvrir le menu">☰</button>
    <nav id="menu">
        <a href="<?= e(url('accueil')) ?>">Accueil</a>
        <a href="<?= e(url('jeux')) ?>">Jeux</a>
        <a href="<?= e(url('billets', 'types')) ?>">Tarifs</a>
        <a href="<?= e(url('reservations', 'creer')) ?>">Réserver</a>
        <?php if (estConnecte() && $role === 'client'): ?>
            <a href="<?= e(url('reservations')) ?>">Mes réservations</a>
        <?php endif; ?>
        <?php if (estPersonnel()): ?>
            <a href="<?= e($role === 'admin' ? url('dashboard') : url('jeux')) ?>">Espace interne</a>
        <?php endif; ?>
    </nav>
    <div class="nav-compte">
        <?php if (!estConnecte()): ?>
            <a class="btn btn-connexion" href="<?= e(url('auth', 'login')) ?>">Connexion</a>
        <?php else: ?>
            <span class="nav-prenom"><?= e($_SESSION['user']['prenom']) ?></span>
            <a class="lien-deconnexion" href="<?= e(url('auth', 'logout')) ?>">Déconnexion</a>
        <?php endif; ?>
    </div>
</header>
<main class="contenu">
<?php endif; ?>
<?php if (isset($_SESSION['flash'])): ?>
    <div class="message <?= e($_SESSION['flash']['type']) ?>"><?= e($_SESSION['flash']['message']) ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
