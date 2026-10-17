<?php $titrePage = 'Jeu'; ?>
<h1><?= $id ? 'Modifier le jeu' : 'Ajouter un jeu' ?></h1>
<?php afficherErreurs($erreurs); ?>
<form method="post" class="formulaire validation">
    <label>Nom *</label><input type="text" name="nom" value="<?= e($jeu['nom']) ?>" required>
    <label>Description</label><textarea name="description" rows="3"><?= e($jeu['description']) ?></textarea>
    <label>Catégorie *</label>
    <select name="categorie">
        <?php foreach (CATEGORIES_JEUX as $cat): ?><option <?= selectionne($jeu['categorie'], $cat) ?>><?= e($cat) ?></option><?php endforeach; ?>
    </select>
    <label>Âge minimum (ans) *</label><input type="number" name="age_minimum" min="0" value="<?= e($jeu['age_minimum']) ?>" required>
    <label>Capacité (personnes) *</label><input type="number" name="capacite" min="1" value="<?= e($jeu['capacite']) ?>" required>
    <label>Durée d'une session (minutes) *</label><input type="number" name="duree_session" min="1" value="<?= e($jeu['duree_session']) ?>" required>
    <label>Tarif par personne (DH) *</label><input type="number" name="tarif" min="0" step="0.01" value="<?= e($jeu['tarif']) ?>" required>
    <label>État *</label>
    <select name="etat">
        <?php foreach (ETATS_JEU as $etat): ?><option value="<?= $etat ?>" <?= selectionne($jeu['etat'], $etat) ?>><?= e(libelle($etat)) ?></option><?php endforeach; ?>
    </select>
    <label>Date de mise en service *</label><input type="date" name="date_mise_service" value="<?= e($jeu['date_mise_service']) ?>" required>
    <label>Responsable affecté</label>
    <select name="id_responsable"><option value="">-- Aucun --</option>
        <?php foreach ($responsables as $r): ?>
            <option value="<?= $r['id_responsable'] ?>" <?= selectionne($jeu['id_responsable'], $r['id_responsable']) ?>><?= e($r['prenom'] . ' ' . $r['nom']) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Enregistrer</button>
    <a class="btn gris" href="<?= e(url('jeux')) ?>">Annuler</a>
</form>
