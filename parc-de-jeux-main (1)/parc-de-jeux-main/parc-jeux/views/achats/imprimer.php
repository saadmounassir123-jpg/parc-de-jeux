<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>E-Billets — Commande <?= e($achat['numero_achat']) ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #1e293b; padding: 20px; max-width: 800px; margin: 0 auto; }
        .entete-recu { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: 800; color: #f97316; }
        .badge-paye { background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 999px; font-weight: 700; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 12px; text-align: left; }
        th { background: #f8fafc; font-weight: 600; }
        .ticket-item { border: 2px dashed #94a3b8; border-radius: 12px; padding: 15px; margin-bottom: 15px; page-break-inside: avoid; display: flex; justify-content: space-between; align-items: center; background: #fafafa; }
        .ticket-code { font-family: monospace; font-size: 18px; letter-spacing: 2px; }
        .btn-print { background: #f97316; color: white; border: none; padding: 10px 20px; font-weight: 700; border-radius: 8px; cursor: pointer; }
        @media print {
            .btn-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div style="text-align: right; margin-bottom: 15px;">
        <button class="btn-print" onclick="window.print()">🖨️ Imprimer les billets</button>
    </div>

    <div class="entete-recu">
        <div>
            <div class="logo">🎡 FunPark</div>
            <p style="margin: 4px 0; color: #64748b;">Parc d'attractions et de loisirs · Casablanca</p>
        </div>
        <div style="text-align: right;">
            <span class="badge-paye">PAYÉ & CONFIRMÉ</span>
            <p style="margin: 4px 0; font-weight: bold;">Commande N° <?= e($achat['numero_achat']) ?></p>
            <p style="margin: 0; font-size: 13px; color: #64748b;">Visite le : <?= dateFr($achat['date_visite']) ?></p>
        </div>
    </div>

    <p>
        <strong>Titulaire :</strong> <?= e($achat['prenom'] . ' ' . $achat['nom']) ?><br>
        <strong>Email :</strong> <?= e($achat['email']) ?> | <strong>Téléphone :</strong> <?= e($achat['telephone'] ?: '-') ?><br>
        <strong>Règlement :</strong> <?= e(libelle($achat['mode_paiement'])) ?> (Réf: <?= e($achat['reference_paiement']) ?>)
    </p>

    <table>
        <thead>
            <tr>
                <th>Billet</th>
                <th>Quantité</th>
                <th>Tarif unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($achat['lignes'] as $l): ?>
            <tr>
                <td><?= e($l['type_nom']) ?> (validité <?= (int)$l['duree_validite'] ?> j)</td>
                <td><?= (int)$l['quantite'] ?></td>
                <td><?= montant($l['tarif_unitaire']) ?></td>
                <td><?= montant($l['total_ligne']) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Sous-total brut :</strong></td>
                <td><?= montant($achat['montant_brut']) ?></td>
            </tr>
            <?php if ((float)$achat['remise'] > 0): ?>
            <tr style="color: #15803d;">
                <td colspan="3" style="text-align: right;"><strong>Remise Code Promo (<?= e($achat['code_promo']) ?>) :</strong></td>
                <td>- <?= montant($achat['remise']) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td colspan="3" style="text-align: right; font-size: 16px;"><strong>Total Réglé :</strong></td>
                <td style="font-size: 16px; font-weight: bold;"><?= montant($achat['montant_total']) ?></td>
            </tr>
        </tbody>
    </table>

    <h3 style="margin-top: 25px; border-bottom: 1px solid #cbd5e1; padding-bottom: 8px;">Vos Titres d'Accès Individuels</h3>
    <?php $numT = 1; ?>
    <?php foreach ($achat['lignes'] as $l): ?>
        <?php for ($i = 1; $i <= $l['quantite']; $i++): ?>
        <div class="ticket-item">
            <div>
                <strong style="font-size: 18px;"><?= e($l['type_nom']) ?></strong>
                <p style="margin: 4px 0; color: #475569;">Bénéficiaire : <?= e($achat['prenom'] . ' ' . $achat['nom']) ?></p>
                <small style="color: #64748b;">Valable pour la date du : <?= dateFr($achat['date_visite']) ?></small>
            </div>
            <div style="text-align: center;">
                <div class="ticket-code">|||| | ||| |||| | ||</div>
                <div style="font-size: 12px; font-weight: bold; margin-top: 4px;"><?= e($achat['numero_achat']) ?>-T<?= str_pad($numT++, 2, '0', STR_PAD_LEFT) ?></div>
            </div>
        </div>
        <?php endfor; ?>
    <?php endforeach; ?>

    <p style="font-size: 12px; color: #94a3b8; text-align: center; margin-top: 30px;">
        FunPark Casablanca — Billet personnel et incessible. Présentez ce document ou sa version mobile aux tourniquets du parc.
    </p>
</body>
</html>
