<?php
require 'includes/fonctions.php';
$titre = 'Accueil';

// Métiers + nombre d'ouvriers actifs pour chacun
$metiers = $pdo->query("SELECT t.*,
        (SELECT COUNT(*) FROM workers w WHERE w.trade_id = t.id AND w.statut = 'actif') AS nb
        FROM trades t ORDER BY t.nom")->fetchAll();

require 'includes/header.php';
?>
<section class="hero">
  <div>
    <h1>Des ouvriers qualifiés, <span>à la journée</span></h1>
    <p>Jardinier, plombier, électricien, agent de sécurité... Réservez l'ouvrier dont vous avez besoin en quelques clics.</p>
    <div class="actions">
      <?php if (!$user): ?>
        <a class="btn" href="<?= BASE ?>/register.php">Créer un compte client</a>
        <a class="btn clair" href="<?= BASE ?>/login.php">Se connecter</a>
      <?php elseif ($role === 'client'): ?>
        <a class="btn" href="<?= BASE ?>/client/reserver.php">Faire une réservation</a>
      <?php else: ?>
        <a class="btn" href="<?= BASE ?>/<?= accueil_role($role) ?>">Mon espace</a>
      <?php endif; ?>
    </div>
  </div>
  <img src="<?= BASE ?>/logo.php" alt="Logo <?= SITE ?>">
</section>

<h2>Comment ça marche ?</h2>
<div class="etapes">
  <div class="etape"><div class="num">1</div><h3>Choisissez un métier</h3><p class="discret">Consultez les tarifs par jour.</p></div>
  <div class="etape"><div class="num">2</div><h3>Réservez vos dates</h3><p class="discret">Indiquez l'adresse et le nombre d'ouvriers.</p></div>
  <div class="etape"><div class="num">3</div><h3>L'ouvrier arrive</h3><p class="discret">Nous affectons un ouvrier disponible.</p></div>
</div>

<h2>Nos métiers et tarifs</h2>
<div class="metiers">
<?php foreach ($metiers as $m): ?>
  <div class="metier">
    <span class="icone"><?= icone_metier($m['nom']) ?></span>
    <h3><?= e($m['nom']) ?></h3>
    <div class="tarif"><?= prix($m['tarif_journalier']) ?> <small>/ jour</small></div>
    <p class="discret"><?= (int)$m['nb'] ?> ouvrier(s) dans l'équipe</p>
    <?php if ($role === 'client'): ?>
      <a class="btn petit" href="<?= BASE ?>/client/reserver.php?metier=<?= (int)$m['id'] ?>">Réserver</a>
    <?php elseif (!$user): ?>
      <a class="btn petit" href="<?= BASE ?>/login.php">Se connecter pour réserver</a>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
</div>
<?php require 'includes/footer.php'; ?>
