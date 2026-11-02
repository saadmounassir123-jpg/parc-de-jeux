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
        message('Bienvenue ! Votre compte a été créé.');
        aller_a('client/reservations.php');
    }
}

require 'includes/header.php';
?>
<div class="carte etroit">
  <h1>Créer un compte</h1>
  <form method="post">
    <div class="champ"><label>Nom complet</label><input type="text" name="nom" value="<?= e($nom) ?>" required></div>
    <div class="champ"><label>Email</label><input type="email" name="email" value="<?= e($email) ?>" required></div>
    <div class="champ"><label>Téléphone</label><input type="tel" name="telephone" value="<?= e($tel) ?>"></div>
    <div class="champ"><label>Mot de passe (6 caractères min.)</label><input type="password" name="password" required></div>
    <div class="champ"><label>Confirmer le mot de passe</label><input type="password" name="password2" required></div>
    <button class="btn" type="submit" style="width:100%">S'inscrire</button>
  </form>
</div>
<?php require 'includes/footer.php'; ?>
