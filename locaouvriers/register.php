<?php
require 'includes/fonctions.php';
$titre = 'Inscription';

if (isset($_SESSION['user'])) {
    aller_a(accueil_role($_SESSION['user']['role']));
}

$nom = $email = $tel = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom   = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tel   = trim($_POST['telephone'] ?? '');
    $mdp   = $_POST['password'] ?? '';

    if ($nom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        message('Nom et email valides obligatoires.', 'err');
    } elseif (strlen($mdp) < 6) {
        message('Le mot de passe doit contenir au moins 6 caractères.', 'err');
    } elseif ($mdp !== ($_POST['password2'] ?? '')) {
        message('Les deux mots de passe sont différents.', 'err');
    } elseif (valeur('SELECT COUNT(*) FROM users WHERE email = ?', [$email]) > 0) {
        message('Cet email est déjà utilisé.', 'err');
    } else {
        // Le mot de passe est toujours enregistré "haché", jamais en clair
        $st = $pdo->prepare("INSERT INTO users (nom, email, mot_de_passe, telephone, role) VALUES (?, ?, ?, ?, 'client')");
        $st->execute([$nom, $email, password_hash($mdp, PASSWORD_DEFAULT), $tel]);
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$pdo->lastInsertId(), 'nom' => $nom, 'role' => 'client'];
        message('Bienvenue ! Votre compte a été créé avec succès.');
        aller_a('client/reserver.php');
    }
}

require 'includes/header.php';
?>
<div class="carte etroit" style="border-top: 4px solid var(--jaune);">
  <div style="text-align:center; margin-bottom: 20px;">
    <span style="font-size: 38px;">✨</span>
    <h1 style="margin-top: 8px;">Créer un compte Client</h1>
    <p class="discret">Rejoignez LocaOuvriers et réservez des artisans certifiés en 2 minutes.</p>
  </div>

  <form method="post">
    <div class="champ">
      <label>Nom complet</label>
      <input type="text" name="nom" value="<?= e($nom) ?>" placeholder="ex: Mohammed Alami" required autofocus>
    </div>
    <div class="champ">
      <label>Email professionnel ou personnel</label>
      <input type="email" name="email" value="<?= e($email) ?>" placeholder="ex: mohammed@email.com" required>
    </div>
    <div class="champ">
      <label>Numéro de Téléphone (pour contact ouvrier)</label>
      <input type="tel" name="telephone" value="<?= e($tel) ?>" placeholder="ex: 06 12 34 56 78" required>
    </div>
    <div class="deux">
      <div class="champ">
        <label>Mot de passe (6 car. min.)</label>
        <input type="password" name="password" placeholder="••••••••" required>
      </div>
      <div class="champ">
        <label>Confirmer</label>
        <input type="password" name="password2" placeholder="••••••••" required>
      </div>
    </div>
    <button class="btn" type="submit" style="width:100%; font-size:16px; padding: 13px;">Créer mon compte & Réserver →</button>
  </form>

  <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--gris-bordure); text-align:center; font-size: 14px;">
    <p class="discret">
      Déjà inscrit ? <a href="<?= BASE ?>/login.php" style="color: var(--orange-vif); font-weight: 700;">Connectez-vous ici</a>
    </p>
    <div style="margin-top: 12px; display:inline-flex; align-items:center; gap:6px; font-size:12px; color:var(--gris); background:var(--fond); padding:4px 10px; border-radius:12px;">
      <span>🛡️</span> Vos données sont 100% sécurisées et confidentielles
    </div>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
