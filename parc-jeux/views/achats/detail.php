<?php $titrePage = 'Billetterie'; $role = roleActuel(); ?>
<div class="entete"><h1>Vente <?= e($achat['numero_commande']) ?> <?= badge($achat['statut_paiement']) ?></h1>
    <a class="btn gris" href="<?= e(url('billets', 'achats')) ?>">Retour à la billetterie</a></div>
<div class="carte"><dl class="details">
    <dt>Client</dt><dd><?= e($achat['prenom'] . ' ' . $achat['nom']) ?></dd>
    <dt>Date</dt><dd><?= dateFr($achat['date_achat']) ?></dd>
    <dt>Mode de paiement</dt><dd><?= e(libelle($achat['mode_paiement'])) ?></dd>
    <dt>Montant total</dt><dd><strong><?= montant($achat['montant_total']) ?></strong></dd>
</dl></div>
<?php if ($achat['statut_paiement'] === 'en_attente' && in_array($role, ['admin', 'agent'])): ?>
<form method="post" action="<?= e(url('billets', 'payer')) ?>" class="filtres" data-confirm="Valider le paiement de cette commande ?">
    <input type="hidden" name="id" value="<?= (int)$achat['id_achat'] ?>">
    <select name="mode_paiement"><option value="especes">Espèces</option><option value="carte">Carte</option></select>
    <button class="btn" type="submit">Valider le paiement</button>
</form>
<?php endif; ?>
<h2>Billets</h2>
<div class="tableau"><table>
    <tr><th>Code unique</th><th>Type</th><th>Prix</th><th>Statut</th></tr>
    <?php foreach ($billets as $b): ?>
    <tr><td><?= e($b['code_unique']) ?></td><td><?= e($b['type_nom']) ?></td><td><?= montant($b['prix']) ?></td><td><?= badge($b['statut']) ?></td></tr>
    <?php endforeach; ?>
</table></div>
