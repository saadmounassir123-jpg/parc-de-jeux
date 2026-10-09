<?php $titrePage = $jeu['nom']; $role = roleActuel(); ?>
<div class="entete">
    <h1><?= e($jeu['nom']) ?> <?= $jeu['actif'] ? badge($jeu['etat']) : badge('inactif') ?></h1>
    <div>
        <?php if ($role === 'admin'): ?><a class="btn" href="<?= e(url('jeux', 'modifier', ['id' => $jeu['id_jeu']])) ?>">Modifier</a><?php endif; ?>
        <?php if (in_array($role, ['admin', 'responsable'])): ?><a class="btn orange" href="<?= e(url('maintenance', 'signaler', ['id_jeu' => $jeu['id_jeu']])) ?>">Signaler une panne</a><?php endif; ?>
        <?php if (!estPersonnel()): ?><a class="btn" href="<?= e(url('reservations', 'creer')) ?>">Réserver</a><?php endif; ?>
        <a class="btn gris" href="<?= e(url('jeux')) ?>">Retour</a>
    </div>
</div>
<?php if (!estPersonnel()) bandeauBilletterie(); ?>
<div class="carte"><dl class="details">
    <dt>Description</dt><dd><?= e($jeu['description']) ?></dd>
    <dt>Catégorie</dt><dd><?= e($jeu['categorie']) ?></dd>
    <dt>Âge minimum</dt><dd><?= (int)$jeu['age_minimum'] ?> ans</dd>
    <dt>Capacité</dt><dd><?= (int)$jeu['capacite'] ?> personnes</dd>
    <dt>Durée d'une session</dt><dd><?= (int)$jeu['duree_session'] ?> minutes</dd>
    <dt>Tarif</dt><dd><?= montant($jeu['tarif']) ?> par personne</dd>
    <dt>Mise en service</dt><dd><?= dateFr($jeu['date_mise_service']) ?></dd>
    <dt>Responsable</dt><dd><?= $jeu['resp_nom'] ? e($jeu['resp_prenom'] . ' ' . $jeu['resp_nom']) : 'Aucun' ?></dd>
</dl></div>

<?php if (in_array($role, ['admin', 'responsable', 'agent'])): ?>
<h2>Planning : réservations à venir</h2>
<div class="tableau"><table>
    <tr><th>N°</th><th>Date de visite</th><th>Client</th><th>Personnes</th><th>Statut</th></tr>
    <?php foreach ($reservations as $r): ?>
    <tr><td><?= e($r['numero_reservation']) ?></td><td><?= dateFr($r['date_visite']) ?></td><td><?= e($r['prenom'] . ' ' . $r['nom']) ?></td>
        <td><?= (int)$r['nombre_personnes'] ?></td><td><?= badge($r['statut']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$reservations): ?><tr><td colspan="5">Aucune réservation à venir.</td></tr><?php endif; ?>
</table></div>
<?php endif; ?>

<?php if (in_array($role, ['admin', 'responsable'])): ?>
<h2>Dépenses de ce jeu</h2>
<div class="tableau"><table>
    <tr><th>Date</th><th>Catégorie</th><th>Description</th><th>Montant</th></tr>
    <?php foreach ($depenses as $d): ?>
    <tr><td><?= dateFr($d['date_depense']) ?></td><td><?= e($d['categorie']) ?></td><td><?= e($d['description']) ?></td><td><?= montant($d['montant']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$depenses): ?><tr><td colspan="4">Aucune dépense.</td></tr><?php endif; ?>
</table></div>
<h2>Maintenance</h2>
<div class="tableau"><table>
    <tr><th>Date</th><th>Type</th><th>Description</th><th>État</th><th>Prochaine</th></tr>
    <?php foreach ($maintenances as $m): ?>
    <tr><td><?= dateFr($m['date_intervention']) ?></td><td><?= e($m['type_intervention']) ?></td><td><?= e($m['description']) ?></td>
        <td><?= badge($m['etat']) ?></td><td><?= dateFr($m['date_prochaine']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$maintenances): ?><tr><td colspan="5">Aucune intervention.</td></tr><?php endif; ?>
</table></div>
<?php endif; ?>
