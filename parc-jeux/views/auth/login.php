<?php $titrePage = $titrePage ?? 'Connexion'; ?>
<div class="login-boite">
    <h1>🎡 FunPark</h1>
    <p>Connectez-vous pour gérer vos réservations ou accéder à l’espace interne.</p>
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="message <?= e($_SESSION['flash']['type']) ?>"><?= e($_SESSION['flash']['message']) ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <?php if (!empty($erreur)): ?><div class="message erreur"><?= e($erreur) ?></div><?php endif; ?>
    <form method="post" class="validation">
        <label>Adresse électronique</label>
        <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
        <label>Mot de passe</label>
        <input type="password" name="mot_de_passe" required>
        <button type="submit" class="btn">Se connecter</button>
    </form>
    <p class="lien-alt">Pas encore de compte ? <a href="<?= e(url('auth', 'inscription')) ?>">Créer un compte visiteur</a></p>
    <p class="aide">Comptes de démonstration (mot de passe : <b>password</b>) :<br>
        admin@parcjeux.test · karim@parcjeux.test · agent@parcjeux.test · youssef@exemple.test</p>
</div>
