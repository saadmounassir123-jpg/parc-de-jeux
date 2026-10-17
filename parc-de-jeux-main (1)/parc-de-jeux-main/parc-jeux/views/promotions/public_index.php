<?php $titrePage = 'Offres Spéciales & Codes Promo — FunPark'; ?>
<div class="hero-promo-banniere">
    <span class="hero-badge">🎁 Bons Plans & Remises</span>
    <h1>Nos Meilleures Offres & Codes Promo</h1>
    <p>Profitez de réductions exclusives sur vos billets d'entrée et vos réservations d'attractions ! Copiez le code de votre choix et appliquez-le lors de votre commande.</p>
</div>

<div class="grille-promos-publiques">
    <?php foreach ($promotions as $p): ?>
    <article class="carte-promo-vip apparition">
        <div class="carte-promo-ruban">
            <?= $p['type_reduction'] === 'pourcentage' ? '-' . (float)$p['valeur'] . '%' : '-' . montant($p['valeur']) ?>
        </div>
        <div class="carte-promo-header">
            <span class="promo-icone">🎉</span>
            <div>
                <h3><?= e($p['titre']) ?></h3>
                <p class="promo-desc"><?= e($p['description']) ?></p>
            </div>
        </div>

        <div class="carte-promo-details">
            <?php if ((float)$p['montant_minimum'] > 0): ?>
                <div class="promo-condition">🛒 Minimum d'achat : <strong><?= montant($p['montant_minimum']) ?></strong></div>
            <?php endif; ?>
            <?php if (!empty($p['date_fin'])): ?>
                <div class="promo-condition">⏳ Valable jusqu'au : <strong><?= dateFr($p['date_fin']) ?></strong></div>
            <?php endif; ?>
        </div>

        <div class="carte-promo-code-bloc">
            <span class="code-texte"><?= e($p['code']) ?></span>
            <button class="btn-copier-code" type="button" onclick="copierCode('<?= e($p['code']) ?>', this)">
                📋 Copier
            </button>
        </div>

        <div class="carte-promo-actions">
            <a class="btn btn-sm" href="<?= e(url('achats', 'billetterie', ['code' => $p['code']])) ?>">🎟️ Acheter des billets</a>
            <a class="btn btn-sm gris" href="<?= e(url('reservations', 'creer', ['code' => $p['code']])) ?>">🎡 Réserver un jeu</a>
        </div>
    </article>
    <?php endforeach; ?>

    <?php if (!$promotions): ?>
        <p class="vide">Aucune promotion en cours pour le moment. Revenez très bientôt pour nos prochains bons plans !</p>
    <?php endif; ?>
</div>

<div class="carte-info-utilisation" style="margin-top:40px; background:#fff; border-radius:16px; padding:24px; border:1px solid #e2e8f0;">
    <h3>💡 Comment utiliser votre code promo ?</h3>
    <ol style="margin:12px 0 0 20px; line-height:1.7; color:#475569;">
        <li>Choisissez vos billets dans la <strong><a href="<?= e(url('achats', 'billetterie')) ?>">Billetterie en ligne</a></strong> ou sélectionnez vos attractions.</li>
        <li>Dans le formulaire de commande, saisissez le code promo dans le champ prévu à cet effet.</li>
        <li>Cliquez sur <strong>Appliquer</strong> : la remise se déduit immédiatement du montant total !</li>
        <li>Procédez au paiement sécurisé par carte bancaire ou optez pour le règlement au guichet.</li>
    </ol>
</div>

<script>
function copierCode(code, btn) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(code).then(function() {
            var oldText = btn.innerHTML;
            btn.innerHTML = '✅ Copié !';
            btn.style.background = '#10b981';
            btn.style.color = '#fff';
            setTimeout(function() {
                btn.innerHTML = oldText;
                btn.style.background = '';
                btn.style.color = '';
            }, 2000);
        });
    } else {
        alert('Code promo : ' + code);
    }
}
</script>
