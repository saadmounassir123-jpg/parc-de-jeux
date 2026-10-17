<?php 
$titrePage = 'Paiement sécurisé — ' . e($reference); 
?>

<div class="paiement-entete">
    <span class="hero-badge">🔒 Tunnel de paiement 100% sécurisé</span>
    <h1>Finaliser votre règlement</h1>
    <p>Réglez votre <?= $type === 'achat' ? 'commande de billets' : 'réservation d\'attractions' ?> en toute sérénité.</p>
</div>

<div class="grille-paiement">
    <!-- Colonne gauche : Formulaire de règlement -->
    <div class="paiement-choix-section">
        <form method="post" action="<?= e(url('paiement', 'process')) ?>" id="form-paiement" class="validation">
            <input type="hidden" name="type" value="<?= e($type) ?>">
            <input type="hidden" name="id" value="<?= (int)$id ?>">

            <div class="carte carte-modes-paiement">
                <h3>1. Choisissez votre moyen de paiement</h3>
                
                <div class="options-moyens-paiement">
                    <label class="option-paiement-radio actif" onclick="choisirMode('carte')">
                        <input type="radio" name="mode_paiement" value="carte" checked id="radio-carte">
                        <div class="option-paiement-contenu">
                            <div class="option-titre-icones">
                                <strong>💳 Carte Bancaire</strong>
                                <span class="cartes-icones">Visa · Mastercard · CMI</span>
                            </div>
                            <p class="texte-muet">Paiement instantané en ligne, sécurisé par cryptage SSL 256 bits.</p>
                        </div>
                    </label>

                    <label class="option-paiement-radio" onclick="choisirMode('especes')">
                        <input type="radio" name="mode_paiement" value="especes" id="radio-especes">
                        <div class="option-paiement-contenu">
                            <div class="option-titre-icones">
                                <strong>💵 Règlement au Guichet (Espèces / Carte)</strong>
                            </div>
                            <p class="texte-muet">Réglez votre visite sur place à l'accueil du parc le jour de votre venue.</p>
                        </div>
                    </label>

                    <label class="option-paiement-radio" onclick="choisirMode('virement')">
                        <input type="radio" name="mode_paiement" value="virement" id="radio-virement">
                        <div class="option-paiement-contenu">
                            <div class="option-titre-icones">
                                <strong>📱 Virement bancaire / Paiement Mobile</strong>
                            </div>
                            <p class="texte-muet">Virement instantané direct vers le compte FunPark.</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Bloc formulaire Carte Bancaire -->
            <div class="carte" id="bloc-carte" style="margin-top:20px;">
                <h3>2. Coordonnées de votre carte</h3>
                
                <!-- Visuel interactif de carte bancaire -->
                <div class="visuel-carte-bancaire">
                    <div class="cb-puce"></div>
                    <div class="cb-numero" id="visu-cb-num">•••• •••• •••• ••••</div>
                    <div class="cb-bas">
                        <div>
                            <span class="cb-label">TITULAIRE</span>
                            <span class="cb-val" id="visu-cb-nom">NOM DU TITULAIRE</span>
                        </div>
                        <div>
                            <span class="cb-label">EXPIRE FIN</span>
                            <span class="cb-val" id="visu-cb-exp">MM/AA</span>
                        </div>
                    </div>
                </div>

                <div class="form-groupe" style="margin-top:16px;">
                    <label for="cb-nom">Nom complet sur la carte *</label>
                    <input type="text" id="cb-nom" name="titulaire" placeholder="Ex: MOHAMMED ALAOUI" required oninput="majVisuNom(this.value)">
                </div>

                <div class="form-groupe">
                    <label for="cb-num">Numéro de carte bancaire *</label>
                    <input type="text" id="cb-num" name="numero_carte" maxlength="19" placeholder="4000 1234 5678 9010" required oninput="majVisuNum(this)">
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-groupe">
                        <label for="cb-exp">Date d'expiration *</label>
                        <input type="text" id="cb-exp" name="expiration" maxlength="5" placeholder="MM/AA" required oninput="majVisuExp(this)">
                    </div>
                    <div class="form-groupe">
                        <label for="cb-cvv">Cryptogramme (CVV) *</label>
                        <input type="password" id="cb-cvv" name="cvv" maxlength="4" placeholder="123" required>
                    </div>
                </div>

                <div class="securite-info-bloc">
                    🔒 Vos données bancaires sont cryptées et transmises en toute sécurité. Aucune donnée sensible n'est enregistrée sur nos serveurs.
                </div>
            </div>

            <!-- Bloc informations Guichet -->
            <div class="carte" id="bloc-especes" style="margin-top:20px; display:none;">
                <h3>💵 Informations règlement au guichet</h3>
                <p>En choisissant cette option, votre réservation / commande reste enregistrée avec le numéro <strong><?= e($reference) ?></strong>.</p>
                <div class="bandeau-info" style="margin-top:12px;">
                    Veuillez vous présenter au guichet billetterie du parc au moins 15 minutes avant le début de votre visite pour régler et retirer vos accès.
                </div>
            </div>

            <!-- Bloc informations Virement -->
            <div class="carte" id="bloc-virement" style="margin-top:20px; display:none;">
                <h3>📱 Coordonnées bancaires FunPark</h3>
                <p>Effectuez votre virement vers les coordonnées bancaires ci-dessous en précisant impérativement votre référence :</p>
                <div style="background:#f1f5f9; padding:12px; border-radius:8px; margin-top:10px; font-family:monospace; line-height:1.6;">
                    <strong>Banque :</strong> Banque Populaire / Attijariwafa<br>
                    <strong>RIB :</strong> 011 780 0000123456789012 34<br>
                    <strong>Bénéficiaire :</strong> FUNPARK CASABLANCA SARL<br>
                    <strong>Motif :</strong> <?= e($reference) ?>
                </div>
            </div>

            <div style="margin-top:24px;">
                <button type="submit" id="btn-valider-paiement" class="btn btn-hero btn-large" style="width:100%;">
                    🔒 Confirmer et Payer <?= montant($montantTotal) ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Colonne droite : Synthèse de la commande -->
    <div class="paiement-synthese-section">
        <div class="carte carte-recap-flottante">
            <h3>📋 Récapitulatif</h3>
            <p class="texte-muet">Réf : <strong><?= e($reference) ?></strong></p>
            
            <div style="margin-top:14px; border-top:1px solid #e2e8f0; padding-top:14px;">
                <?php if ($type === 'achat'): ?>
                    <strong>🎟️ Billets commandés :</strong>
                    <div style="margin-top:8px;">
                        <?php foreach ($objet['lignes'] as $l): ?>
                            <div class="ligne-recap-item">
                                <span><?= (int)$l['quantite'] ?>x <?= e($l['type_nom']) ?></span>
                                <strong><?= montant($l['total_ligne']) ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <strong>🎡 Détail de la réservation :</strong>
                    <p style="margin:6px 0;"><?= e($objet['jeux']) ?></p>
                    <p class="texte-muet">Pour <?= (int)$objet['nombre_personnes'] ?> personne(s)</p>
                <?php endif; ?>
                
                <p class="texte-muet" style="margin-top:10px;">
                    📅 Visite prévue le : <strong><?= dateFr($objet['date_visite']) ?></strong>
                </p>
            </div>

            <hr class="separateur-recap">

            <div class="ligne-totaux">
                <div class="total-ligne">
                    <span>Montant brut</span>
                    <strong><?= montant($montantBrut) ?></strong>
                </div>

                <?php if ($remise > 0): ?>
                <div class="total-ligne" style="color:#10b981;">
                    <span>Remise Promo (<?= e($codePromo) ?>)</span>
                    <strong>- <?= montant($remise) ?></strong>
                </div>
                <?php endif; ?>

                <div class="total-ligne total-principal">
                    <span>Total Net à payer</span>
                    <strong><?= montant($montantTotal) ?></strong>
                </div>
            </div>

            <div class="garanties-recap-bloc">
                <div>🛡️ <strong>Garantie FunPark :</strong> Billet remplaçable ou modifiable jusqu'à 24h avant la date de visite.</div>
            </div>
        </div>
    </div>
