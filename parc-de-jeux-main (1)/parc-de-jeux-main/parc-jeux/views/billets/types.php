<?php $titrePage = 'Tarifs'; $role = roleActuel(); ?>
<div class="entete"><h1>Types de billets et tarifs</h1>
    <?php if ($role === 'admin'): ?><a class="btn" href="<?= e(url('billets', 'typeAjouter')) ?>">+ Ajouter un type</a><?php endif; ?></div>
<?php if ($role !== 'admin') bandeauBilletterie(); ?>

<?php if (!estPersonnel()): ?>
<div class="grille-tarifs">
    <?php foreach ($types as $t): ?>
    <article class="carte-tarif">
        <div class="tarif-icon">🎫</div>
        <h3><?= e($t['nom']) ?></h3>
        <strong class="tarif-montant"><?= montant($t['tarif']) ?></strong>
        <span class="tarif-validite">Valable <?= (int)$t['duree_validite'] ?> jour(s)</span>
        <div style="margin-top:12px;">
            <a class="btn btn-sm" href="<?= e(url('achats', 'billetterie')) ?>">Acheter en ligne</a>
        </div>
    </article>
    <?php endforeach; ?>
</div>
<div style="margin-top:24px; text-align:center;">
    <a class="btn btn-hero" href="<?= e(url('achats', 'billetterie')) ?>">🎟️ Commander sur notre Billetterie en ligne</a>
    <a class="btn gris" href="<?= e(url('promotions')) ?>">🎁 Découvrir nos codes promo</a>
</div>
<p class="aide" style="margin-top:20px; text-align:center;">
    Achetez vos billets en ligne en quelques clics avec paiement sécurisé (CB ou au guichet) pour éviter l'attente à l'entrée du parc.
</p>
<?php else: ?>
<div class="tableau"><table>
    <tr><th>Type</th><th>Tarif</th><th>Validité</th><?php if ($role === 'admin'): ?><th>Actions</th><?php endif; ?></tr>
    <?php foreach ($types as $t): ?>
    <tr><td><?= e($t['nom']) ?></td><td><?= montant($t['tarif']) ?></td><td><?= (int)$t['duree_validite'] ?> jour(s)</td>
        <?php if ($role === 'admin'): ?><td class="actions"><a href="<?= e(url('billets', 'typeModifier', ['id' => $t['id_type_billet']])) ?>">Modifier</a></td><?php endif; ?></tr>
    <?php endforeach; ?>
</table></div>
<?php endif; ?>
