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
<div class="carte etroit">
  <h1>Connexion</h1>
  <p class="discret" style="text-align:center">Heureux de vous revoir 👋</p>
  <form method="post">
    <div class="champ"><label>Email</label><input type="email" name="email" value="<?= e($email) ?>" required autofocus></div>
    <div class="champ"><label>Mot de passe</label><input type="password" name="password" required></div>
    <button class="btn" type="submit" style="width:100%">Se connecter</button>
  </form>
  <p class="discret" style="text-align:center">Pas de compte ? <a href="<?= BASE ?>/register.php">Inscrivez-vous</a><br>
  Mot de passe perdu ? Contactez l'administrateur.</p>
</div>
<?php require 'includes/footer.php'; ?>
