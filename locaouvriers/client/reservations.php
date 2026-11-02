<?php
require '../includes/fonctions.php';
exiger_role('client');
$titre = 'Mes réservations';
$client_id = $_SESSION['user']['id'];

// Annulation d'une réservation (avant le début de la mission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $st = $pdo->prepare('SELECT * FROM reservations WHERE id = ? AND client_id = ?');
    $st->execute([$id, $client_id]);
    $r = $st->fetch();

    if ($r && in_array($r['statut'], ['en_attente', 'acceptee', 'affectee']) && $r['date_debut'] > date('Y-m-d')) {
        $pdo->prepare("UPDATE reservations SET statut = 'annulee' WHERE id = ?")->execute([$id]);
        $pdo->prepare('DELETE FROM assignments WHERE reservation_id = ?')->execute([$id]);
        message('Réservation annulée.');
    } else {
        message('Cette réservation ne peut plus être annulée.', 'err');
    }
    aller_a('client/reservations.php');
}

$st = $pdo->prepare('SELECT r.*, t.nom AS metier FROM reservations r
                     JOIN trades t ON t.id = r.trade_id
                     WHERE r.client_id = ? ORDER BY r.created_at DESC');
$st->execute([$client_id]);
$reservations = $st->fetchAll();

require '../includes/header.php';
?>
<div class="ligne">
  <div><h1>Mes réservations</h1><p class="discret">Suivez l'avancement de vos demandes.</p></div>
  <a class="btn" href="<?= BASE ?>/client/reserver.php">+ Nouvelle réservation</a>
</div>

<?php if (!$reservations): ?>
  <div class="carte">Vous n'avez pas encore de réservation. <a href="<?= BASE ?>/client/reserver.php">Faites la première !</a></div>
<?php endif; ?>

<div class="liste">
<?php foreach ($reservations as $r): ?>
  <?php
    // Ouvriers affectés à cette réservation
    $st = $pdo->prepare('SELECT DISTINCT u.nom FROM assignments a
                         JOIN workers w ON w.id = a.worker_id JOIN users u ON u.id = w.user_id
                         WHERE a.reservation_id = ?');
    $st->execute([$r['id']]);
    $ouvriers = array_column($st->fetchAll(), 'nom');
  ?>
  <div class="carte">
    <div class="ligne">
      <h3><?= icone_metier($r['metier']) ?> <?= e($r['metier']) ?> × <?= (int)$r['nombre_ouvriers'] ?></h3>
      <?= badge($r['statut']) ?>
    </div>
    <p>📅 Du <?= date_fr($r['date_debut']) ?> au <?= date_fr($r['date_fin']) ?><br>
       📍 <?= e($r['adresse']) ?><br>
       💰 <?= prix($r['montant']) ?> – <?= $r['paye'] ? 'Payée' : 'Non payée' ?></p>
    <?php if ($ouvriers): ?>
      <p>👷 Ouvrier(s) : <b><?= e(implode(', ', $ouvriers)) ?></b></p>
    <?php endif; ?>

    <?php if (in_array($r['statut'], ['en_attente', 'acceptee', 'affectee']) && $r['date_debut'] > date('Y-m-d')): ?>
      <form method="post" class="en-ligne" onsubmit="return confirm('Annuler cette réservation ?')">
        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
        <button class="btn rouge petit" type="submit">Annuler</button>
      </form>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
</div>
<?php require '../includes/footer.php'; ?>
