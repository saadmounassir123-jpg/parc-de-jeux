<?php $titrePage = 'Réservation confirmée'; ?>
<div class="carte apparition" style="max-width:640px;margin:0 auto;text-align:center">
    <h1>Réservation enregistrée</h1>
    <p>Votre numéro de réservation :</p>
    <p class="confirmation-numero"><?= e($resa['numero_reservation']) ?></p>
    <p><?= e($resa['prenom'] . ' ' . $resa['nom']) ?> · visite le <?= dateFr($resa['date_visite']) ?> · <?= (int)$resa['nombre_personnes'] ?> pers.</p>
    <p><strong>Jeux :</strong> <?= e($resa['jeux']) ?></p>
    <p><strong>Montant à régler :</strong> <?= montant($resa['montant']) ?>
        <?php if (!empty($resa['remise']) && (float)$resa['remise'] > 0): ?>
            <span style="color:#10b981; font-size:13px;">(Remise code promo <?= e($resa['code_promo']) ?> : -<?= montant($resa['remise']) ?>)</span>
        <?php endif; ?>
    </p>
    <p><?= badge($resa['statut']) ?></p>
    <?php bandeauBilletterie(); ?>
    <div style="margin-top:20px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
        <?php if ($resa['statut'] !== 'payee'): ?>
            <a class="btn btn-hero" href="<?= e(url('paiement', 'recapitulatif', ['type' => 'reservation', 'id' => $resa['id_reservation']])) ?>">💳 Payer en ligne maintenant</a>
        <?php endif; ?>
        <a class="btn" href="<?= e(url('reservations', 'imprimer', ['id' => $resa['id_reservation']])) ?>" target="_blank">🖨️ Imprimer</a>
        <a class="btn gris" href="<?= e(url('reservations')) ?>">Mes réservations</a>
        <a class="btn gris" href="<?= e(url('accueil')) ?>">Accueil</a>
    </div>
</div>
