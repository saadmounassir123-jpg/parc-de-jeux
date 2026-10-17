<?php $titrePage = 'Nouvelle réservation'; $role = roleActuel(); ?>
<h1>Nouvelle réservation</h1>
<?php bandeauBilletterie(); ?>
<?php afficherErreurs($erreurs); ?>
<form method="post" class="formulaire validation">
    <?php if (in_array($role, ['admin', 'agent'], true)): ?>
    <label>Client * <a href="<?= e(url('clients', 'ajouter')) ?>">(nouveau client)</a></label>
    <select name="id_client" required><option value="">-- Choisir --</option>
        <?php foreach ($clients as $c): ?><option value="<?= $c['id_client'] ?>" <?= selectionne($form['id_client'], $c['id_client']) ?>><?= e($c['prenom'] . ' ' . $c['nom']) ?> (<?= e(libelle($c['type_client'])) ?>)</option><?php endforeach; ?>
    </select>
    <?php endif; ?>
    <label>Date de visite *</label>
    <input type="date" name="date_visite" min="<?= date('Y-m-d') ?>" value="<?= e($form['date_visite']) ?>" required>
    <label>Nombre de personnes (famille ou groupe) *</label>
    <input type="number" id="nb-personnes" name="nombre_personnes" min="1" value="<?= e($form['nombre_personnes']) ?>" required>
    <label>Jeux choisis *</label>
    <?php foreach ($jeux as $j): ?>
        <label class="case">
            <input type="checkbox" class="jeu-choisi" name="jeux[]" value="<?= (int)$j['id_jeu'] ?>" data-prix="<?= e($j['tarif']) ?>"
                   <?= in_array($j['id_jeu'], $form['jeux']) ? 'checked' : '' ?>>
            <span><?= e($j['nom']) ?> — <?= montant($j['tarif']) ?>/pers. <small>(capacité <?= (int)$j['capacite'] ?>, âge min. <?= (int)$j['age_minimum'] ?> ans)</small></span>
        </label>
    <?php endforeach; ?>
    <?php if (!empty($promotions)): ?>
    <div style="margin: 16px 0 8px;">
        <span style="font-size:13px; font-weight:600; color:#475569;">🎁 Offres promo disponibles :</span>
        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:4px;">
            <?php foreach ($promotions as $pr): ?>
                <button type="button" class="promo-pill" onclick="appliquerCodeResa('<?= e($pr['code']) ?>')">
                    <strong><?= e($pr['code']) ?></strong> (<?= $pr['type_reduction'] === 'pourcentage' ? '-' . (float)$pr['valeur'] . '%' : '-' . montant($pr['valeur']) ?>)
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <label for="code-promo-resa">Code promotionnel (optionnel)</label>
    <div style="display:flex; gap:8px; margin-bottom:12px;">
        <input type="text" id="code-promo-resa" name="code_promo" value="<?= e($form['code_promo'] ?? '') ?>" placeholder="Ex: FUN20, BIENVENUE10" style="text-transform:uppercase; max-width:260px;">
        <button type="button" class="btn btn-sm" onclick="verifierPromoResa()">Appliquer</button>
    </div>
    <div id="msg-promo-resa" style="font-size:13px; margin-bottom:14px;"></div>

    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px; margin: 16px 0;">
        <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
            <span>Sous-total jeux :</span>
            <span id="sous-total-resa">0,00 DH</span>
        </div>
        <div id="ligne-remise-resa" style="display:none; justify-content:space-between; margin-bottom:4px; color:#10b981;">
            <span>Remise promo :</span>
            <span id="remise-resa">- 0,00 DH</span>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:16px; font-weight:700; border-top:1px solid #e2e8f0; padding-top:6px; margin-top:6px;">
            <span>Montant estimé :</span>
            <strong id="total-resa" style="color:var(--couleur-primaire, #f97316);">0,00 DH</strong>
        </div>
    </div>

    <button class="btn" type="submit">🎯 Réserver & Accéder au paiement</button>
    <a class="btn gris" href="<?= e(estPersonnel() ? url('reservations') : url('accueil')) ?>">Annuler</a>
</form>

<script>
var sousTotalResa = 0;
var remiseResa = 0;

function recalculerTotalResaComplet() {
    var nb = parseInt(document.getElementById('nb-personnes').value) || 0;
    sousTotalResa = 0;
    document.querySelectorAll('.jeu-choisi').forEach(function (c) {
        if (c.checked) {
            sousTotalResa += parseFloat(c.getAttribute('data-prix')) * nb;
        }
    });

    document.getElementById('sous-total-resa').textContent = formaterMontant(sousTotalResa);
    majAffichageTotalResa();
}

function majAffichageTotalResa() {
    var finalVal = Math.max(0, sousTotalResa - remiseResa);
    var ligneRemise = document.getElementById('ligne-remise-resa');
    if (remiseResa > 0) {
        ligneRemise.style.display = 'flex';
        document.getElementById('remise-resa').textContent = '- ' + formaterMontant(remiseResa);
    } else {
        ligneRemise.style.display = 'none';
    }
    document.getElementById('total-resa').textContent = formaterMontant(finalVal);
}

function appliquerCodeResa(code) {
    var inp = document.getElementById('code-promo-resa');
    if (inp) {
        inp.value = code;
        verifierPromoResa();
    }
}

function verifierPromoResa() {
    var code = document.getElementById('code-promo-resa').value.trim();
    var msg = document.getElementById('msg-promo-resa');
    if (!code) {
        remiseResa = 0;
        majAffichageTotalResa();
        msg.innerHTML = '';
        return;
    }
    if (sousTotalResa <= 0) {
        msg.innerHTML = '<span style="color:#ef4444;">⚠️ Sélectionnez au moins un jeu pour appliquer le code.</span>';
        return;
    }
    fetch('index.php?page=promotions&action=verifier&code=' + encodeURIComponent(code) + '&montant=' + sousTotalResa)
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.valide) {
                remiseResa = data.remise;
                majAffichageTotalResa();
                msg.innerHTML = '<span style="color:#10b981;">✅ ' + data.message + '</span>';
            } else {
                remiseResa = 0;
                majAffichageTotalResa();
                msg.innerHTML = '<span style="color:#ef4444;">❌ ' + data.message + '</span>';
            }
        });
}

document.querySelectorAll('.jeu-choisi').forEach(function (c) { 
    c.addEventListener('change', function() {
        recalculerTotalResaComplet();
        if (document.getElementById('code-promo-resa').value.trim()) verifierPromoResa();
    }); 
});
document.getElementById('nb-personnes').addEventListener('input', function() {
    recalculerTotalResaComplet();
    if (document.getElementById('code-promo-resa').value.trim()) verifierPromoResa();
});
recalculerTotalResaComplet();
if (document.getElementById('code-promo-resa').value.trim()) {
    verifierPromoResa();
}
</script>
