<?php 
$titrePage = 'Billetterie en ligne — Achetez vos billets FunPark'; 
$role = roleActuel();
$preCode = $_GET['code'] ?? '';
?>

<div class="shop-hero">
    <span class="hero-badge">🎟️ Billetterie Officielle</span>
    <h1>Achetez vos billets en ligne</h1>
    <p>Sélectionnez vos pass et entrées, profitez de paiements 100% sécurisés et appliquez nos codes promo pour des tarifs imbattables !</p>
</div>

<?php if (!empty($promotions)): ?>
<div class="bandeau-promos-dispo">
    <span>🔥 <strong>Codes promo du moment :</strong></span>
    <div class="promos-pills">
        <?php foreach ($promotions as $promo): ?>
            <button type="button" class="promo-pill" onclick="appliquerCodeDepuisPill('<?= e($promo['code']) ?>')">
                <strong><?= e($promo['code']) ?></strong> (<?= $promo['type_reduction'] === 'pourcentage' ? '-' . (float)$promo['valeur'] . '%' : '-' . montant($promo['valeur']) ?>)
            </button>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<form method="post" action="<?= e(url('achats', 'creer')) ?>" id="form-achat" class="billetterie-layout">
    
    <!-- Colonne gauche : Sélection des billets et options -->
    <div class="billetterie-selection">
        
        <?php if (estPersonnel()): ?>
        <div class="carte carte-form-section" style="border-left: 4px solid var(--primaire);">
            <h3>👤 Client bénéficiaire de la commande</h3>
            <label for="select-id-client">Sélectionner pour qui vous passez cette commande :</label>
            <select name="id_client" id="select-id-client" style="font-weight: 500;">
                <option value="moi">⭐ Passer la commande pour mon propre compte (<?= e(nomComplet()) ?> - Personnel)</option>
                <optgroup label="Sélectionner un client du parc">
                    <?php foreach ((new Client(getPDO()))->tous() as $c): ?>
                        <option value="<?= $c['id_client'] ?>"><?= e($c['prenom'] . ' ' . $c['nom']) ?> (<?= e($c['email']) ?>)</option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
            <small class="texte-muet">En tant que membre de l'équipe (<?= e(roleActuel()) ?>), vous pouvez commander pour vous-même ou pour un client enregistré.</small>
        </div>
        <?php endif; ?>

        <div class="carte carte-form-section">
            <h3>📅 Date de visite prévue</h3>
            <label>Choisissez le jour de votre venue au parc *</label>
            <input type="date" name="date_visite" id="date-visite" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
            <small class="texte-muet">Les billets sont valables à partir de la date sélectionnée selon la durée de validité du titre.</small>
        </div>

        <div class="carte carte-form-section">
            <h3>🎟️ Choix des formules & billets</h3>
            <p class="texte-muet" style="margin-bottom:16px;">Sélectionnez le nombre de billets souhaités pour chaque formule :</p>
            
            <div class="grille-billets-achat">
                <?php foreach ($types as $t): ?>
                <div class="item-billet-achat" data-id="<?= (int)$t['id_type_billet'] ?>" data-prix="<?= (float)$t['tarif'] ?>">
                    <div class="item-billet-infos">
                        <h4><?= e($t['nom']) ?></h4>
                        <div class="item-billet-tarif"><?= montant($t['tarif']) ?> <small>/ billet</small></div>
                        <span class="item-billet-validite">⏱️ Valable <?= (int)$t['duree_validite'] ?> jour(s)</span>
                    </div>
                    
                    <div class="compteur-qte">
                        <button type="button" class="btn-qte" onclick="ajusterQte(<?= (int)$t['id_type_billet'] ?>, -1)">−</button>
                        <input type="number" 
                               name="quantites[<?= (int)$t['id_type_billet'] ?>]" 
                               id="qte-<?= (int)$t['id_type_billet'] ?>" 
                               class="input-qte" 
                               value="0" 
                               min="0" 
                               max="50" 
                               data-prix="<?= (float)$t['tarif'] ?>"
                               oninput="recalculerPanier()">
                        <button type="button" class="btn-qte" onclick="ajusterQte(<?= (int)$t['id_type_billet'] ?>, 1)">+</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Colonne droite : Résumé de commande & Paiement -->
    <div class="billetterie-recap">
        <div class="carte carte-recap-flottante">
            <h3>🛒 Votre Panier</h3>
            
            <div id="recap-lignes-panier" class="recap-lignes-panier">
                <p class="texte-muet aucun-billet">Aucun billet sélectionné pour le moment.</p>
            </div>

            <hr class="separateur-recap">

            <!-- Code Promo -->
            <div class="bloc-code-promo">
                <label for="input-code-promo">🎁 Vous avez un code promo ?</label>
                <div class="champ-avec-bouton">
                    <input type="text" name="code_promo" id="input-code-promo" value="<?= e($preCode) ?>" placeholder="Ex: FUN20, BIENVENUE10" style="text-transform:uppercase;">
                    <button type="button" id="btn-appliquer-promo" class="btn btn-sm" onclick="verifierCodePromo()">Appliquer</button>
                </div>
                <div id="message-promo" class="message-promo"></div>
            </div>

            <hr class="separateur-recap">

            <div class="ligne-totaux">
                <div class="total-ligne">
                    <span>Sous-total brut</span>
                    <strong id="sous-total-txt">0,00 DH</strong>
                </div>
                <div class="total-ligne ligne-remise" id="ligne-remise" style="display:none; color:#10b981;">
                    <span>Remise promotionnelle</span>
                    <strong id="remise-txt">- 0,00 DH</strong>
                </div>
                <div class="total-ligne total-principal">
                    <span>Total à régler</span>
                    <strong id="total-final-txt">0,00 DH</strong>
                </div>
            </div>

            <div class="recap-securite-badges">
                <span>🔒 Paiement 100% sécurisé</span>
                <span>⚡ Billets électroniques immédiats</span>
                <span>💳 CB / Visa / Mastercard / Guichet</span>
            </div>

            <button type="submit" id="btn-commander" class="btn btn-hero btn-large" style="width:100%; margin-top:16px;" disabled>
                💳 Valider & Régler la commande
            </button>
            <p class="texte-centre-petit" style="margin-top:8px; font-size:12px; color:#64748b;">
                Présentez votre billet sur smartphone ou imprimé à l'entrée du parc.
            </p>
        </div>
    </div>
