<?php $titrePage = 'Réservations'; $role = roleActuel(); ?>
<div class="entete">
    <h1><?= $role === 'client' ? 'Mes réservations' : ($role === 'responsable' ? 'Planning de mes jeux' : 'Réservations') ?></h1>
    <?php if ($role !== 'responsable'): ?>
        <a class="btn" href="<?= e(url('reservations', 'creer')) ?>">+ Nouvelle réservation</a>
    <?php endif; ?>
</div>

<?php if ($role === 'client' && function_exists('bandeauBilletterie')) bandeauBilletterie(); ?>

<form method="get" class="filtres">
    <input type="hidden" name="page" value="reservations">
    <input type="date" name="date_debut" value="<?= e($filtres['date_debut']) ?>" title="Visite du">
    <input type="date" name="date_fin" value="<?= e($filtres['date_fin']) ?>" title="Visite au">
    <select name="statut"><option value="">Tous les statuts</option>
        <?php foreach (['en_attente', 'confirmee', 'payee', 'utilisee', 'annulee'] as $s): ?><option value="<?= $s ?>" <?= selectionne($filtres['statut'], $s) ?>><?= e(libelle($s)) ?></option><?php endforeach; ?>
    </select>
    <select name="id_jeu"><option value="">Tous les jeux</option>
        <?php foreach ($jeux as $j): ?><option value="<?= $j['id_jeu'] ?>" <?= selectionne($filtres['id_jeu'], $j['id_jeu']) ?>><?= e($j['nom']) ?></option><?php endforeach; ?>
    </select>
    <?php if (in_array($role, ['admin', 'agent'])): ?>
    <select name="id_client"><option value="">Tous les clients</option>
        <?php foreach ($clients as $c): ?><option value="<?= $c['id_client'] ?>" <?= selectionne($filtres['id_client'], $c['id_client']) ?>><?= e($c['prenom'] . ' ' . $c['nom']) ?></option><?php endforeach; ?>
    </select>
    <?php endif; ?>
    <?php if ($role === 'admin'): ?>
    <select name="id_responsable"><option value="">Tous les responsables</option>
        <?php foreach ($responsables as $r): ?><option value="<?= $r['id_responsable'] ?>" <?= selectionne($filtres['id_responsable'], $r['id_responsable']) ?>><?= e($r['prenom'] . ' ' . $r['nom']) ?></option><?php endforeach; ?>
    </select>
    <?php endif; ?>
    <button class="btn" type="submit">Filtrer</button>
</form>

<div class="tableau"><table>
    <tr><th>N°</th><th>Client</th><th>Créée le</th><th>Visite</th><th>Pers.</th><th>Jeux</th><th>Montant</th><th>Statut</th><?php if ($role !== 'responsable'): ?><th>Actions</th><?php endif; ?></tr>
    <?php foreach ($reservations as $r): ?>
    <?php $s = $r['statut']; $monClient = ($role === 'client'); ?>
    <tr>
        <td><?= e($r['numero_reservation']) ?></td><td><?= e($r['prenom'] . ' ' . $r['nom']) ?></td>
        <td><?= date('d/m/Y', strtotime($r['date_creation'])) ?></td><td><?= date('d/m/Y', strtotime($r['date_visite'])) ?></td><td><?= (int)$r['nombre_personnes'] ?></td>
        <td><?= e($r['jeux'] ?: 'Aucun') ?></td><td><?= montant($r['montant']) ?></td><td><?= badge($s) ?></td>
        <?php if ($role !== 'responsable'): ?>
        <td class="actions">
            <?php if ($monClient): ?>
                <a class="btn violet" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;" href="<?= e(url('reservations', 'imprimer', ['id' => $r['id_reservation']])) ?>" target="_blank">🎟️ Billet</a>
            <?php endif; ?>
            <?php if ($s === 'en_attente' && !$monClient): ?>
                <form method="post" action="<?= e(url('reservations', 'statut')) ?>" data-confirm="Confirmer cette réservation ?">
                    <input type="hidden" name="id" value="<?= (int)$r['id_reservation'] ?>"><input type="hidden" name="statut" value="confirmee">
                    <button class="lien" type="submit">Confirmer</button></form>
            <?php endif; ?>
            <?php if ($s === 'confirmee' && !$monClient): ?>
                <form method="post" action="<?= e(url('reservations', 'statut')) ?>" data-confirm="Enregistrer le paiement à la billetterie ?">
                    <input type="hidden" name="id" value="<?= (int)$r['id_reservation'] ?>"><input type="hidden" name="statut" value="payee">
                    <select name="mode_paiement" style="padding: 0.2rem; min-width: 80px; margin: 0 5px 0 0; border-radius: 4px; font-size: 0.85rem;"><option value="especes">Espèces</option><option value="carte">Carte</option></select>
                    <button class="lien" type="submit" style="color: var(--couleur-succes);">Encaisser</button></form>
            <?php endif; ?>
            <?php if ($s === 'payee' && !$monClient): ?>
                <form method="post" action="<?= e(url('reservations', 'statut')) ?>" data-confirm="Marquer comme utilisée ?">
                    <input type="hidden" name="id" value="<?= (int)$r['id_reservation'] ?>"><input type="hidden" name="statut" value="utilisee">
                    <button class="lien" type="submit">Utilisée</button></form>
            <?php endif; ?>
            <?php if (in_array($s, ['en_attente', 'confirmee', 'payee'])): ?>
                <form method="post" action="<?= e(url('reservations', 'statut')) ?>" data-confirm="Annuler cette réservation ?">
                    <input type="hidden" name="id" value="<?= (int)$r['id_reservation'] ?>"><input type="hidden" name="statut" value="annulee">
                    <button class="lien rouge" type="submit">Annuler</button></form>
            <?php endif; ?>
        </td>
        <?php endif; ?>
    </tr>
    <?php endforeach; ?>
    <?php if (!$reservations): ?><tr><td colspan="9">Aucune réservation.</td></tr><?php endif; ?>
</table></div>
<p class="aide" style="margin-top: 1.5rem; font-size: 0.9rem; color: var(--couleur-texte-clair);">Cycle : en attente → confirmée (accueil) → payée (billetterie, recette créée) → utilisée. Annulation possible avant la date de visite. Une réservation payée annulée est remboursée (sa recette est retirée).</p>
