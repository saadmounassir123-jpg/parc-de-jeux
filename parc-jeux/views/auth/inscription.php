<?php $titrePage = $titrePage ?? 'Inscription'; ?>
<div class="login-boite">
    <h1>Créer un compte</h1>
    <p>Inscrivez-vous pour finaliser une réservation. L'achat des billets se fait à la billetterie du parc.</p>
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="message <?= e($_SESSION['flash']['type']) ?>"><?= e($_SESSION['flash']['message']) ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <?php afficherErreurs($erreurs); ?>
    <form method="post" class="validation" id="form-inscription">
        <label>Nom *</label>
        <input type="text" name="nom" value="<?= e($form['nom']) ?>" required>
        <label>Prénom *</label>
        <input type="text" name="prenom" value="<?= e($form['prenom']) ?>" required>
        <label>Téléphone *</label>
        <input type="text" name="telephone" value="<?= e($form['telephone']) ?>" required>
        <label>Adresse électronique *</label>
        <input type="email" name="email" value="<?= e($form['email']) ?>" required>
        <label>Mot de passe * (6 caractères minimum)</label>
        <input type="password" name="mot_de_passe" minlength="6" required>
        <label>Confirmation du mot de passe *</label>
        <input type="password" name="mot_de_passe_confirmation" minlength="6" required>
        <button type="submit" class="btn">Créer mon compte</button>
    </form>
    <p class="lien-alt">Déjà inscrit ? <a href="<?= e(url('auth', 'login')) ?>">Se connecter</a></p>
</div>
