<?php
require '../includes/fonctions.php';
exiger_role('admin');
$titre = 'Réservations';

// Actions de l'administrateur (accepter, refuser, affecter...)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $st = $pdo->prepare('SELECT * FROM reservations WHERE id = ?');
    $st->execute([$id]);
    $r = $st->fetch();

    if ($r) {
        if ($action === 'accepter' && $r['statut'] === 'en_attente') {
            $pdo->prepare("UPDATE reservations SET statut = 'acceptee' WHERE id = ?")->execute([$id]);
            message('Réservation acceptée. Affectez maintenant un ouvrier.');

        } elseif ($action === 'refuser' && $r['statut'] === 'en_attente') {
            $pdo->prepare("UPDATE reservations SET statut = 'refusee' WHERE id = ?")->execute([$id]);
            message('Réservation refusée.');

        } elseif ($action === 'annuler' && in_array($r['statut'], ['acceptee', 'affectee'])) {
            $pdo->prepare("UPDATE reservations SET statut = 'annulee' WHERE id = ?")->execute([$id]);
            $pdo->prepare('DELETE FROM assignments WHERE reservation_id = ?')->execute([$id]);
            message('Réservation annulée.');

        } elseif ($action === 'payee' || $action === 'impayee') {
            $pdo->prepare('UPDATE reservations SET paye = ? WHERE id = ?')->execute([$action === 'payee' ? 1 : 0, $id]);
            message($action === 'payee' ? 'Réservation marquée comme payée.' : 'Paiement retiré.');

        } elseif ($action === 'affecter' && in_array($r['statut'], ['acceptee', 'affectee'])) {
            $ouvrier_id = (int)($_POST['worker_id'] ?? 0);
            $deja = (int)valeur('SELECT COUNT(DISTINCT worker_id) FROM assignments WHERE reservation_id = ?', [$id]);
            $libres = array_map('intval', array_column(ouvriers_libres($r['trade_id'], $r['date_debut'], $r['date_fin']), 'id'));

            if ($deja >= $r['nombre_ouvriers']) {
                message('Tous les ouvriers demandés sont déjà affectés.', 'err');
            } elseif (!in_array($ouvrier_id, $libres)) {
                message("Cet ouvrier n'est pas disponible sur toute la période.", 'err');
            } else {
                // Une mission par jour pour cet ouvrier
                foreach (dates_entre($r['date_debut'], $r['date_fin']) as $jour) {
                    $pdo->prepare('INSERT INTO assignments (reservation_id, worker_id, date_mission) VALUES (?, ?, ?)')
                        ->execute([$id, $ouvrier_id, $jour]);
                }
                if ($deja + 1 >= $r['nombre_ouvriers']) {
                    $pdo->prepare("UPDATE reservations SET statut = 'affectee' WHERE id = ?")->execute([$id]);
                }
                message('Ouvrier affecté.');
            }
        }
    }
    aller_a('admin/reservations.php');
}

// Liste (avec filtre par statut)
$filtre = $_GET['statut'] ?? '';
$sql = 'SELECT r.*, t.nom AS metier, u.nom AS client_nom, u.telephone AS client_tel,
               (SELECT COUNT(DISTINCT worker_id) FROM assignments a WHERE a.reservation_id = r.id) AS nb_affectes
        FROM reservations r JOIN trades t ON t.id = r.trade_id JOIN users u ON u.id = r.client_id';
$params = [];
if ($filtre !== '') { $sql .= ' WHERE r.statut = ?'; $params[] = $filtre; }
$st = $pdo->prepare($sql . ' ORDER BY r.created_at DESC');
$st->execute($params);
$reservations = $st->fetchAll();

$statuts = ['' => 'Toutes', 'en_attente' => 'En attente', 'acceptee' => 'Acceptées', 'affectee' => 'Affectées',
            'en_cours' => 'En cours', 'terminee' => 'Terminées', 'refusee' => 'Refusées', 'annulee' => 'Annulées'];

require '../includes/header.php';
?>
<h1>Réservations</h1>
<form method="get" class="actions">
  <select name="statut" onchange="this.form.submit()">
    <?php foreach ($statuts as $cle => $nom): ?>
      <option value="<?= e($cle) ?>" <?= $filtre === $cle ? 'selected' : '' ?>><?= e($nom) ?></option>
    <?php endforeach; ?>
  </select>
</form>

<?php if (!$reservations): ?><div class="carte" style="margin-top:16px">Aucune réservation.</div><?php endif; ?>

<div class="liste" style="margin-top:16px">
<?php foreach ($reservations as $r): ?>
  <div class="carte">
    <div class="ligne">
      <h3>#<?= (int)$r['id'] ?> – <?= icone_metier($r['metier']) ?> <?= e($r['metier']) ?> × <?= (int)$r['nombre_ouvriers'] ?></h3>
      <span><?= badge($r['statut']) ?> <?= $r['paye'] ? badge('paye') : badge('a_payer') ?></span>
    </div>
    <p>👤 <?= e($r['client_nom']) ?> – 📞 <?= e($r['client_tel']) ?><br>
       📅 Du <?= date_fr($r['date_debut']) ?> au <?= date_fr($r['date_fin']) ?><br>
       📍 <?= e($r['adresse']) ?><br>
       💰 <?= prix($r['montant']) ?> – 👷 <?= (int)$r['nb_affectes'] ?>/<?= (int)$r['nombre_ouvriers'] ?> affecté(s)</p>

    <div class="actions">
    <?php if ($r['statut'] === 'en_attente'): ?>
      <form method="post" class="en-ligne"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="action" value="accepter"><button class="btn vert petit">Accepter</button></form>
      <form method="post" class="en-ligne"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="action" value="refuser"><button class="btn rouge petit">Refuser</button></form>
    <?php endif; ?>

    <?php if (in_array($r['statut'], ['acceptee', 'affectee'])): ?>
      <?php if ($r['nb_affectes'] < $r['nombre_ouvriers']): ?>
        <?php $libres = ouvriers_libres($r['trade_id'], $r['date_debut'], $r['date_fin']); ?>
        <?php if ($libres): ?>
          <form method="post" class="actions" style="margin:0">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="action" value="affecter">
            <select name="worker_id" required>
              <option value="">-- Choisir un ouvrier --</option>
              <?php foreach ($libres as $o): ?><option value="<?= (int)$o['id'] ?>"><?= e($o['nom']) ?></option><?php endforeach; ?>
            </select>
            <button class="btn petit">Affecter</button>
          </form>
        <?php else: ?>
          <span class="discret">Aucun ouvrier libre sur cette période.</span>
        <?php endif; ?>
      <?php endif; ?>
      <form method="post" class="en-ligne" onsubmit="return confirm('Annuler cette réservation ?')"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="action" value="annuler"><button class="btn rouge petit">Annuler</button></form>
    <?php endif; ?>

    <form method="post" class="en-ligne"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
      <input type="hidden" name="action" value="<?= $r['paye'] ? 'impayee' : 'payee' ?>">
      <button class="btn sombre petit"><?= $r['paye'] ? 'Retirer le paiement' : 'Marquer payée' ?></button></form>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php require '../includes/footer.php'; ?>
