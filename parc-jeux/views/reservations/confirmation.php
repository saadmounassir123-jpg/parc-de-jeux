<?php $titrePage = 'Réservation confirmée'; ?>
<div class="boite-blanche" style="max-width:640px;margin:2rem auto;text-align:center">
    <h1>Réservation enregistrée ✅</h1>
    <p>Votre numéro de réservation :</p>
    <p style="font-size: 2rem; font-weight: 800; color: var(--couleur-primaire); letter-spacing: 2px; margin: 1rem 0;"><?= e($resa['numero_reservation']) ?></p>
    <p><?= e($resa['prenom'] . ' ' . $resa['nom']) ?> • visite le <?= date('d/m/Y', strtotime($resa['date_visite'])) ?> • <?= (int)$resa['nombre_personnes'] ?> pers.</p>
    <p><strong>Jeux :</strong> <?= e($resa['jeux'] ?: 'Aucun') ?></p>
    <p><strong>Montant à régler à la billetterie :</strong> <?= montant($resa['montant']) ?></p>
    <p style="margin: 1.5rem 0;"><?= badge($resa['statut']) ?></p>
    
    <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1rem; color: #92400e; font-size: 0.9rem; border-radius: 4px; text-align:left; margin-bottom: 2rem;">
        <strong>Rappel :</strong> La réservation est gratuite en ligne. Présentez votre billet de réservation à la billetterie du parc pour effectuer le paiement et retirer vos accès aux manèges.
    </div>
    
    <p style="display:flex; justify-content:center; gap: 1rem; flex-wrap: wrap;">
        <a class="btn" href="<?= e(url('reservations', 'imprimer', ['id' => $resa['id_reservation']])) ?>" target="_blank">🎟️ Télécharger mon billet</a>
        <a class="btn violet" href="<?= e(url('reservations')) ?>">Mes réservations</a>
    </p>
</div>
