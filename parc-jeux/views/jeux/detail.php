<?php $titrePage = $jeu['nom']; $role = roleActuel(); ?>
<div class="entete">
    <h1><?= e($jeu['nom']) ?> <?= $jeu['actif'] ? badge($jeu['etat']) : badge('inactif') ?></h1>
    <div>
        <?php if ($role === 'admin'): ?><a class="btn" href="<?= e(url('jeux', 'modifier', ['id' => $jeu['id_jeu']])) ?>">Modifier</a><?php endif; ?>
        <?php if (in_array($role, ['admin', 'responsable'])): ?><a class="btn orange" href="<?= e(url('maintenance', 'signaler', ['id_jeu' => $jeu['id_jeu']])) ?>">Signaler une panne</a><?php endif; ?>
        <?php if (!estPersonnel()): ?><a class="btn violet" href="<?= e(url('reservations', 'creer')) ?>">Réserver</a><?php endif; ?>
        <a class="btn gris" href="<?= e(url('jeux')) ?>">Retour</a>
    </div>
</div>
<?php if (!estPersonnel() && function_exists('bandeauBilletterie')) bandeauBilletterie(); ?>

<div class="boite-blanche" style="display: flex; gap: 2rem; flex-wrap: wrap;">
    <?php $imgFile = 'public/img/jeu_' . $jeu['id_jeu'] . '.jpg'; ?>
    <?php if (file_exists(RACINE . '/' . $imgFile)): ?>
        <div style="flex: 1; min-width: 300px;">
            <img src="<?= e($imgFile) ?>" alt="<?= e($jeu['nom']) ?>" style="width: 100%; border-radius: var(--rayon-bordure); box-shadow: var(--ombre-douce);">
        </div>
    <?php endif; ?>
    
    <div style="flex: 2; min-width: 300px;">
        <dl class="details" style="display: grid; grid-template-columns: auto 1fr; gap: 1rem; margin: 0;">
            <dt style="font-weight: 600; color: var(--couleur-texte-clair);">Description</dt><dd style="margin:0;"><?= e($jeu['description']) ?></dd>
            <dt style="font-weight: 600; color: var(--couleur-texte-clair);">Catégorie</dt><dd style="margin:0;"><span class="badge" style="background:#e2e8f0; color:#475569;"><?= e($jeu['categorie']) ?></span></dd>
            <dt style="font-weight: 600; color: var(--couleur-texte-clair);">Âge minimum</dt><dd style="margin:0;"><?= (int)$jeu['age_minimum'] ?> ans</dd>
            <dt style="font-weight: 600; color: var(--couleur-texte-clair);">Capacité</dt><dd style="margin:0;"><?= (int)$jeu['capacite'] ?> personnes</dd>
            <dt style="font-weight: 600; color: var(--couleur-texte-clair);">Durée de session</dt><dd style="margin:0;"><?= (int)$jeu['duree_session'] ?> minutes</dd>
            <dt style="font-weight: 600; color: var(--couleur-texte-clair);">Tarif</dt><dd style="margin:0;"><strong style="color: var(--couleur-primaire); font-size: 1.2rem;"><?= montant($jeu['tarif']) ?></strong> par personne</dd>
            <dt style="font-weight: 600; color: var(--couleur-texte-clair);">Mise en service</dt><dd style="margin:0;"><?= date('d/m/Y', strtotime($jeu['date_mise_service'])) ?></dd>
            <dt style="font-weight: 600; color: var(--couleur-texte-clair);">Responsable</dt><dd style="margin:0;"><?= $jeu['resp_nom'] ? e($jeu['resp_prenom'] . ' ' . $jeu['resp_nom']) : 'Aucun' ?></dd>
        </dl>
    </div>
</div>

<?php if (in_array($role, ['admin', 'responsable', 'agent'])): ?>
<h2 style="margin-top: 3rem;">Planning : réservations à venir</h2>
<div class="tableau"><table>
    <tr><th>N°</th><th>Date de visite</th><th>Client</th><th>Personnes</th><th>Statut</th></tr>
    <?php foreach ($reservations as $r): ?>
    <tr><td><?= e($r['numero_reservation']) ?></td><td><?= date('d/m/Y', strtotime($r['date_visite'])) ?></td><td><?= e($r['prenom'] . ' ' . $r['nom']) ?></td>
        <td><?= (int)$r['nombre_personnes'] ?></td><td><?= badge($r['statut']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$reservations): ?><tr><td colspan="5">Aucune réservation à venir.</td></tr><?php endif; ?>
</table></div>
<?php endif; ?>

<?php if (in_array($role, ['admin', 'responsable'])): ?>
<h2 style="margin-top: 3rem;">Dépenses de ce jeu</h2>
<div class="tableau"><table>
    <tr><th>Date</th><th>Catégorie</th><th>Description</th><th>Montant</th></tr>
    <?php foreach ($depenses as $d): ?>
    <tr><td><?= date('d/m/Y', strtotime($d['date_depense'])) ?></td><td><?= e($d['categorie']) ?></td><td><?= e($d['description']) ?></td><td><?= montant($d['montant']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$depenses): ?><tr><td colspan="4">Aucune dépense.</td></tr><?php endif; ?>
</table></div>
<h2 style="margin-top: 3rem;">Maintenance</h2>
<div class="tableau"><table>
    <tr><th>Date</th><th>Type</th><th>Description</th><th>État</th><th>Prochaine</th></tr>
    <?php foreach ($maintenances as $m): ?>
    <tr><td><?= date('d/m/Y', strtotime($m['date_intervention'])) ?></td><td><?= e($m['type_intervention']) ?></td><td><?= e($m['description']) ?></td>
        <td><?= badge($m['etat']) ?></td><td><?= $m['date_prochaine'] ? date('d/m/Y', strtotime($m['date_prochaine'])) : '-' ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$maintenances): ?><tr><td colspan="5">Aucune intervention.</td></tr><?php endif; ?>
</table></div>
<?php endif; ?>
