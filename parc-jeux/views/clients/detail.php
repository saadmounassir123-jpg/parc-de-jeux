<?php $titrePage = 'Client'; ?>
<div class="entete"><h1><?= e($c['prenom'] . ' ' . $c['nom']) ?></h1>
    <div><a class="btn" href="<?= e(url('clients', 'modifier', ['id' => $c['id_client']])) ?>">Modifier</a>
         <a class="btn gris" href="<?= e(url('clients')) ?>">Retour</a></div></div>
<div class="carte"><dl class="details">
    <dt>Téléphone</dt><dd><?= e($c['telephone']) ?></dd>
    <dt>Email</dt><dd><?= e($c['email']) ?></dd>
    <dt>Type</dt><dd><?= e(libelle($c['type_client'])) ?></dd>
</dl></div>
<h2>Achats</h2>
<div class="tableau"><table>
    <tr><th>Commande</th><th>Date</th><th>Billets</th><th>Montant</th><th>Paiement</th></tr>
    <?php foreach ($achats as $a): ?>
    <tr><td><a href="<?= e(url('billets', 'detailAchat', ['id' => $a['id_achat']])) ?>"><?= e($a['numero_commande']) ?></a></td>
        <td><?= dateFr($a['date_achat']) ?></td><td><?= (int)$a['nb_billets'] ?></td><td><?= montant($a['montant_total']) ?></td><td><?= badge($a['statut_paiement']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$achats): ?><tr><td colspan="5">Aucun achat.</td></tr><?php endif; ?>
</table></div>
<h2>Réservations</h2>
<div class="tableau"><table>
    <tr><th>N°</th><th>Visite</th><th>Jeux</th><th>Personnes</th><th>Montant</th><th>Statut</th></tr>
    <?php foreach ($reservations as $r): ?>
    <tr><td><?= e($r['numero_reservation']) ?></td><td><?= dateFr($r['date_visite']) ?></td><td><?= e($r['jeux']) ?></td>
        <td><?= (int)$r['nombre_personnes'] ?></td><td><?= montant($r['montant']) ?></td><td><?= badge($r['statut']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$reservations): ?><tr><td colspan="6">Aucune réservation.</td></tr><?php endif; ?>
</table></div>
