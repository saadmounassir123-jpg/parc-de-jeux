<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Billet de Réservation - <?= e($resa['numero_reservation']) ?></title>
    <style>
        :root {
            --couleur-primaire: #3b82f6;
            --couleur-primaire-hover: #2563eb;
            --couleur-secondaire: #f59e0b;
            --couleur-texte: #1e293b;
            --couleur-texte-clair: #64748b;
            --couleur-fond: #f1f5f9;
            --couleur-surface: #ffffff;
            --couleur-bordure: #e2e8f0;
            --couleur-succes: #10b981;
            --couleur-succes-fond: #d1fae5;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--couleur-fond);
            color: var(--couleur-texte);
            line-height: 1.5;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* Barre d'action supérieure (téléchargement / retour) */
        .barre-action {
            width: 100%;
            max-width: 620px;
            margin-bottom: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .actions-boutons {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-telecharger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background-color: var(--couleur-primaire);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
            transition: all 0.15s ease;
        }

        .btn-telecharger:hover {
            background-color: var(--couleur-primaire-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.25);
        }

        .btn-telecharger:active {
            transform: translateY(0);
        }

        .btn-retour {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.75rem 1.25rem;
            background-color: #ffffff;
            color: var(--couleur-texte);
            font-size: 0.925rem;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-retour:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
        }

        .astuce-telechargement {
            font-size: 0.85rem;
            color: var(--couleur-texte-clair);
            background: #ffffff;
            padding: 0.6rem 1rem;
            border-radius: 6px;
            border: 1px solid var(--couleur-bordure);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Structure du billet simple */
        .billet {
            background-color: var(--couleur-surface);
            width: 100%;
            max-width: 620px;
            border-radius: 14px;
            border: 1px solid var(--couleur-bordure);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            position: relative;
        }

        /* En-tête du billet */
        .billet-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 1.75rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .billet-brand h1 {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .billet-brand p {
            margin-top: 0.25rem;
            font-size: 0.875rem;
            opacity: 0.9;
            font-weight: 500;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .billet-badge-statut {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.825rem;
            font-weight: 600;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.35);
        }

        /* Bloc Référence / Numéro de réservation */
        .billet-ref-block {
            background: #f8fafc;
            border-bottom: 1px solid var(--couleur-bordure);
            padding: 1.25rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .billet-ref-item span {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--couleur-texte-clair);
            font-weight: 600;
            margin-bottom: 0.2rem;
        }

        .billet-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--couleur-primaire);
            letter-spacing: 1.5px;
        }

        .billet-date-creation {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--couleur-texte);
        }

        /* Corps du billet */
        .billet-body {
            padding: 2rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem 1rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-item.pleine-largeur {
            grid-column: 1 / -1;
        }

        .info-item .label {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            color: var(--couleur-texte-clair);
            margin-bottom: 0.35rem;
        }

        .info-item .valeur {
            font-size: 1.05rem;
            font-weight: 600;
            color: #0f172a;
        }

        .info-item .valeur-jeux {
            font-size: 0.95rem;
            font-weight: 500;
            color: #334155;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            line-height: 1.4;
        }

        /* Ligne de séparation ticket */
        .separateur-billet {
            margin: 1.75rem 0;
            border: none;
            border-top: 2px dashed #cbd5e1;
            position: relative;
        }

        /* Résumé financier */
        .montant-block {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .montant-block span {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e40af;
        }

        .montant-valeur {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--couleur-primaire);
        }

        /* Note importante */
        .instructions {
            background-color: #fef3c7;
            border-left: 4px solid var(--couleur-secondaire);
            padding: 0.9rem 1.1rem;
            color: #92400e;
            font-size: 0.875rem;
            border-radius: 6px;
            line-height: 1.45;
        }

        .instructions strong {
            color: #78350f;
        }

        /* Pied de page du billet */
        .billet-footer {
            padding: 1rem 2rem;
            background-color: #f8fafc;
            border-top: 1px solid var(--couleur-bordure);
            text-align: center;
            font-size: 0.8rem;
            color: var(--couleur-texte-clair);
        }

        /* ==========================================================================
           STYLE D'IMPRESSION (PDF / IMPRIMANTE)
           ========================================================================== */
        @media print {
            @page {
                size: auto;
                margin: 12mm;
            }

            body {
                background: #ffffff !important;
                padding: 0 !important;
                display: block;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .barre-action {
                display: none !important;
            }

            .billet {
                max-width: 100% !important;
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 8px !important;
                margin: 0 auto;
            }

            .billet-header {
                background: #1e40af !important;
                color: #ffffff !important;
            }

            .billet-badge-statut {
                background: #ffffff !important;
                color: #1e40af !important;
                border: 1px solid #ffffff !important;
            }

            .montant-block {
                background: #f1f5f9 !important;
                border: 1px solid #cbd5e1 !important;
            }

            .instructions {
                background: #fef3c7 !important;
                border-left: 4px solid #f59e0b !important;
            }
        }
    </style>
</head>
<body>

    <!-- Barre d'action supérieure simple -->
    <div class="barre-action">
        <div class="actions-boutons">
            <button class="btn-telecharger" id="btn-imprimer" onclick="telechargerBillet()">
                📥 Télécharger en PDF / Imprimer
            </button>
            <a class="btn-retour" href="<?= e(url('reservations')) ?>">
                ⬅ Mes réservations
            </a>
        </div>
        <div class="astuce-telechargement">
            💡 Pour enregistrer le billet : cliquez sur le bouton bleu ci-dessus, puis sélectionnez <strong>« Enregistrer au format PDF »</strong> dans la boîte de dialogue d'impression.
        </div>
    </div>

    <!-- Carte du Billet Simple -->
    <div class="billet">
        <div class="billet-header">
            <div class="billet-brand">
                <h1>🎪 FunPark</h1>
                <p>Billet de Réservation</p>
            </div>
            <div class="billet-badge-statut">
                <?= e(libelle($resa['statut'])) ?>
            </div>
        </div>

        <div class="billet-ref-block">
            <div class="billet-ref-item">
                <span>Code Réservation</span>
                <div class="billet-code"><?= e($resa['numero_reservation']) ?></div>
            </div>
            <div class="billet-ref-item" style="text-align: right;">
                <span>Créée le</span>
                <div class="billet-date-creation"><?= date('d/m/Y', strtotime($resa['date_creation'] ?? 'now')) ?></div>
            </div>
        </div>

        <div class="billet-body">
            <div class="info-grid">
                <div class="info-item">
                    <span class="label">Titulaire</span>
                    <span class="valeur"><?= e($resa['prenom'] . ' ' . $resa['nom']) ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Date de visite</span>
                    <span class="valeur"><?= date('d/m/Y', strtotime($resa['date_visite'])) ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Nombre de personnes</span>
                    <span class="valeur"><?= (int)$resa['nombre_personnes'] ?> personne<?= (int)$resa['nombre_personnes'] > 1 ? 's' : '' ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Paiement</span>
                    <span class="valeur">Au guichet du parc</span>
                </div>

                <div class="info-item pleine-largeur">
                    <span class="label">Jeux & Attractions choisis</span>
                    <div class="valeur-jeux">
                        <?= e($resa['jeux'] ?: 'Accès général aux attractions réservables') ?>
                    </div>
                </div>
            </div>

            <hr class="separateur-billet">

            <div class="montant-block">
                <span>Montant estimé (à régler sur place)</span>
                <span class="montant-valeur"><?= number_format($resa['montant'], 2, ',', ' ') ?> DH</span>
            </div>

            <div class="instructions">
                <strong>ℹ️ Important :</strong> Ce billet confirme votre réservation. Présentez-le simplement à la billetterie du parc (sur l'écran de votre téléphone ou imprimé) pour régler votre entrée et retirer vos bracelets d'accès.
            </div>
        </div>

        <div class="billet-footer">
            FunPark — Parc de loisirs et d'attractions • Merci pour votre visite !
        </div>
    </div>

    <script>
        function telechargerBillet() {
            window.print();
        }

        // Si l'URL comporte le paramètre ?auto=1 ou ?telecharger=1, ouvrir directement le téléchargement/impression
        (function() {
            var params = new URLSearchParams(window.location.search);
            if (params.get('telecharger') === '1' || params.get('auto') === '1') {
                setTimeout(function() {
                    window.print();
                }, 400);
            }
        })();
    </script>
</body>
</html>
