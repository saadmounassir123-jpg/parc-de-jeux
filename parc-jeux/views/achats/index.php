<?php $titrePage = 'Billetterie'; ?>
<div class="entete"><h1>Billetterie du parc</h1>
    <a class="btn" href="<?= e(url('billets', 'vendre')) ?>">+ Nouvelle vente</a></div>
<p class="aide">Vente de billets au guichet (espèces ou carte). Le total est calculé automatiquement à partir des tarifs.</p>
<form method="get" class="filtres">
    <input type="hidden" name="page" value="billets"><input type="hidden" name="action" value="achats">
    <input type="text" name="q" placeholder="Client ou n° de commande" value="<?= e($filtres['q']) ?>">
    <input type="date" name="date_debut" value="<?= e($filtres['date_debut']) ?>" title="Du">
    <input type="date" name="date_fin" value="<?= e($filtres['date_fin']) ?>" title="Au">
    <select name="statut"><option value="">Tous les paiements</option>
        <option value="paye" <?= selectionne($filtres['statut'], 'paye') ?>>Payé</option>
        <option value="en_attente" <?= selectionne($filtres['statut'], 'en_attente') ?>>En attente</option></select>
    <button class="btn" type="submit">Filtrer</button>
</form>
<div class="tableau"><table>
    <tr><th>Commande</th><th>Date</th><th>Client</th><th>Billets</th><th>Montant total</th><th>Mode</th><th>Paiement</th></tr>
    <?php foreach ($achats as $a): ?>
    <tr><td><a href="<?= e(url('billets', 'detailAchat', ['id' => $a['id_achat']])) ?>"><?= e($a['numero_commande']) ?></a></td>
        <td><?= dateFr($a['date_achat']) ?></td><td><?= e($a['prenom'] . ' ' . $a['nom']) ?></td><td><?= (int)$a['nb_billets'] ?></td>
        <td><?= montant($a['montant_total']) ?></td><td><?= e(libelle($a['mode_paiement'])) ?></td><td><?= badge($a['statut_paiement']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$achats): ?><tr><td colspan="7">Aucune vente.</td></tr><?php endif; ?>
</table></div>
