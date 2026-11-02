<?php
require '../includes/fonctions.php';
exiger_role('ouvrier');
$titre = 'Mon planning';

// Fiche ouvrier de l'utilisateur connecté
$st = $pdo->prepare('SELECT * FROM workers WHERE user_id = ?');
$st->execute([$_SESSION['user']['id']]);
$ouvrier = $st->fetch();
if (!$ouvrier) die('Profil ouvrier introuvable.');

$aujourdhui = date('Y-m-d');

// Pointage : démarrer ou terminer une mission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $st = $pdo->prepare('SELECT * FROM assignments WHERE id = ? AND worker_id = ?');
    $st->execute([$id, $ouvrier['id']]);
    $m = $st->fetch();

    if ($m && $action === 'demarrer' && $m['date_mission'] === $aujourdhui && $m['statut'] === 'planifiee') {
        $pdo->prepare("UPDATE assignments SET statut = 'en_cours' WHERE id = ?")->execute([$id]);
        synchroniser_reservation($m['reservation_id']);
        message('Mission démarrée.');
    } elseif ($m && $action === 'terminer' && $m['statut'] === 'en_cours') {
        $pdo->prepare("UPDATE assignments SET statut = 'terminee' WHERE id = ?")->execute([$id]);
        synchroniser_reservation($m['reservation_id']);
        message('Mission terminée. Bon travail !');
    } else {
        message('Action impossible.', 'err');
    }
    aller_a('ouvrier/planning.php');
}

$st = $pdo->prepare('SELECT a.*, r.adresse, r.description, u.nom AS client_nom, u.telephone AS client_tel
                     FROM assignments a
                     JOIN reservations r ON r.id = a.reservation_id
                     JOIN users u ON u.id = r.client_id
                     WHERE a.worker_id = ? ORDER BY a.date_mission DESC');
$st->execute([$ouvrier['id']]);
$missions = $st->fetchAll();

require '../includes/header.php';
?>
<h1>Mon planning</h1>
<p class="discret">Bonjour <?= e($_SESSION['user']['nom']) ?> 👷 – voici vos missions.</p>

<?php if (!$missions): ?>
  <div class="carte">Aucune mission pour le moment.</div>
<?php endif; ?>

<div class="liste">
<?php foreach ($missions as $m): ?>
  <div class="carte mission <?= $m['date_mission'] === $aujourdhui ? 'aujourdhui' : '' ?>">
    <div class="ligne">
      <h3>📅 <?= date_fr($m['date_mission']) ?><?= $m['date_mission'] === $aujourdhui ? " (aujourd'hui)" : '' ?></h3>
      <?= badge($m['statut']) ?>
    </div>
    <p>📍 <?= e($m['adresse']) ?><br>
       👤 Client : <?= e($m['client_nom']) ?> – 📞 <?= e($m['client_tel']) ?><br>
       🛠️ <?= e($m['description']) ?></p>

    <?php if ($m['date_mission'] === $aujourdhui && $m['statut'] === 'planifiee'): ?>
      <form method="post" class="en-ligne">
        <input type="hidden" name="id" value="<?= (int)$m['id'] ?>"><input type="hidden" name="action" value="demarrer">
        <button class="btn vert petit" type="submit">▶ Démarrer</button>
      </form>
    <?php elseif ($m['statut'] === 'en_cours'): ?>
      <form method="post" class="en-ligne">
        <input type="hidden" name="id" value="<?= (int)$m['id'] ?>"><input type="hidden" name="action" value="terminer">
        <button class="btn petit" type="submit">✔ Terminer</button>
      </form>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
</div>
<?php require '../includes/footer.php'; ?>
