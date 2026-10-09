<?php $titrePage = 'Nouvelle réservation'; $role = roleActuel(); ?>
<h1>Nouvelle réservation</h1>
<?php bandeauBilletterie(); ?>
<?php afficherErreurs($erreurs); ?>
<form method="post" class="formulaire validation">
    <?php if (in_array($role, ['admin', 'agent'], true)): ?>
    <label>Client * <a href="<?= e(url('clients', 'ajouter')) ?>">(nouveau client)</a></label>
    <select name="id_client" required><option value="">-- Choisir --</option>
        <?php foreach ($clients as $c): ?><option value="<?= $c['id_client'] ?>" <?= selectionne($form['id_client'], $c['id_client']) ?>><?= e($c['prenom'] . ' ' . $c['nom']) ?> (<?= e(libelle($c['type_client'])) ?>)</option><?php endforeach; ?>
    </select>
    <?php endif; ?>
    <label>Date de visite *</label>
    <input type="date" name="date_visite" min="<?= date('Y-m-d') ?>" value="<?= e($form['date_visite']) ?>" required>
    <label>Nombre de personnes (famille ou groupe) *</label>
    <input type="number" id="nb-personnes" name="nombre_personnes" min="1" value="<?= e($form['nombre_personnes']) ?>" required>
    <label>Jeux choisis *</label>
    <?php foreach ($jeux as $j): ?>
        <label class="case">
            <input type="checkbox" class="jeu-choisi" name="jeux[]" value="<?= (int)$j['id_jeu'] ?>" data-prix="<?= e($j['tarif']) ?>"
                   <?= in_array($j['id_jeu'], $form['jeux']) ? 'checked' : '' ?>>
            <span><?= e($j['nom']) ?> — <?= montant($j['tarif']) ?>/pers. <small>(capacité <?= (int)$j['capacite'] ?>, âge min. <?= (int)$j['age_minimum'] ?> ans)</small></span>
        </label>
    <?php endforeach; ?>
    <?php if (!$jeux): ?><p class="aide">Aucun jeu n’est actuellement réservable.</p><?php endif; ?>
    <p>Montant estimé (à régler à la billetterie) : <strong id="total-resa">0,00 DH</strong></p>
    <button class="btn" type="submit">Réserver</button>
    <a class="btn gris" href="<?= e(estPersonnel() ? url('reservations') : url('accueil')) ?>">Annuler</a>
</form>
