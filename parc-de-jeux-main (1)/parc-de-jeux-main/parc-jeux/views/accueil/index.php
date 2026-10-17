<?php $titrePage = 'FunPark — Accueil & Réservations d\'attractions'; ?>
<section class="hero">
    <div class="hero-badge">🌟 Le parc d'attractions n°1 de la région</div>
    <h1>Sensations, Rires & Souvenirs inoubliables !</h1>
    <p>Découvrez nos attractions sensationnelles, manèges familiaux et espaces enfants. Achetez vos billets en ligne, réservez vos attractions et profitez de réductions exclusives avec nos codes promo !</p>
    <div class="hero-actions">
        <a class="btn btn-hero" href="<?= e(url('achats', 'billetterie')) ?>">🎟️ Acheter mes billets en ligne</a>
        <a class="btn btn-hero-alt" href="<?= e(url('reservations', 'creer')) ?>">🎯 Réserver des attractions</a>
    </div>
    <div class="hero-stats">
        <div class="hero-stat"><strong>15+</strong><span>Attractions</span></div>
        <div class="hero-stat"><strong>100%</strong><span>Paiement sécurisé</span></div>
        <div class="hero-stat"><strong>4.9 ★</strong><span>Avis visiteurs</span></div>
    </div>
</section>

<?php bandeauBilletterie(); ?>

<section class="section" id="nos-jeux">
    <div class="section-header">
        <div>
            <h2 class="section-titre">Nos Attractions Phares</h2>
            <p class="section-sous">Choisissez vos attractions selon l’âge, la durée et les sensations recherchées.</p>
        </div>
        <a class="lien-tout" href="<?= e(url('jeux')) ?>">Voir toutes les attractions (<?= count($jeux) ?>) →</a>
    </div>
    <div class="grille-jeux">
        <?php foreach ($jeux as $j): ?>
        <article class="carte-jeu">
            <div class="carte-image-wrapper">
                <img src="<?= e(imageJeu($j)) ?>" alt="<?= e($j['nom']) ?>" class="carte-jeu-img" loading="lazy">
                <span class="badge-categorie"><?= e($j['categorie']) ?></span>
                <div class="badge-etat-overlay"><?= badge($j['etat']) ?></div>
            </div>
            <div class="carte-jeu-corps">
                <h3><a href="<?= e(url('jeux', 'detail', ['id' => $j['id_jeu']])) ?>"><?= e($j['nom']) ?></a></h3>
                <p class="desc-courte"><?= e($j['description']) ?></p>
                <div class="caracteristiques">
                    <span>👶 Dès <?= (int)$j['age_minimum'] ?> ans</span>
                    <span>⏱️ <?= (int)$j['duree_session'] ?> min/session</span>
                    <span>👥 Capacité : <?= (int)$j['capacite'] ?> pers.</span>
                </div>
                <div class="carte-jeu-pied">
                    <div class="prix-bloc">
                        <span class="prix-libelle">Tarif par pers.</span>
                        <strong class="prix"><?= montant($j['tarif']) ?></strong>
                    </div>
                    <a class="btn btn-sm" href="<?= e(url('jeux', 'detail', ['id' => $j['id_jeu']])) ?>">Découvrir</a>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
        <?php if (!$jeux): ?><p class="vide">Aucune attraction disponible pour le moment.</p><?php endif; ?>
    </div>
</section>

<?php if (!empty($promotions)): ?>
<section class="section" id="promotions">
    <div class="section-header">
        <div>
            <h2 class="section-titre">🎁 Offres & Promotions du Moment</h2>
            <p class="section-sous">Économisez sur vos billets et attractions grâce à nos codes promotionnels exclusifs.</p>
        </div>
        <a class="lien-tout" href="<?= e(url('promotions')) ?>">Voir tous les bons plans (<?= count($promotions) ?>) →</a>
    </div>
    <div class="grille-promos-publiques">
        <?php foreach (array_slice($promotions, 0, 3) as $p): ?>
        <article class="carte-promo-vip">
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
            <div class="carte-promo-code-bloc">
                <span class="code-texte"><?= e($p['code']) ?></span>
                <a class="btn-copier-code" href="<?= e(url('achats', 'billetterie', ['code' => $p['code']])) ?>">
                    Appliquer ➔
                </a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="section" id="tarifs">
    <div class="section-header">
        <div>
            <h2 class="section-titre">Formules & Billets d'accès</h2>
            <p class="section-sous">Achetez vos pass en ligne ou réservez pour une expérience sans file d'attente.</p>
        </div>
        <a class="lien-tout" href="<?= e(url('achats', 'billetterie')) ?>">Accéder à la billetterie complète →</a>
    </div>
    <div class="grille-tarifs">
        <?php foreach ($types as $t): ?>
        <article class="carte-tarif">
            <div class="tarif-icon">🎫</div>
            <h3><?= e($t['nom']) ?></h3>
            <strong class="tarif-montant"><?= montant($t['tarif']) ?></strong>
            <span class="tarif-validite">Valable <?= (int)$t['duree_validite'] ?> jour(s)</span>
            <div style="display:flex; flex-direction:column; gap:6px; margin-top:10px;">
                <a class="btn btn-sm" href="<?= e(url('achats', 'billetterie')) ?>">Acheter en ligne</a>
                <a class="btn btn-sm gris" href="<?= e(url('reservations', 'creer')) ?>">Réserver</a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <h2 class="section-titre">Comment préparer votre journée</h2>
    <div class="etapes">
        <article class="etape">
            <div class="num">1</div>
            <h3>Commandez vos billets</h3>
            <p>Sélectionnez vos pass ou vos attractions préférées en quelques clics.</p>
        </article>
        <article class="etape">
            <div class="num">2</div>
            <h3>Bénéficiez des promotions</h3>
            <p>Appliquez un code promo au panier pour bénéficier d'une réduction immédiate.</p>
        </article>
        <article class="etape">
            <div class="num">3</div>
            <h3>Paiement sécurisé ou sur place</h3>
            <p>Réglez en ligne par carte bancaire avec e-billet immédiat ou payez au guichet.</p>
        </article>
    </div>
</section>

