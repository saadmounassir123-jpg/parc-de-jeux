<?php
require '../includes/fonctions.php';
exiger_role('client');
$titre = 'Réserver';

$metiers = $pdo->query('SELECT * FROM trades ORDER BY nom')->fetchAll();

// Valeurs du formulaire (pré-remplissage depuis l'accueil ou le simulateur)
$f = [
    'metier'      => (int)($_GET['metier'] ?? 0),
    'nb'          => max(1, (int)($_GET['nb'] ?? 1)),
    'debut'       => $_GET['debut'] ?? date('Y-m-d'),
    'fin'         => $_GET['fin'] ?? ($_GET['debut'] ?? date('Y-m-d')),
    'adresse'     => '',
    'description' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $f = [
        'metier'      => (int)($_POST['metier'] ?? 0),
        'nb'          => max(1, (int)($_POST['nb'] ?? 1)),
        'debut'       => $_POST['debut'] ?? '',
        'fin'         => $_POST['fin'] ?? '',
        'adresse'     => trim($_POST['adresse'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
    ];

    $st = $pdo->prepare('SELECT * FROM trades WHERE id = ?');
    $st->execute([$f['metier']]);
    $metier = $st->fetch();

    $debut_ok = DateTime::createFromFormat('Y-m-d', $f['debut']);
    $fin_ok   = DateTime::createFromFormat('Y-m-d', $f['fin']);

    // Vérifications, une par une
    $erreur = '';
    if (!$metier)                                     $erreur = 'Choisissez un métier.';
    elseif ($f['adresse'] === '')                     $erreur = "L'adresse du lieu de travail est obligatoire.";
    elseif (!$debut_ok || !$fin_ok)                   $erreur = 'Dates invalides.';
    elseif ($f['debut'] < date('Y-m-d'))              $erreur = 'La date de début ne peut pas être dans le passé.';
    elseif ($f['fin'] < $f['debut'])                  $erreur = 'La date de fin doit être après la date de début.';
    elseif (count(dates_entre($f['debut'], $f['fin'])) > 31) $erreur = 'Durée maximale : 31 jours.';
    else {
        $libres = count(ouvriers_libres($metier['id'], $f['debut'], $f['fin']));
        if ($libres < $f['nb']) $erreur = "Seulement $libres ouvrier(s) disponible(s) sur cette période pour ce métier.";
    }

    if ($erreur !== '') {
        message($erreur, 'err');
    } else {
        // Prix = tarif par jour x nombre de jours x nombre d'ouvriers
        $jours   = count(dates_entre($f['debut'], $f['fin']));
        $montant = $metier['tarif_journalier'] * $jours * $f['nb'];
        $st = $pdo->prepare('INSERT INTO reservations (client_id, trade_id, nombre_ouvriers, date_debut, date_fin, adresse, description, montant)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $st->execute([$_SESSION['user']['id'], $metier['id'], $f['nb'], $f['debut'], $f['fin'], $f['adresse'], $f['description'], $montant]);
        message("Réservation envoyée ! L'administrateur va la traiter.");
        aller_a('client/reservations.php');
    }
}

require '../includes/header.php';
?>
<h1>Nouvelle réservation</h1>
<p class="discret">Remplissez le formulaire, nous nous occupons du reste.</p>

<div class="carte">
  <form method="post">
    <div class="deux">
      <div class="champ"><label>Métier</label>
        <select name="metier" id="metier" required>
          <option value="" data-tarif="0">-- Choisir --</option>
          <?php foreach ($metiers as $m): ?>
            <option value="<?= (int)$m['id'] ?>" data-tarif="<?= e($m['tarif_journalier']) ?>" <?= $f['metier'] === (int)$m['id'] ? 'selected' : '' ?>>
              <?= icone_metier($m['nom']) ?> <?= e($m['nom']) ?> – <?= prix($m['tarif_journalier']) ?>/jour
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="champ"><label>Nombre d'ouvriers</label><input type="number" id="nb" name="nb" min="1" max="20" value="<?= (int)$f['nb'] ?>" required></div>
      <div class="champ"><label>Date de début</label><input type="date" id="debut" name="debut" min="<?= date('Y-m-d') ?>" value="<?= e($f['debut']) ?>" required></div>
      <div class="champ"><label>Date de fin</label><input type="date" id="fin" name="fin" min="<?= date('Y-m-d') ?>" value="<?= e($f['fin']) ?>" required></div>
    </div>
    <div class="champ"><label>Adresse du lieu de travail</label><input type="text" name="adresse" value="<?= e($f['adresse']) ?>" required></div>
    <div class="champ"><label>Description du travail</label><textarea name="description"><?= e($f['description']) ?></textarea></div>

    <div class="estimation">Prix estimé : <span id="total">0,00 DH</span></div>
    <p class="discret">Prix = tarif journalier × nombre de jours × nombre d'ouvriers. Le paiement se fait en dehors du site.</p>
    <button class="btn" type="submit">Envoyer la réservation</button>
  </form>
</div>

<script>
// Calcul du prix estimé en direct
function calculer() {
  var tarif = parseFloat(document.getElementById('metier').selectedOptions[0].dataset.tarif) || 0;
  var nb    = parseInt(document.getElementById('nb').value) || 0;
  var d1    = new Date(document.getElementById('debut').value);
  var d2    = new Date(document.getElementById('fin').value);
  var jours = (d2 - d1) / 86400000 + 1;
  var total = (jours > 0) ? tarif * nb * jours : 0;
  document.getElementById('total').textContent = total.toFixed(2).replace('.', ',') + ' DH';
}
['metier', 'nb', 'debut', 'fin'].forEach(function (id) {
  document.getElementById(id).addEventListener('change', calculer);
});
calculer();
</script>
<?php require '../includes/footer.php'; ?>
