<?php $titrePage = 'Compte utilisateur'; ?>
<h1><?= $id ? 'Modifier le compte' : 'Ajouter un compte' ?></h1>
<?php afficherErreurs($erreurs); ?>
<form method="post" class="formulaire validation">
    <label>Nom *</label><input type="text" name="nom" value="<?= e($compte['nom']) ?>" required>
    <label>Prénom *</label><input type="text" name="prenom" value="<?= e($compte['prenom']) ?>" required>
    <label>Adresse électronique *</label><input type="email" name="email" value="<?= e($compte['email']) ?>" required>
    <label>Mot de passe <?= $id ? '(laisser vide pour ne pas changer)' : '*' ?></label>
    <input type="password" name="mot_de_passe" minlength="6" <?= $id ? '' : 'required' ?>>
    <label>Rôle *</label>
    <select name="role" id="role">
        <?php foreach (['admin', 'responsable', 'agent', 'client'] as $r): ?>
            <option value="<?= $r ?>" <?= selectionne($compte['role'], $r) ?>><?= e(libelle($r)) ?></option>
        <?php endforeach; ?>
    </select>
    <div id="bloc-responsable">
        <label>Fiche responsable liée</label>
        <select name="id_responsable"><option value="">-- Choisir --</option>
            <?php foreach ($responsables as $r): ?>
                <option value="<?= $r['id_responsable'] ?>" <?= selectionne($compte['id_responsable'], $r['id_responsable']) ?>><?= e($r['prenom'] . ' ' . $r['nom']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div id="bloc-client">
        <label>Fiche client liée</label>
        <select name="id_client"><option value="">-- Choisir --</option>
            <?php foreach ($clients as $c): ?>
                <option value="<?= $c['id_client'] ?>" <?= selectionne($compte['id_client'], $c['id_client']) ?>><?= e($c['prenom'] . ' ' . $c['nom']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="btn" type="submit">Enregistrer</button>
    <a class="btn gris" href="<?= e(url('utilisateurs')) ?>">Annuler</a>
</form>
