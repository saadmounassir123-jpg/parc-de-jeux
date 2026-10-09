<?php $titrePage = 'Intervention'; ?>
<h1><?= $id ? "Modifier l'intervention" : 'Ajouter une intervention' ?></h1>
<?php afficherErreurs($erreurs); ?>
<form method="post" class="formulaire validation">
    <label>Jeu concerné *</label>
    <select name="id_jeu" required><option value="">-- Choisir --</option>
        <?php foreach ($jeux as $j): ?><option value="<?= $j['id_jeu'] ?>" <?= selectionne($m['id_jeu'], $j['id_jeu']) ?>><?= e($j['nom']) ?></option><?php endforeach; ?>
    </select>
    <label>Type d'intervention *</label><input type="text" name="type_intervention" value="<?= e($m['type_intervention']) ?>" placeholder="Réparation, contrôle, nettoyage..." required>
    <label>Description</label><textarea name="description" rows="3"><?= e($m['description']) ?></textarea>
    <label>Date de l'intervention *</label><input type="date" name="date_intervention" value="<?= e($m['date_intervention']) ?>" required>
    <label>Coût (DH)</label><input type="number" name="cout" min="0" step="0.01" value="<?= e($m['cout']) ?>">
    <label>Responsable</label>
    <select name="id_responsable"><option value="">-- Aucun --</option>
        <?php foreach ($responsables as $r): ?><option value="<?= $r['id_responsable'] ?>" <?= selectionne($m['id_responsable'], $r['id_responsable']) ?>><?= e($r['prenom'] . ' ' . $r['nom']) ?></option><?php endforeach; ?>
    </select>
    <label>État de l'intervention *</label>
    <select name="etat"><?php foreach (['signalee', 'planifiee', 'en_cours', 'terminee'] as $s): ?><option value="<?= $s ?>" <?= selectionne($m['etat'], $s) ?>><?= e(libelle($s)) ?></option><?php endforeach; ?></select>
    <label>Date de prochaine maintenance</label><input type="date" name="date_prochaine" value="<?= e($m['date_prochaine']) ?>">
    <small>« En cours » met le jeu en maintenance ; « Terminée » le remet disponible.</small>
    <button class="btn" type="submit">Enregistrer</button>
    <a class="btn gris" href="<?= e(url('maintenance')) ?>">Annuler</a>
</form>
