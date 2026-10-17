<?php $titrePage = 'Maintenance'; $role = roleActuel(); ?>
<div class="entete"><h1>Maintenance</h1>
    <div><a class="btn orange" href="<?= e(url('maintenance', 'signaler')) ?>">Signaler une panne</a>
    <?php if ($role === 'admin'): ?><a class="btn" href="<?= e(url('maintenance', 'ajouter')) ?>">+ Ajouter une intervention</a><?php endif; ?></div></div>
<form method="get" class="filtres">
    <input type="hidden" name="page" value="maintenance">
    <select name="id_jeu"><option value="">Tous les jeux</option>
        <?php foreach ($jeux as $j): ?><option value="<?= $j['id_jeu'] ?>" <?= selectionne($filtres['id_jeu'], $j['id_jeu']) ?>><?= e($j['nom']) ?></option><?php endforeach; ?>
    </select>
    <select name="etat"><option value="">Tous les états</option>
        <?php foreach (['signalee', 'planifiee', 'en_cours', 'terminee'] as $s): ?><option value="<?= $s ?>" <?= selectionne($filtres['etat'], $s) ?>><?= e(libelle($s)) ?></option><?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Filtrer</button>
</form>
<div class="tableau"><table>
    <tr><th>Jeu</th><th>Type</th><th>Description</th><th>Date</th><th>Coût</th><th>Responsable</th><th>État</th><th>Prochaine</th><?php if ($role === 'admin'): ?><th>Actions</th><?php endif; ?></tr>
    <?php foreach ($maintenances as $m): ?>
    <tr><td><?= e($m['jeu_nom']) ?></td><td><?= e($m['type_intervention']) ?></td><td><?= e($m['description']) ?></td><td><?= dateFr($m['date_intervention']) ?></td>
        <td><?= montant($m['cout']) ?></td><td><?= $m['resp_nom'] ? e($m['resp_prenom'] . ' ' . $m['resp_nom']) : '-' ?></td>
        <td><?= badge($m['etat']) ?></td><td><?= dateFr($m['date_prochaine']) ?></td>
        <?php if ($role === 'admin'): ?><td class="actions"><a href="<?= e(url('maintenance', 'modifier', ['id' => $m['id_maintenance']])) ?>">Modifier</a></td><?php endif; ?></tr>
    <?php endforeach; ?>
    <?php if (!$maintenances): ?><tr><td colspan="9">Aucune intervention.</td></tr><?php endif; ?>
</table></div>
<p class="aide">Le coût d'une intervention est indicatif : seules les dépenses enregistrées dans le module « Dépenses » comptent dans le résultat financier.</p>
