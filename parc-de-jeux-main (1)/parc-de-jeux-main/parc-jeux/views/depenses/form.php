<?php $titrePage = 'Nouvelle dépense'; ?>
<h1>Ajouter une dépense</h1>
<?php afficherErreurs($erreurs); ?>
<form method="post" class="formulaire validation">
    <label>Type de dépense *</label>
    <select name="type" id="type-depense">
        <option value="jeu" <?= selectionne($d['type'], 'jeu') ?>>Dépense liée à un jeu</option>
        <option value="general" <?= selectionne($d['type'], 'general') ?>>Frais général du parc</option>
    </select>
    <div id="bloc-jeu">
        <label>Jeu concerné *</label>
        <select name="id_jeu"><option value="">-- Choisir --</option>
            <?php foreach ($jeux as $j): ?><option value="<?= $j['id_jeu'] ?>" <?= selectionne($d['id_jeu'], $j['id_jeu']) ?>><?= e($j['nom']) ?></option><?php endforeach; ?>
        </select>
    </div>
    <label>Catégorie *</label>
    <select name="categorie"><?php foreach (CATEGORIES_DEPENSE as $cat): ?><option <?= selectionne($d['categorie'], $cat) ?>><?= e($cat) ?></option><?php endforeach; ?></select>
    <label>Description *</label><input type="text" name="description" value="<?= e($d['description']) ?>" required>
    <label>Montant (DH) *</label><input type="number" name="montant" min="0.01" step="0.01" value="<?= e($d['montant']) ?>" required>
    <label>Date *</label><input type="date" name="date_depense" value="<?= e($d['date_depense']) ?>" required>
    <label>Justificatif (référence de facture, facultatif)</label><input type="text" name="justificatif" value="<?= e($d['justificatif']) ?>">
    <button class="btn" type="submit">Enregistrer</button>
    <a class="btn gris" href="<?= e(url('depenses')) ?>">Annuler</a>
</form>
