<?php
// En-tête commun : barre de rassurance + navigation moderne
$user  = $_SESSION['user'] ?? null;
$role  = $user['role'] ?? null;
$flash = message();
$titre = $titre ?? SITE;
$pleine_largeur = $pleine_largeur ?? false;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titre) ?> – <?= SITE ?> | Ouvriers Qualifiés à la Journée</title>
<meta name="description" content="Plateforme n°1 de réservation d'ouvriers et artisans qualifiés à la journée : plombiers, électriciens, jardiniers, peintres, sécurité. Devis instantané et réservation rapide.">
<link rel="icon" href="<?= BASE ?>/logo.php">

<!-- Polices modernes Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="<?= BASE ?>/assets/style.css">
<script>window.BASE_URL = '<?= BASE ?>';</script>
<script src="<?= BASE ?>/assets/script.js" defer></script>
</head>
<body>

<!-- Barre d'annonce et réassurance supérieure -->
<div class="top-annonces">
  <div class="conteneur top-barre-flex">
    <div class="top-gauche">
      <span class="top-badge">⭐ N°1 au Maroc</span>
      <span class="top-texte">Des ouvriers certifiés et vérifiés à votre service sous 24h</span>
    </div>
    <div class="top-droite">
      <a href="tel:+212522001122" class="top-contact">📞 Service client : +212 5 22 00 11 22</a>
      <span class="top-sep">|</span>
      <span class="top-horaires">🕒 7j/7 : 8h - 20h</span>
    </div>
  </div>
</div>

<header class="entete">
  <div class="conteneur barre">
    <a href="<?= BASE ?>/index.php" class="logo-lien">
      <img class="logo" src="<?= BASE ?>/logo.php?full=1" alt="<?= SITE ?>">
    </a>

    <button class="burger" aria-label="Menu" onclick="document.getElementById('menu').classList.toggle('ouvert')">
      <span></span><span></span><span></span>
    </button>

    <nav id="menu">
      <a href="<?= BASE ?>/index.php" class="nav-lien">Accueil</a>
      <a href="<?= BASE ?>/index.php#metiers" class="nav-lien">Nos Métiers</a>
      <a href="<?= BASE ?>/index.php#comment-marche" class="nav-lien">Comment ça marche</a>
      <a href="<?= BASE ?>/index.php#simulateur" class="nav-lien">Simulateur</a>
      <a href="<?= BASE ?>/index.php#realisations" class="nav-lien">Réalisations</a>

      <?php if ($role === 'client'): ?>
        <a href="<?= BASE ?>/client/reservations.php" class="nav-lien">Mes réservations</a>
        <a class="bouton-menu" href="<?= BASE ?>/client/reserver.php">✨ Réserver un ouvrier</a>
      <?php elseif ($role === 'ouvrier'): ?>
        <a href="<?= BASE ?>/ouvrier/planning.php" class="nav-lien">Mon planning</a>
      <?php elseif ($role === 'admin'): ?>
        <a href="<?= BASE ?>/admin/index.php" class="nav-lien">Tableau de bord</a>
        <a href="<?= BASE ?>/admin/reservations.php" class="nav-lien">Réservations</a>
        <a href="<?= BASE ?>/admin/ouvriers.php" class="nav-lien">Ouvriers</a>
      <?php endif; ?>

      <?php if ($user): ?>
        <a class="bouton-menu sortie" href="<?= BASE ?>/logout.php">Déconnexion (<?= e($user['nom']) ?>)</a>
      <?php else: ?>
        <a href="<?= BASE ?>/login.php" class="nav-lien nav-connexion">Espace Client</a>
        <a class="bouton-menu" href="<?= BASE ?>/register.php">✨ Réserver maintenant</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="<?= $pleine_largeur ? 'page-pleine-largeur' : 'conteneur contenu' ?>">
<?php if ($flash): ?>
  <div class="conteneur">
    <div class="message <?= e($flash[1]) ?>"><?= e($flash[0]) ?></div>
  </div>
<?php endif; ?>
