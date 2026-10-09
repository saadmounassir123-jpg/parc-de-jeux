<?php $titrePage = 'Type de billet'; ?>
<h1><?= $id ? 'Modifier le type de billet' : 'Ajouter un type de billet' ?></h1>
<?php afficherErreurs($erreurs); ?>
<form method="post" class="formulaire validation">
    <label>Nom *</label><input type="text" name="nom" value="<?= e($type['nom']) ?>" required>
    <label>Tarif (DH) *</label><input type="number" name="tarif" min="0" step="0.01" value="<?= e($type['tarif']) ?>" required>
    <label>Durée de validité (jours) *</label><input type="number" name="duree_validite" min="1" value="<?= e($type['duree_validite']) ?>" required>
    <button class="btn" type="submit">Enregistrer</button>
    <a class="btn gris" href="<?= e(url('billets', 'types')) ?>">Annuler</a>
</form>
