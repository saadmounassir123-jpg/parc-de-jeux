<?php $titrePage = 'Tarifs'; $role = roleActuel(); ?>
<div class="entete"><h1>Types de billets et tarifs</h1>
    <?php if ($role === 'admin'): ?><a class="btn" href="<?= e(url('billets', 'typeAjouter')) ?>">+ Ajouter un type</a><?php endif; ?></div>
<?php if ($role !== 'admin') bandeauBilletterie(); ?>

<?php if (!estPersonnel()): ?>
<div class="grille-tarifs">
    <?php foreach ($types as $t): ?>
    <article class="carte-tarif">
        <span><?= e($t['nom']) ?></span>
        <strong><?= montant($t['tarif']) ?></strong>
        <small>Valable <?= (int)$t['duree_validite'] ?> jour(s)</small>
    </article>
    <?php endforeach; ?>
</div>
<p class="aide" style="margin-top:20px">Ces tarifs sont indicatifs. Les billets s’achètent à la billetterie du parc, pas en ligne.</p>
<?php else: ?>
<div class="tableau"><table>
    <tr><th>Type</th><th>Tarif</th><th>Validité</th><?php if ($role === 'admin'): ?><th>Actions</th><?php endif; ?></tr>
    <?php foreach ($types as $t): ?>
    <tr><td><?= e($t['nom']) ?></td><td><?= montant($t['tarif']) ?></td><td><?= (int)$t['duree_validite'] ?> jour(s)</td>
        <?php if ($role === 'admin'): ?><td class="actions"><a href="<?= e(url('billets', 'typeModifier', ['id' => $t['id_type_billet']])) ?>">Modifier</a></td><?php endif; ?></tr>
    <?php endforeach; ?>
</table></div>
<?php endif; ?>
