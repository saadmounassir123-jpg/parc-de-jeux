<?php
require '../includes/fonctions.php';
exiger_role('admin');
$titre = 'Ouvriers';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'ajouter') {
        $nom   = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mdp   = $_POST['password'] ?? '';
        $tel   = trim($_POST['telephone'] ?? '');
        $metier  = (int)($_POST['metier'] ?? 0);
        $spec    = trim($_POST['specialites'] ?? '');
        $salaire = (float)str_replace(',', '.', $_POST['salaire'] ?? '0');

        if ($nom === '' || $salaire <= 0 || valeur('SELECT COUNT(*) FROM trades WHERE id = ?', [$metier]) == 0) {
            message('Nom, métier et salaire valides obligatoires.', 'err');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($mdp) < 6) {
            message('Email valide et mot de passe (6 caractères min.) obligatoires.', 'err');
        } elseif (valeur('SELECT COUNT(*) FROM users WHERE email = ?', [$email]) > 0) {
            message('Cet email est déjà utilisé.', 'err');
        } else {
            // Un ouvrier = un compte utilisateur + une fiche ouvrier
            $pdo->beginTransaction();
            $pdo->prepare("INSERT INTO users (nom, email, mot_de_passe, telephone, role) VALUES (?, ?, ?, ?, 'ouvrier')")
                ->execute([$nom, $email, password_hash($mdp, PASSWORD_DEFAULT), $tel]);
            $user_id = $pdo->lastInsertId();
            $pdo->prepare('INSERT INTO workers (user_id, trade_id, specialites, salaire_mensuel) VALUES (?, ?, ?, ?)')
                ->execute([$user_id, $metier, $spec, $salaire]);
            $pdo->commit();
            message('Ouvrier ajouté.');
        }
    }

    if ($action === 'basculer') {
        $pdo->prepare("UPDATE workers SET statut = IF(statut = 'actif', 'inactif', 'actif') WHERE id = ?")
            ->execute([(int)($_POST['id'] ?? 0)]);
        message("Statut de l'ouvrier mis à jour.");
    }
    aller_a('admin/ouvriers.php');
}

$metiers  = $pdo->query('SELECT * FROM trades ORDER BY nom')->fetchAll();
$ouvriers = $pdo->query('SELECT w.*, u.nom, u.email, u.telephone, t.nom AS metier
                         FROM workers w JOIN users u ON u.id = w.user_id JOIN trades t ON t.id = w.trade_id
                         ORDER BY u.nom')->fetchAll();

require '../includes/header.php';
?>
<h1>Ouvriers</h1>
<p class="discret">Les ouvriers sont les employés de la société, créés par l'administrateur.</p>

<div class="tableau">
<table>
  <tr><th>Nom</th><th>Métier</th><th>Contact</th><th>Salaire mensuel</th><th>Statut</th><th></th></tr>
  <?php foreach ($ouvriers as $o): ?>
  <tr>
    <td><b><?= e($o['nom']) ?></b><br><span class="discret"><?= e($o['specialites']) ?></span></td>
    <td><?= icone_metier($o['metier']) ?> <?= e($o['metier']) ?></td>
    <td><?= e($o['email']) ?><br><?= e($o['telephone']) ?></td>
    <td><?= prix($o['salaire_mensuel']) ?></td>
    <td><?= badge($o['statut']) ?></td>
    <td>
      <form method="post" class="en-ligne">
        <input type="hidden" name="action" value="basculer"><input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
        <button class="btn sombre petit"><?= $o['statut'] === 'actif' ? 'Désactiver' : 'Activer' ?></button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
</div>

<h2>Ajouter un ouvrier</h2>
<div class="carte">
  <form method="post">
    <input type="hidden" name="action" value="ajouter">
    <div class="deux">
      <div class="champ"><label>Nom complet</label><input type="text" name="nom" required></div>
      <div class="champ"><label>Métier</label>
        <select name="metier" required>
          <option value="">-- Choisir --</option>
          <?php foreach ($metiers as $m): ?><option value="<?= (int)$m['id'] ?>"><?= e($m['nom']) ?></option><?php endforeach; ?>
        </select></div>
      <div class="champ"><label>Email (identifiant de connexion)</label><input type="email" name="email" required></div>
      <div class="champ"><label>Mot de passe (6 caractères min.)</label><input type="password" name="password" required></div>
      <div class="champ"><label>Téléphone</label><input type="tel" name="telephone"></div>
      <div class="champ"><label>Salaire mensuel (DH)</label><input type="number" name="salaire" min="1" step="0.01" required></div>
    </div>
    <div class="champ"><label>Spécialités</label><input type="text" name="specialites"></div>
    <button class="btn" type="submit">Ajouter l'ouvrier</button>
  </form>
</div>
<?php require '../includes/footer.php'; ?>
