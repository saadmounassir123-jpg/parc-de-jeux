<?php 
$titrePage = 'Paiement confirmé — FunPark'; 
$refPaiement = $objet['reference_paiement'] ?? 'TXN-' . date('Ymd');
$mode = $objet['mode_paiement'] ?? 'carte';
$numero = ($type === 'achat') ? $objet['numero_achat'] : $objet['numero_reservation'];
$montant = ($type === 'achat') ? $objet['montant_total'] : $objet['montant'];
?>

<div class="carte carte-succes-paiement apparition" style="max-width:680px; margin:30px auto; text-align:center; padding:36px 24px;">
    
    <div class="icone-succes-anime">
        <span>✅</span>
    </div>

    <h1 style="color:#0f172a; margin-top:16px;">
        <?= $mode === 'especes' ? 'Commande / Réservation validée !' : 'Paiement confirmé avec succès !' ?>
    </h1>
    
    <p class="texte-muet" style="font-size:16px;">
        Merci pour votre confiance. Votre transaction a été enregistrée avec succès.
    </p>

    <div class="badge-reference-transaction">
        <span class="texte-muet">Numéro de dossier :</span>
        <strong style="font-size:22px; color:var(--couleur-primaire, #f97316); display:block; margin:4px 0;"><?= e($numero) ?></strong>
        <?php if ($mode !== 'especes'): ?>
            <small class="texte-muet">Réf. transaction bancaire : <?= e($refPaiement) ?></small>
        <?php endif; ?>
    </div>

    <div class="recap-succes-detail" style="text-align:left; background:#f8fafc; border-radius:12px; padding:20px; margin:24px 0; border:1px solid #e2e8f0;">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; font-size:14px; line-height:1.7;">
            <div>
                <span class="texte-muet">Bénéficiaire :</span><br>
                <strong><?= e($objet['prenom'] . ' ' . $objet['nom']) ?></strong>
            </div>
            <div>
                <span class="texte-muet">Date de visite :</span><br>
                <strong><?= dateFr($objet['date_visite']) ?></strong>
            </div>
            <div>
                <span class="texte-muet">Moyen de règlement :</span><br>
                <strong><?= e(libelle($mode)) ?></strong>
            </div>
            <div>
                <span class="texte-muet">Montant réglé :</span><br>
                <strong style="color:#10b981; font-size:16px;"><?= montant($montant) ?></strong>
            </div>
        </div>

        <div style="margin-top:14px; border-top:1px dashed #cbd5e1; padding-top:12px; font-size:14px;">
            <?php if ($type === 'achat'): ?>
                <span class="texte-muet">Billets commandés :</span><br>
                <?php foreach ($objet['lignes'] as $l): ?>
                    <strong>• <?= (int)$l['quantite'] ?>x <?= e($l['type_nom']) ?></strong> (<?= montant($l['total_ligne']) ?>)<br>
                <?php endforeach; ?>
            <?php else: ?>
                <span class="texte-muet">Attractions réservées :</span><br>
                <strong><?= e($objet['jeux']) ?></strong> (<?= (int)$objet['nombre_personnes'] ?> pers.)
            <?php endif; ?>
        </div>
    </div>

    <?php if ($mode === 'especes'): ?>
        <div class="bandeau-info" style="margin-bottom:24px; text-align:left;">
            ℹ️ <strong>Rappel :</strong> Présentez votre numéro <strong><?= e($numero) ?></strong> à la billetterie du parc le jour de votre visite pour régler et retirer vos pass d'accès.
        </div>
    <?php endif; ?>

    <div class="actions-succes" style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap;">
        <?php if ($type === 'achat'): ?>
            <a class="btn" href="<?= e(url('achats', 'imprimer', ['id' => $id])) ?>" target="_blank">
                🖨️ Télécharger / Imprimer les billets
            </a>
            <a class="btn gris" href="<?= e(url('achats')) ?>">
                🎟️ Voir mes achats
            </a>
        <?php else: ?>
            <a class="btn" href="<?= e(url('reservations', 'imprimer', ['id' => $id])) ?>" target="_blank">
                🖨️ Imprimer la réservation
            </a>
            <a class="btn gris" href="<?= e(url('reservations')) ?>">
                📅 Voir mes réservations
            </a>
        <?php endif; ?>
        <a class="btn gris" href="<?= e(url('accueil')) ?>">🏠 Accueil</a>
    </div>
</div>