</div>

<script>
function choisirMode(mode) {
    document.querySelectorAll('.option-paiement-radio').forEach(function(el) { el.classList.remove('actif'); });
    var radio = document.getElementById('radio-' + mode);
    if (radio) {
        radio.checked = true;
        radio.closest('.option-paiement-radio').classList.add('actif');
    }

    document.getElementById('bloc-carte').style.display = (mode === 'carte') ? 'block' : 'none';
    document.getElementById('bloc-especes').style.display = (mode === 'especes') ? 'block' : 'none';
    document.getElementById('bloc-virement').style.display = (mode === 'virement') ? 'block' : 'none';

    var btn = document.getElementById('btn-valider-paiement');
    if (mode === 'carte') {
        btn.innerHTML = '🔒 Régler par carte bancaire (' + '<?= montant($montantTotal) ?>' + ')';
        setChampsCarteRequis(true);
    } else if (mode === 'especes') {
        btn.innerHTML = '💵 Confirmer la commande (Règlement au guichet)';
        setChampsCarteRequis(false);
    } else {
        btn.innerHTML = '📱 Confirmer le virement (' + '<?= montant($montantTotal) ?>' + ')';
        setChampsCarteRequis(false);
    }
}

function setChampsCarteRequis(requis) {
    ['cb-nom', 'cb-num', 'cb-exp', 'cb-cvv'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            if (requis) el.setAttribute('required', 'required');
            else el.removeAttribute('required');
        }
    });
}

function majVisuNom(val) {
    document.getElementById('visu-cb-nom').textContent = val.toUpperCase() || 'NOM DU TITULAIRE';
}

function majVisuNum(input) {
    var val = input.value.replace(/\D/g, '').substring(0, 16);
    var formatted = val.match(/.{1,4}/g)?.join(' ') || '';
    input.value = formatted;
    document.getElementById('visu-cb-num').textContent = formatted || '•••• •••• •••• ••••';
}

function majVisuExp(input) {
    var val = input.value.replace(/\D/g, '').substring(0, 4);
    if (val.length >= 2) {
        val = val.substring(0, 2) + '/' + val.substring(2);
    }
    input.value = val;
    document.getElementById('visu-cb-exp').textContent = val || 'MM/AA';
}
</script>
