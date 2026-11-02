<?php
require 'includes/fonctions.php';
$titre = 'Connexion';

if (isset($_SESSION['user'])) {
    aller_a(accueil_role($_SESSION['user']['role']));
}

$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $st = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $st->execute([$email]);
    $u = $st->fetch();

    if (!$u || !password_verify($_POST['password'] ?? '', $u['mot_de_passe'])) {
        message('Email ou mot de passe incorrect.', 'err');
    } elseif ($u['statut'] !== 'actif') {
        message("Votre compte est suspendu. Contactez l'administrateur.", 'err');
    } else {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$u['id'], 'nom' => $u['nom'], 'role' => $u['role']];
        aller_a(accueil_role($u['role']));
    }
}

require 'includes/header.php';
?>
<div class="carte etroit" style="border-top: 4px solid var(--orange);">
  <div style="text-align:center; margin-bottom: 20px;">
    <span style="font-size: 38px;">🔐</span>
    <h1 style="margin-top: 8px;">Espace Connexion</h1>
    <p class="discret">Heureux de vous revoir ! Accédez à vos réservations et devis.</p>
  </div>

  <form method="post">
    <div class="champ">
      <label>Adresse Email</label>
      <input type="email" name="email" value="<?= e($email) ?>" placeholder="ex: client@site.com" required autofocus>
    </div>
    <div class="champ">
      <label>Mot de passe</label>
      <input type="password" name="password" placeholder="Votre mot de passe" required>
    </div>
    <button class="btn" type="submit" style="width:100%; font-size:16px; padding: 13px;">Se connecter à mon compte →</button>
  </form>

  <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--gris-bordure); text-align:center; font-size: 14px;">
    <p class="discret">
      Vous n'avez pas encore de compte ? <br>
      <a href="<?= BASE ?>/register.php" style="color: var(--orange-vif); font-weight: 700;">Créer un compte gratuitement</a>
    </p>
    <div style="margin-top: 14px; display:inline-flex; align-items:center; gap:6px; font-size:12px; color:var(--gris); background:var(--fond); padding:4px 10px; border-radius:12px;">
      <span>🔒</span> Connexion sécurisée SSL 256-bit
    </div>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
