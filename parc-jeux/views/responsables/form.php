<?php $titrePage = 'Responsable'; ?>
<h1><?= $id ? 'Modifier le responsable' : 'Créer un responsable' ?></h1>
<?php afficherErreurs($erreurs); ?>
<form method="post" class="formulaire validation">
    <label>Nom *</label><input type="text" name="nom" value="<?= e($r['nom']) ?>" required>
    <label>Prénom *</label><input type="text" name="prenom" value="<?= e($r['prenom']) ?>" required>
    <label>Téléphone</label><input type="text" name="telephone" value="<?= e($r['telephone']) ?>">
    <label>Adresse électronique</label><input type="email" name="email" value="<?= e($r['email']) ?>">
    <label>Fonction</label><input type="text" name="fonction" value="<?= e($r['fonction']) ?>">
    <label>Date d'embauche *</label><input type="date" name="date_embauche" value="<?= e($r['date_embauche']) ?>" required>
    <label>Statut</label>
    <select name="statut">
        <option value="actif" <?= selectionne($r['statut'], 'actif') ?>>Actif</option>
        <option value="inactif" <?= selectionne($r['statut'], 'inactif') ?>>Inactif</option>
    </select>
    <button class="btn" type="submit">Enregistrer</button>
    <a class="btn gris" href="<?= e(url('responsables')) ?>">Annuler</a>
</form>
