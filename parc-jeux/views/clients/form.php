<?php $titrePage = 'Client'; ?>
<h1><?= $id ? 'Modifier le client' : 'Enregistrer un client' ?></h1>
<?php afficherErreurs($erreurs); ?>
<form method="post" class="formulaire validation">
    <label>Nom *</label><input type="text" name="nom" value="<?= e($c['nom']) ?>" required>
    <label>Prénom *</label><input type="text" name="prenom" value="<?= e($c['prenom']) ?>" required>
    <label>Téléphone</label><input type="text" name="telephone" value="<?= e($c['telephone']) ?>">
    <label>Adresse électronique</label><input type="email" name="email" value="<?= e($c['email']) ?>">
    <label>Type de client (famille / groupe pour les réservations collectives)</label>
    <select name="type_client">
        <?php foreach (['individuel', 'famille', 'groupe'] as $t): ?><option value="<?= $t ?>" <?= selectionne($c['type_client'], $t) ?>><?= e(libelle($t)) ?></option><?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Enregistrer</button>
    <a class="btn gris" href="<?= e(url('clients')) ?>">Annuler</a>
</form>
