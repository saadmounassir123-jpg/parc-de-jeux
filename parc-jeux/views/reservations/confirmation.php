<?php $titrePage = 'Réservation confirmée'; ?>
<div class="carte apparition" style="max-width:640px;margin:0 auto;text-align:center">
    <h1>Réservation enregistrée</h1>
    <p>Votre numéro de réservation :</p>
    <p class="confirmation-numero"><?= e($resa['numero_reservation']) ?></p>
    <p><?= e($resa['prenom'] . ' ' . $resa['nom']) ?> · visite le <?= dateFr($resa['date_visite']) ?> · <?= (int)$resa['nombre_personnes'] ?> pers.</p>
    <p><strong>Jeux :</strong> <?= e($resa['jeux']) ?></p>
    <p><strong>Montant à régler à la billetterie :</strong> <?= montant($resa['montant']) ?></p>
    <p><?= badge($resa['statut']) ?></p>
    <?php bandeauBilletterie(); ?>
    <p>
        <a class="btn" href="<?= e(url('reservations')) ?>">Mes réservations</a>
        <a class="btn gris" href="<?= e(url('accueil')) ?>">Accueil</a>
    </p>
</div>
