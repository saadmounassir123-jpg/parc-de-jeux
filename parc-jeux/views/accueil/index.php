<?php $titrePage = 'FunPark — Accueil'; ?>
<section class="hero">
    <h1>Bienvenue à FunPark 🎢</h1>
    <p>Manèges, sensations et sourires : réservez vos jeux en ligne, puis retirez vos billets à la billetterie du parc.</p>
    <a class="btn" href="<?= e(url('reservations', 'creer')) ?>">Réserver maintenant</a>
</section>

<?php if (function_exists('bandeauBilletterie')) bandeauBilletterie(); ?>

<section class="section" id="nos-jeux">
    <h2 class="section-titre">Nos jeux</h2>
    <p class="section-sous">Choisissez vos attractions selon l'âge, la durée et l'ambiance.</p>
    <div class="grille-jeux">
        <?php foreach ($jeux as $j): ?>
        <article class="carte-jeu" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
            <?php $imgFile = 'public/img/jeu_' . $j['id_jeu'] . '.jpg'; ?>
            <?php if (file_exists(RACINE . '/' . $imgFile)): ?>
                <img src="<?= e($imgFile) ?>" alt="<?= e($j['nom']) ?>" style="width: 100%; height: 200px; object-fit: cover;">
            <?php else: ?>
                <div style="width: 100%; height: 200px; background: #cbd5e1; display:flex; align-items:center; justify-content:center; color: white;">Image indisponible</div>
            <?php endif; ?>
            
            <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                <div><?= badge($j['etat']) ?> <span class="badge" style="background:#e2e8f0; color:#475569;"><?= e($j['categorie']) ?></span></div>
                <h3 style="margin-top: 1rem;"><a href="<?= e(url('jeux', 'detail', ['id' => $j['id_jeu']])) ?>"><?= e($j['nom']) ?></a></h3>
                <p class="meta">Dès <?= (int)$j['age_minimum'] ?> ans • <?= (int)$j['duree_session'] ?> min</p>
                <p class="prix" style="margin-top: auto; padding-top: 1rem;"><?= montant($j['tarif']) ?> / pers.</p>
                <a class="btn violet" style="display:block; text-align:center;" href="<?= e(url('jeux', 'detail', ['id' => $j['id_jeu']])) ?>">Voir le détail</a>
            </div>
        </article>
        <?php endforeach; ?>
        <?php if (!$jeux): ?><p>Aucun jeu n'est affiché pour le moment.</p><?php endif; ?>
    </div>
    <p style="text-align:center;margin-top:2rem"><a class="btn" href="<?= e(url('jeux')) ?>">Voir tous les jeux</a></p>
</section>

<section class="section" id="tarifs">
    <h2 class="section-titre">Tarifs</h2>
    <p class="section-sous">Consultez les prix. L'achat se fait uniquement au guichet.</p>
    <div class="grille-tarifs">
        <?php foreach ($types as $t): ?>
        <article class="carte-tarif">
            <span><?= e($t['nom']) ?></span>
            <strong><?= montant($t['tarif']) ?></strong>
            <small>Valable <?= (int)$t['duree_validite'] ?> jour(s)</small>
        </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <h2 class="section-titre">Comment ça marche ?</h2>
    <div class="etapes">
        <article class="etape">
            <div class="num">1</div>
            <h3>Je réserve en ligne</h3>
            <p>Choisissez votre date, nombre de personnes et jeux. C'est gratuit et sans paiement.</p>
        </article>
        <article class="etape">
            <div class="num">2</div>
            <h3>Je me présente à la billetterie</h3>
            <p>Un agent du parc confirme votre réservation à l'aide de votre numéro.</p>
        </article>
        <article class="etape">
            <div class="num">3</div>
            <h3>J'achète mes billets et je profite</h3>
            <p>Paiement sur place (espèces ou carte), puis place aux attractions !</p>
        </article>
    </div>
</section>
