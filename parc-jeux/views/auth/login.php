<?php $titrePage = $titrePage ?? 'Connexion'; ?>
<div class="login-boite">
    <h1>Connexion</h1>
    <p>Connectez-vous pour gérer vos réservations ou accéder à l'espace interne.</p>
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="message <?= e($_SESSION['flash']['type']) ?>"><?= e($_SESSION['flash']['message']) ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <?php if (!empty($erreur)): ?><div class="message erreur"><?= e($erreur) ?></div><?php endif; ?>
    <form method="post" id="form-login">
        <label>Adresse électronique</label>
        <input type="email" name="email" required autofocus>
        <label>Mot de passe</label>
        <input type="password" name="mot_de_passe" required>
        <button type="submit" class="btn">Se connecter</button>
    </form>
    <p class="lien-alt">Nouveau visiteur ? <a href="<?= e(url('auth', 'inscription')) ?>">Créer un compte</a></p>
    
    <div style="margin-top:2rem; padding:1rem; background: #f1f5f9; border-radius: 8px; font-size: 0.85rem; color:#64748b;">
        <strong>Comptes de démo (mot de passe : password) :</strong><br>
        Admin : admin@parcjeux.test<br>
        Client : youssef@exemple.test<br>
        Agent : agent@parcjeux.test
    </div>
</div>
