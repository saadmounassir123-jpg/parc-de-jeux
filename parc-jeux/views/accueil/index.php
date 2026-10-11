<?php $titrePage = 'FunPark — Accueil & Réservations d\'attractions'; ?>
<section class="hero">
    <div class="hero-badge">🌟 Le parc d'attractions n°1 de la région</div>
    <h1>Sensations, Rires & Souvenirs inoubliables !</h1>
    <p>Découvrez nos attractions sensationnelles, manèges familiaux et espaces enfants. Réservez votre visite gratuitement en ligne et profitez d'un accès rapide.</p>
    <div class="hero-actions">
        <a class="btn btn-hero" href="<?= e(url('reservations', 'creer')) ?>">🎯 Réserver ma visite gratuite</a>
        <a class="btn btn-hero-alt" href="#nos-jeux">🎡 Découvrir les jeux</a>
    </div>
    <div class="hero-stats">
        <div class="hero-stat"><strong>15+</strong><span>Attractions</span></div>
        <div class="hero-stat"><strong>100%</strong><span>Sécurité garantie</span></div>
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

<section class="section" id="tarifs">
    <h2 class="section-titre">Grille Tarifaire du Guichet</h2>
    <p class="section-sous">Consultez nos pass et formules d'accès au parc.</p>
    <div class="grille-tarifs">
        <?php foreach ($types as $t): ?>
        <article class="carte-tarif">
            <div class="tarif-icon">🎫</div>
            <h3><?= e($t['nom']) ?></h3>
            <strong class="tarif-montant"><?= montant($t['tarif']) ?></strong>
            <span class="tarif-validite">Valable <?= (int)$t['duree_validite'] ?> jour(s)</span>
            <a class="btn btn-sm gris" href="<?= e(url('reservations', 'creer')) ?>">Réserver</a>
        </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <h2 class="section-titre">Comment préparer votre journée</h2>
    <div class="etapes">
        <article class="etape">
            <div class="num">1</div>
            <h3>Réservez en ligne</h3>
            <p>Choix de la date, du nombre de personnes et de vos jeux favoris. C'est 100% gratuit !</p>
        </article>
        <article class="etape">
            <div class="num">2</div>
            <h3>Présentez votre numéro</h3>
            <p>À votre arrivée, communiquez votre code de réservation à l'accueil du guichet.</p>
        </article>
        <article class="etape">
            <div class="num">3</div>
            <h3>Réglez et Amusez-vous</h3>
            <p>Règlement par espèces ou carte bancaire à l'accueil pour accéder directement aux attractions.</p>
        </article>
    </div>
</section>

