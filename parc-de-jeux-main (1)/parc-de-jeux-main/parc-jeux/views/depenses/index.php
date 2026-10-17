<?php $titrePage = 'Dépenses'; $role = roleActuel(); ?>
<div class="entete"><h1><?= $role === 'responsable' ? 'Dépenses de mes jeux' : 'Dépenses' ?></h1>
    <?php if ($role === 'admin'): ?><a class="btn" href="<?= e(url('depenses', 'ajouter')) ?>">+ Ajouter une dépense</a><?php endif; ?></div>
<form method="get" class="filtres">
    <input type="hidden" name="page" value="depenses">
    <input type="date" name="date_debut" value="<?= e($filtres['date_debut']) ?>" title="Du">
    <input type="date" name="date_fin" value="<?= e($filtres['date_fin']) ?>" title="Au">
    <select name="id_jeu"><option value="">Tous les jeux</option>
        <?php foreach ($jeux as $j): ?><option value="<?= $j['id_jeu'] ?>" <?= selectionne($filtres['id_jeu'], $j['id_jeu']) ?>><?= e($j['nom']) ?></option><?php endforeach; ?>
    </select>
    <select name="categorie"><option value="">Toutes catégories</option>
        <?php foreach (CATEGORIES_DEPENSE as $cat): ?><option <?= selectionne($filtres['categorie'], $cat) ?>><?= e($cat) ?></option><?php endforeach; ?>
    </select>
    <?php if ($role === 'admin'): ?>
    <select name="type"><option value="">Tous les types</option>
        <option value="jeu" <?= selectionne($filtres['type'], 'jeu') ?>>Liée à un jeu</option>
        <option value="general" <?= selectionne($filtres['type'], 'general') ?>>Frais généraux</option></select>
    <?php endif; ?>
    <button class="btn" type="submit">Filtrer</button>
</form>
<div class="tableau"><table>
    <tr><th>Date</th><th>Catégorie</th><th>Description</th><th>Type</th><th>Jeu</th><th>Montant</th><th>Saisi par</th><th>Justificatif</th></tr>
    <?php foreach ($depenses as $d): ?>
    <tr><td><?= dateFr($d['date_depense']) ?></td><td><?= e($d['categorie']) ?></td><td><?= e($d['description']) ?></td>
        <td><?= e(libelle($d['type'])) ?></td><td><?= e($d['jeu_nom'] ?? '-') ?></td><td><?= montant($d['montant']) ?></td>
        <td><?= e($d['user_prenom'] . ' ' . $d['user_nom']) ?></td><td><?= e($d['justificatif'] ?? '-') ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$depenses): ?><tr><td colspan="8">Aucune dépense.</td></tr><?php endif; ?>
    <tr class="total"><td colspan="5">Total affiché</td><td colspan="3"><?= montant($total) ?></td></tr>
</table></div>
