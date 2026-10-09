<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Billet de Réservation - <?= e($resa['numero_reservation']) ?></title>
    <style>
        body { font-family: system-ui, sans-serif; background: #e2e8f0; margin: 0; padding: 2rem; display: flex; justify-content: center; }
        .billet { background: white; width: 100%; max-width: 600px; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); overflow: hidden; position: relative; }
        .billet-header { background: #3b82f6; color: white; padding: 2rem; text-align: center; }
        .billet-header h1 { margin: 0; font-size: 2rem; }
        .billet-header p { margin: 0.5rem 0 0; opacity: 0.9; }
        .billet-body { padding: 2rem; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
        .info-item span { display: block; font-size: 0.85rem; color: #64748b; text-transform: uppercase; font-weight: 600; }
        .info-item strong { display: block; font-size: 1.1rem; color: #0f172a; }
        .qr-code { text-align: center; margin: 2rem 0; padding: 1.5rem; border: 2px dashed #cbd5e1; border-radius: 8px; background: #f8fafc; }
        .qr-code img { max-width: 150px; }
        .qr-code p { margin: 0.5rem 0 0; font-weight: bold; font-family: monospace; font-size: 1.2rem; letter-spacing: 2px; }
        .instructions { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1rem; color: #92400e; font-size: 0.9rem; border-radius: 4px; }
        .print-btn { display: block; width: 200px; margin: 2rem auto; padding: 1rem; text-align: center; background: #10b981; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; cursor: pointer; border: none; }
        @media print {
            body { background: white; padding: 0; }
            .billet { box-shadow: none; max-width: 100%; border: 1px solid #cbd5e1; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <div>
        <div class="billet">
            <div class="billet-header">
                <h1>🎪 FunPark</h1>
                <p>Billet de Réservation</p>
            </div>
            <div class="billet-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span>Réservation N°</span>
                        <strong><?= e($resa['numero_reservation']) ?></strong>
                    </div>
                    <div class="info-item">
                        <span>Client</span>
                        <strong><?= e($resa['prenom'] . ' ' . $resa['nom']) ?></strong>
                    </div>
                    <div class="info-item">
                        <span>Date de visite</span>
                        <strong><?= date('d/m/Y', strtotime($resa['date_visite'])) ?></strong>
                    </div>
                    <div class="info-item">
                        <span>Nombre de personnes</span>
                        <strong><?= (int)$resa['nombre_personnes'] ?> pers.</strong>
                    </div>
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <span>Jeux réservés</span>
                        <strong><?= e($resa['jeux'] ?: 'Aucun jeu spécifique') ?></strong>
                    </div>
                    <div class="info-item" style="grid-column: 1 / -1; text-align: center; margin-top: 1rem;">
                        <span>Montant estimé</span>
                        <strong style="font-size: 1.5rem; color: #3b82f6;"><?= number_format($resa['montant'], 2, ',', ' ') ?> DH</strong>
                    </div>
                </div>
                
                <div class="qr-code">
                    <!-- Fictional QR Code using an online generator API for the demo -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode($resa['numero_reservation']) ?>" alt="QR Code">
                    <p><?= e($resa['numero_reservation']) ?></p>
                </div>
                
                <div class="instructions">
                    <strong>⚠️ Important :</strong> Ce billet ne fait pas office de ticket d'entrée. Veuillez le présenter (imprimé ou sur votre téléphone) au guichet du parc pour régler votre achat et retirer vos billets définitifs.
                </div>
            </div>
        </div>
        <button class="print-btn" onclick="window.print()">🖨️ Imprimer mon billet</button>
    </div>
</body>
</html>