</form>

<script>
var sousTotal = 0;
var remiseActuelle = 0;
var promoValideCode = '';

function ajusterQte(id, delta) {
    var input = document.getElementById('qte-' + id);
    if (!input) return;
    var val = parseInt(input.value) || 0;
    val = Math.max(0, val + delta);
    input.value = val;
    recalculerPanier();
}

function appliquerCodeDepuisPill(code) {
    var inp = document.getElementById('input-code-promo');
    if (inp) {
        inp.value = code;
        verifierCodePromo();
    }
}

function recalculerPanier() {
    var inputs = document.querySelectorAll('.input-qte');
    var recapContainer = document.getElementById('recap-lignes-panier');
    recapContainer.innerHTML = '';

    sousTotal = 0;
    var totalBillets = 0;

    inputs.forEach(function(inp) {
        var qte = parseInt(inp.value) || 0;
        if (qte > 0) {
            var prix = parseFloat(inp.getAttribute('data-prix')) || 0;
            var ligneTot = qte * prix;
            sousTotal += ligneTot;
            totalBillets += qte;

            var parent = inp.closest('.item-billet-achat');
            var titre = parent ? parent.querySelector('h4').textContent : 'Billet';

            var itemDiv = document.createElement('div');
            itemDiv.className = 'ligne-recap-item';
            itemDiv.innerHTML = '<span>' + qte + 'x ' + titre + '</span><strong>' + formaterMontant(ligneTot) + '</strong>';
            recapContainer.appendChild(itemDiv);
        }
    });

    if (totalBillets === 0) {
        recapContainer.innerHTML = '<p class="texte-muet aucun-billet">Aucun billet sélectionné pour le moment.</p>';
    }

    document.getElementById('sous-total-txt').textContent = formaterMontant(sousTotal);
    
    // Si un code promo était appliqué, le re-vérifier avec le nouveau total
    if (promoValideCode !== '' && sousTotal > 0) {
        verifierCodePromo(true);
    } else {
        majTotauxFinaux(0);
    }

    document.getElementById('btn-commander').disabled = (totalBillets === 0);
}

function majTotauxFinaux(remise) {
    remiseActuelle = remise;
    var finalTot = Math.max(0, sousTotal - remiseActuelle);

    var ligneRemise = document.getElementById('ligne-remise');
    if (remiseActuelle > 0) {
        ligneRemise.style.display = 'flex';
        document.getElementById('remise-txt').textContent = '- ' + formaterMontant(remiseActuelle);
    } else {
        ligneRemise.style.display = 'none';
    }

    document.getElementById('total-final-txt').textContent = formaterMontant(finalTot);
}

function verifierCodePromo(silencieux) {
    var code = document.getElementById('input-code-promo').value.trim();
    var msgDiv = document.getElementById('message-promo');

    if (code === '') {
        promoValideCode = '';
        majTotauxFinaux(0);
        msgDiv.innerHTML = '';
        return;
    }

    if (sousTotal <= 0) {
        if (!silencieux) {
            msgDiv.innerHTML = '<span style="color:#ef4444;">⚠️ Veuillez d\'abord ajouter des billets au panier.</span>';
        }
        return;
    }

    fetch('index.php?page=promotions&action=verifier&code=' + encodeURIComponent(code) + '&montant=' + sousTotal)
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.valide) {
                promoValideCode = code;
                majTotauxFinaux(data.remise);
                msgDiv.innerHTML = '<span style="color:#10b981;">✅ ' + data.message + ' (- ' + formaterMontant(data.remise) + ')</span>';
            } else {
                promoValideCode = '';
                majTotauxFinaux(0);
                msgDiv.innerHTML = '<span style="color:#ef4444;">❌ ' + data.message + '</span>';
            }
        })
        .catch(function() {
            msgDiv.innerHTML = '<span style="color:#ef4444;">Erreur lors de la vérification du code.</span>';
        });
}

document.addEventListener('DOMContentLoaded', function() {
    recalculerPanier();
    var preCode = '<?= e($preCode) ?>';
    if (preCode) {
        // Laisser le temps à l'utilisateur de choisir
    }
});
</script>
