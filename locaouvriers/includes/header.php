<?php
// En-tête commun : logo + menu selon le rôle
$user  = $_SESSION['user'] ?? null;
$role  = $user['role'] ?? null;
$flash = message();
$titre = $titre ?? SITE;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titre) ?> – <?= SITE ?></title>
<link rel="icon" href="<?= BASE ?>/logo.php">
<link rel="stylesheet" href="<?= BASE ?>/assets/style.css">
</head>
<body>
<header class="entete">
  <div class="conteneur barre">
    <a href="<?= BASE ?>/index.php"><img class="logo" src="<?= BASE ?>/logo.php?full=1" alt="<?= SITE ?>"></a>
    <button class="burger" onclick="document.getElementById('menu').classList.toggle('ouvert')">☰</button>
    <nav id="menu">
      <?php if ($role === 'client'): ?>
        <a href="<?= BASE ?>/client/reserver.php">Réserver</a>
        <a href="<?= BASE ?>/client/reservations.php">Mes réservations</a>
      <?php elseif ($role === 'ouvrier'): ?>
        <a href="<?= BASE ?>/ouvrier/planning.php">Mon planning</a>
      <?php elseif ($role === 'admin'): ?>
        <a href="<?= BASE ?>/admin/index.php">Tableau de bord</a>
        <a href="<?= BASE ?>/admin/reservations.php">Réservations</a>
        <a href="<?= BASE ?>/admin/ouvriers.php">Ouvriers</a>
      <?php endif; ?>
      <?php if ($user): ?>
        <a class="bouton-menu sortie" href="<?= BASE ?>/logout.php">Déconnexion (<?= e($user['nom']) ?>)</a>
      <?php else: ?>
        <a href="<?= BASE ?>/login.php">Connexion</a>
        <a class="bouton-menu" href="<?= BASE ?>/register.php">Inscription</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="conteneur contenu">
<?php if ($flash): ?>
  <div class="message <?= e($flash[1]) ?>"><?= e($flash[0]) ?></div>
<?php endif; ?>
