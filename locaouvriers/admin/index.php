<?php
require '../includes/fonctions.php';
exiger_role('admin');
$titre = 'Tableau de bord';

$total       = valeur('SELECT COUNT(*) FROM reservations');
$en_attente  = valeur("SELECT COUNT(*) FROM reservations WHERE statut = 'en_attente'");
$en_mission  = valeur('SELECT COUNT(DISTINCT worker_id) FROM assignments WHERE date_mission = CURDATE()');
$disponibles = valeur("SELECT COUNT(*) FROM workers WHERE statut = 'actif'
                       AND id NOT IN (SELECT worker_id FROM assignments WHERE date_mission = CURDATE())
                       AND id NOT IN (SELECT worker_id FROM absences WHERE date_absence = CURDATE())");
$clients     = valeur("SELECT COUNT(*) FROM users WHERE role = 'client'");
$ca_mois     = valeur('SELECT COALESCE(SUM(montant), 0) FROM reservations
                      WHERE paye = 1 AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())');

require '../includes/header.php';
?>
<h1>Tableau de bord</h1>
<p class="discret">Vue d'ensemble de l'activité.</p>

<?php if ($en_attente > 0): ?>
  <div class="message err">Il y a <?= (int)$en_attente ?> réservation(s) en attente. <a href="<?= BASE ?>/admin/reservations.php?statut=en_attente">Les traiter</a></div>
<?php endif; ?>

<div class="stats">
  <div class="stat"><span class="icone">📋</span><b><?= (int)$total ?></b>Réservations</div>
  <div class="stat"><span class="icone">⏳</span><b><?= (int)$en_attente ?></b>En attente</div>
  <div class="stat"><span class="icone">👷</span><b><?= (int)$en_mission ?></b>Ouvriers en mission aujourd'hui</div>
  <div class="stat"><span class="icone">✅</span><b><?= (int)$disponibles ?></b>Ouvriers disponibles aujourd'hui</div>
  <div class="stat"><span class="icone">👥</span><b><?= (int)$clients ?></b>Clients</div>
  <div class="stat"><span class="icone">💰</span><b><?= prix($ca_mois) ?></b>Chiffre d'affaires du mois</div>
</div>
<?php require '../includes/footer.php'; ?>
