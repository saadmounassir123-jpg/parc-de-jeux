<?php $titrePage = 'Commande ' . $achat['numero_achat']; ?>

<div class="entete">
    <h1>Commande <?= e($achat['numero_achat']) ?></h1>
    <div>
        <a class="btn gris" href="<?= e(url('achats')) ?>">← Retour aux achats</a>
        <?php if ($achat['statut'] === 'paye'): ?>
            <a class="btn" href="<?= e(url('achats', 'imprimer', ['id' => $achat['id_achat']])) ?>" target="_blank">🖨️ Imprimer les billets</a>
        <?php endif; ?>
    </div>
</div>

<div class="grille-detail-achat" style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
    <div>
        <div class="carte">
            <h3>🎟️ Billets commandés</h3>
            <div class="tableau" style="margin-top:16px;">
                <table>
                    <thead>
                        <tr>
                            <th>Formule / Billet</th>
                            <th>Validité</th>
                            <th>Tarif unitaire</th>
                            <th>Quantité</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($achat['lignes'] as $l): ?>
                        <tr>
                            <td><strong><?= e($l['type_nom']) ?></strong></td>
                            <td><?= (int)$l['duree_validite'] ?> jour(s)</td>
                            <td><?= montant($l['tarif_unitaire']) ?></td>
                            <td><strong>x<?= (int)$l['quantite'] ?></strong></td>
                            <td><?= montant($l['total_ligne']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- E-Billets visuels individuels -->
            <?php if ($achat['statut'] === 'paye'): ?>
            <h4 style="margin-top:30px; margin-bottom:12px;">📱 Vos E-Billets d'accès</h4>
            <div class="grille-ebillets">
                <?php $numTicket = 1; ?>
                <?php foreach ($achat['lignes'] as $l): ?>
                    <?php for ($i = 1; $i <= $l['quantite']; $i++): ?>
                    <div class="carte-ebillet">
                        <div class="ebillet-entete">
                            <span class="ebillet-logo">🎡 FunPark</span>
                            <span class="badge b-confirmee">Valide</span>
                        </div>
                        <div class="ebillet-corps">
                            <strong><?= e($l['type_nom']) ?></strong>
                            <p>Visite : <?= dateFr($achat['date_visite']) ?></p>
                            <small class="texte-muet">Bénéficiaire : <?= e($achat['prenom'] . ' ' . $achat['nom']) ?></small>
                        </div>
                        <div class="ebillet-pied">
                            <div class="code-barres-simule">
                                ||| | |||| | ||| || |||| | |||||
                            </div>
                            <small class="code-ref-billet"><?= e($achat['numero_achat']) ?>-T<?= str_pad($numTicket++, 2, '0', STR_PAD_LEFT) ?></small>
                        </div>
                    </div>
                    <?php endfor; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <div class="carte">
            <h3>📋 Synthèse financière</h3>
            <div class="liste-cle-valeur" style="margin-top:16px; line-height:2;">
                <div><span>Date de commande :</span> <strong><?= dateFr($achat['date_achat']) ?></strong></div>
                <div><span>Date de visite :</span> <strong><?= dateFr($achat['date_visite']) ?></strong></div>
                <div><span>Sous-total brut :</span> <strong><?= montant($achat['montant_brut']) ?></strong></div>
                <?php if ((float)$achat['remise'] > 0): ?>
                    <div style="color:#10b981;"><span>Code promo (<?= e($achat['code_promo']) ?>) :</span> <strong>- <?= montant($achat['remise']) ?></strong></div>
                <?php endif; ?>
                <div style="font-size:1.15em; border-top:1px solid #e2e8f0; padding-top:8px; margin-top:8px;">
                    <span>Montant total :</span> <strong style="color:var(--couleur-primaire, #ff5722);"><?= montant($achat['montant_total']) ?></strong>
                </div>
                <div><span>Statut :</span> 
                    <?php if ($achat['statut'] === 'paye'): ?>
                        <span class="badge b-confirmee">Payé</span>
                    <?php elseif ($achat['statut'] === 'en_attente'): ?>
                        <span class="badge b-en_attente">En attente</span>
                    <?php else: ?>
                        <span class="badge b-annulee">Annulé</span>
                    <?php endif; ?>
                </div>
                <?php if ($achat['statut'] === 'paye'): ?>
                    <div><span>Mode de paiement :</span> <strong><?= e(libelle($achat['mode_paiement'])) ?></strong></div>
                    <div><span>Référence transaction :</span> <small><?= e($achat['reference_paiement']) ?></small></div>
                <?php endif; ?>
            </div>

            <?php if ($achat['statut'] === 'en_attente'): ?>
                <div style="margin-top:20px;">
                    <a class="btn btn-hero" style="width:100%; text-align:center;" href="<?= e(url('paiement', 'recapitulatif', ['type' => 'achat', 'id' => $achat['id_achat']])) ?>">
                        💳 Procéder au règlement
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="carte" style="margin-top:20px;">
            <h3>👤 Informations Visiteur</h3>
            <p style="margin-top:10px; line-height:1.6;">
                <strong><?= e($achat['prenom'] . ' ' . $achat['nom']) ?></strong><br>
                ✉️ <?= e($achat['email']) ?><br>
                📞 <?= e($achat['telephone'] ?: 'Non renseigné') ?><br>
                Type : <?= e(libelle($achat['type_client'])) ?>
            </p>
        </div>
    </div>
</div>
