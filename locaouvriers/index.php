<?php
/**
 * Page d'accueil : LocaOuvriers
 * Code PHP simplifié et structuré pour une compréhension rapide
 */
require 'includes/fonctions.php';
$titre = 'Accueil';
$pleine_largeur = true;

// 1. Récupération des métiers et du nombre d'ouvriers actifs depuis la base de données
$metiers = $pdo->query("SELECT t.*,
        (SELECT COUNT(*) FROM workers w WHERE w.trade_id = t.id AND w.statut = 'actif') AS nb
        FROM trades t ORDER BY t.nom")->fetchAll();

// 2. Ouvriers vedettes (liste simple pour affichage)
$ouvriers_vedettes = [
    [
        'nom'      => 'Ahmed M.',
        'metier'   => '🌿 Jardinier Paysagiste',
        'note'     => '★ 4.96',
        'missions' => '148 missions',
        'photo'    => 'ouvrier-1.jpg',
        'desc'     => 'Spécialiste de la taille ornementale, tonte et arrosage goutte-à-goutte.',
        'tags'     => ['Ponctuel', 'Soigné', 'Outillé']
    ],
    [
        'nom'      => 'Youssef T.',
        'metier'   => '🔧 Maître Plombier',
        'note'     => '★ 5.0',
        'missions' => '210 missions',
        'photo'    => 'ouvrier-3.jpg',
        'desc'     => 'Expert en dépannage urgent, tuyauterie cuivre, multicouche et chauffe-eau.',
        'tags'     => ['Urgence', 'Diagnostic précis', 'Garanti']
    ],
    [
        'nom'      => 'Hamza B.',
        'metier'   => '⚡ Électricien Bâtiment',
        'note'     => '★ 4.93',
        'missions' => '175 missions',
        'photo'    => 'ouvrier-4.jpg',
        'desc'     => 'Remise aux normes, tableaux disjoncteurs et éclairage moderne.',
        'tags'     => ['Certifié', 'Sécurité totale', 'Rapide']
    ],
    [
        'nom'      => 'Karim E.',
        'metier'   => '🎨 Peintre Décorateur',
        'note'     => '★ 4.91',
        'missions' => '96 missions',
        'photo'    => 'ouvrier-2.jpg',
        'desc'     => 'Application soignée, réfection d\'enduits et finitions haut de gamme.',
        'tags'     => ['Propreté', 'Finitions nettes', 'Conseil']
    ]
];

// 3. Réalisations récentes en images
$realisations = [
    [
        'titre'  => 'Rénovation peinture salon moderne',
        'ville'  => 'Casablanca – Anfa',
        'metier' => '🎨 Peinture & Finition',
        'image'  => 'realisation-1.jpg',
        'desc'   => 'Peinture satinée premium et réfection soignée des plafonds.'
    ],
    [
        'titre'  => 'Taille et réaménagement de jardin',
        'ville'  => 'Rabat – Souissi',
        'metier' => '🌿 Jardinage & Paysage',
        'image'  => 'realisation-2.jpg',
        'desc'   => 'Taille de 40m de haies et engazonnement complet du terrain.'
    ],
    [
        'titre'  => 'Remplacement plomberie et sanitaires',
        'ville'  => 'Marrakech – Guéliz',
        'metier' => '🔧 Plomberie Sanitaire',
        'image'  => 'realisation-3.jpg',
        'desc'   => 'Élimination d\'une fuite encastrée et nouveaux raccordements.'
    ],
    [
        'titre'  => 'Pose spots LED et tableau électrique',
        'ville'  => 'Tanger – Malabata',
        'metier' => '⚡ Électricité Bâtiment',
        'image'  => 'realisation-4.jpg',
        'desc'   => 'Mise aux normes complète avec disjoncteurs différentiels.'
    ],
    [
        'titre'  => 'Gardiennage de propriété privée',
        'ville'  => 'Bouskoura – Ville Verte',
        'metier' => '🛡️ Sécurité & Gardiennage',
        'image'  => 'realisation-5.jpg',
        'desc'   => 'Contrôle d\'accès jour/nuit et sécurisation d\'équipements.'
    ],
    [
        'titre'  => 'Pose carrelage terrasse et muret',
        'ville'  => 'Casablanca – Gauthier',
        'metier' => '🔨 Maçonnerie & Finitions',
        'image'  => 'realisation-6.jpg',
        'desc'   => 'Étanchéité et pose soignée de dalles céramiques hydrofuges.'
    ]
];

// 4. Questions fréquentes (FAQ)
$faqs = [
    [
        'q' => 'Comment les ouvriers sont-ils recrutés et vérifiés ?',
        'r' => 'Chaque ouvrier passe par un contrôle strict : carte d\'identité nationale (CNIE), extrait de casier judiciaire, vérification de ses références et test technique de compétences.'
    ],
    [
        'q' => 'Quels sont les délais pour obtenir un ouvrier chez moi ?',
        'r' => 'Nous pouvons affecter un ouvrier sous 24 heures partout au Maroc, et souvent le jour même selon les disponibilités de nos équipes.'
    ],
    [
        'q' => 'Comment se déroule le calcul du prix et le paiement ?',
        'r' => 'Le calcul est direct et transparent : Tarif journalier × Nombre de jours × Nombre d\'ouvriers. Aucun frais de dossier caché.'
    ],
    [
        'q' => 'Que faire si l\'ouvrier a un empêchement ou tombe malade ?',
        'r' => 'Nous vous affectons immédiatement et gratuitement un ouvrier remplaçant du même métier afin que votre travail ne prenne aucun retard.'
    ]
];

require 'includes/header.php';
?>

<!-- 1. SECTION HERO PRINCIPALE -->
<section class="section-hero-moderne">
  <div class="conteneur grille-hero">
    <div class="hero-texte">
      <div class="pill-vedette">
        <span class="pill-point"></span>
        <span class="pill-texte">La référence des ouvriers qualifiés au Maroc</span>
      </div>

      <h1 class="hero-titre">
        Des ouvriers qualifiés, <br>
        <span class="texte-degrade">à la journée</span> et en toute confiance.
      </h1>

      <p class="hero-sous-titre">
        Jardinier, plombier, électricien, peintre ou agent de sécurité... Réservez en <strong>2 minutes</strong> votre artisan de confiance avec tarif transparent dès 300 DH/jour.
      </p>

      <!-- Barre de réservation rapide -->
      <div class="hero-barre-recherche">
        <form action="<?= $role === 'client' ? BASE . '/client/reserver.php' : BASE . '/register.php' ?>" method="get" class="hero-form-rapide">
          <div class="champ-hero">
            <label for="hero-metier">Corps de métier</label>
            <select name="metier" id="hero-metier" required>
              <option value="">-- Choisir un métier --</option>
              <?php foreach ($metiers as $m): ?>
                <option value="<?= (int)$m['id'] ?>">
                  <?= icone_metier($m['nom']) ?> <?= e($m['nom']) ?> (<?= prix($m['tarif_journalier']) ?>/j)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="champ-hero">
            <label for="hero-date">Date d'intervention</label>
            <input type="date" name="debut" id="hero-date" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
          </div>

          <button type="submit" class="btn-hero-action">
            <span>Trouver un ouvrier</span> →
          </button>
        </form>
      </div>

      <!-- Garanties de réassurance -->
      <div class="hero-puces">
        <div class="puce-item"><span class="puce-icone">✓</span> Artisans vérifiés 100%</div>
        <div class="puce-item"><span class="puce-icone">✓</span> Devis net sans surprise</div>
        <div class="puce-item"><span class="puce-icone">✓</span> Intervention sous 24h</div>
        <div class="puce-item"><span class="puce-icone">✓</span> Assistance 7j/7</div>
      </div>
    </div>

    <!-- Visuel Hero avec photo principale et badges flottants -->
    <div class="hero-visuel">
      <div class="cadre-image-hero">
        <img src="<?= BASE ?>/assets/images/hero-artisan.jpg" alt="Artisan professionnel LocaOuvriers" class="image-artisan-principale">
        <div class="halo-arriere-plan"></div>

        <div class="badge-flottant note-flottante">
          <div class="badge-etoiles">★★★★★</div>
          <div class="badge-note-texte">
            <strong>4.9 / 5</strong>
            <small>+1 850 clients satisfaits</small>
          </div>
        </div>

        <div class="badge-flottant dispo-flottante">
          <div class="icone-flash">⚡</div>
          <div>
            <strong>Intervention Rapide</strong>
            <small>Affectation en < 45 min</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 2. BANDEAU DE STATISTIQUES EN DIRECT -->
<section class="section-stats-chiffres">
  <div class="conteneur grille-stats-chiffres">
    <div class="carte-stat-chiffre">
      <div class="stat-numero">+1 850</div>
      <div class="stat-libelle">Missions réalisées avec succès</div>
    </div>
    <div class="carte-stat-chiffre">
      <div class="stat-numero">98.7%</div>
      <div class="stat-libelle">Taux de clients satisfaits</div>
    </div>
    <div class="carte-stat-chiffre">
      <div class="stat-numero">350+</div>
      <div class="stat-libelle">Ouvriers qualifiés & actifs</div>
    </div>
    <div class="carte-stat-chiffre">
      <div class="stat-numero">< 24h</div>
      <div class="stat-libelle">Délai moyen d'intervention</div>
    </div>
  </div>
</section>

<!-- 3. CATALOGUE DES MÉTIERS (AVEC RECHERCHE INTERACTIVE EN JAVASCRIPT) -->
<section id="metiers" class="section-contenu">
  <div class="conteneur">
    <div class="en-tete-section centre">
      <span class="badge-sous-titre">Nos Corps de Métier</span>
      <h2 class="titre-section">Nos spécialistes et tarifs journaliers</h2>
      <p class="texte-section">Des professionnels outillés et notés par nos clients pour tous vos besoins.</p>

      <!-- Barre de recherche interactive gérée par assets/script.js -->
      <div style="max-width: 460px; margin: 25px auto 0;">
        <input type="text" id="recherche-metier" placeholder="🔍 Filtrer un métier en direct (ex: plombier, jardinier...)" style="box-shadow: var(--ombre-douce);">
      </div>
    </div>

    <div class="grille-metiers-photos">
      <?php foreach ($metiers as $m): ?>
        <div class="carte-metier-photo">
          <div class="boite-image-metier">
            <img src="<?= photo_metier($m['nom']) ?>" alt="<?= e($m['nom']) ?>" loading="lazy">
            <div class="voile-degrade"></div>
            <div class="badge-disponibilite">
              <span class="pastille-verte"></span>
              <span><?= (int)$m['nb'] > 0 ? (int)$m['nb'] . ' disponible(s)' : 'Disponible' ?></span>
            </div>
            <div class="etiquette-tarif-metier">
              <span class="montant"><?= prix($m['tarif_journalier']) ?></span>
              <span class="frequence">/ jour net</span>
            </div>
          </div>

          <div class="corps-metier-photo">
            <div class="ligne-titre-metier">
              <span class="emoticon-metier"><?= icone_metier($m['nom']) ?></span>
              <h3 class="nom-metier"><?= e($m['nom']) ?></h3>
            </div>

            <p class="desc-metier-photo">
              <?= description_metier($m['nom']) ?>
            </p>

            <div class="liste-points-forts">
              <span class="point-fort">★ Outillage pro complet</span>
              <span class="point-fort">★ Note moyenne 4.9/5</span>
            </div>

            <div class="pied-carte-metier">
              <?php if ($role === 'client'): ?>
                <a class="btn-metier-reserver" href="<?= BASE ?>/client/reserver.php?metier=<?= (int)$m['id'] ?>">
                  <span>Réserver cet ouvrier</span> <span>→</span>
                </a>
              <?php else: ?>
                <a class="btn-metier-reserver" href="<?= BASE ?>/register.php">
                  <span>Réserver maintenant</span> <span>→</span>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 4. COMMENT ÇA MARCHE (3 ÉTAPES SIMPLES) -->
<section id="comment-marche" class="section-contenu fond-alt">
  <div class="conteneur">
    <div class="en-tete-section centre">
      <span class="badge-sous-titre">Simple & Rapide</span>
      <h2 class="titre-section">Comment fonctionne LocaOuvriers ?</h2>
      <p class="texte-section">Une démarche 100% transparente en 3 étapes sans complications.</p>
    </div>

    <div class="grille-etapes-modernes">
      <div class="carte-etape-moderne">
        <div class="numero-etape-cercle">01</div>
        <div class="icone-etape-grande">🔍</div>
        <h3 class="titre-etape">Choisissez votre métier & vos dates</h3>
        <p class="texte-etape">Indiquez votre besoin, la date et le nombre d'ouvriers. Le prix est calculé instantanément.</p>
      </div>

      <div class="carte-etape-moderne">
        <div class="numero-etape-cercle">02</div>
        <div class="icone-etape-grande">⚡</div>
        <h3 class="titre-etape">Affectation de l'ouvrier idéal</h3>
        <p class="texte-etape">Nous sélectionnons un artisan vérifié, ponctuel et disponible dans votre zone géographique.</p>
      </div>

      <div class="carte-etape-moderne">
        <div class="numero-etape-cercle">03</div>
        <div class="icone-etape-grande">✨</div>
        <h3 class="titre-etape">Intervention et travail soigné</h3>
        <p class="texte-etape">L'ouvrier arrive outillé sur votre chantier et réalise vos travaux en toute tranquillité.</p>
      </div>
    </div>
  </div>
</section>

<!-- 5. SIMULATEUR DE TARIF EN DIRECT (INTERACTIF EN JAVASCRIPT) -->
<section id="simulateur" class="section-contenu">
  <div class="conteneur">
    <div class="bloc-simulateur-card">
      <div class="simulateur-gauche">
        <span class="badge-sous-titre-jaune">Simulateur en direct</span>
        <h2 class="titre-simulateur">Estimez votre tarif en direct</h2>
        <p class="desc-simulateur">
          Ajustez la durée et le nombre d'ouvriers. Le JavaScript calcule le total immédiatement sans recharger la page.
        </p>

        <div class="simulateur-garanties">
          <div class="garantie-row"><span class="check-vert">✔</span> Tarif fixe à la journée sans frais cachés</div>
          <div class="garantie-row"><span class="check-vert">✔</span> Remplacement garanti et gratuit en cas d'imprévu</div>
          <div class="garantie-row"><span class="check-vert">✔</span> Paiement direct en toute confiance</div>
        </div>
      </div>

      <div class="simulateur-droite">
        <div class="champ-sim">
          <label for="sim-metier">Corps de métier</label>
          <select id="sim-metier">
            <?php foreach ($metiers as $index => $m): ?>
              <option value="<?= (float)$m['tarif_journalier'] ?>" data-id="<?= (int)$m['id'] ?>" <?= $index === 0 ? 'selected' : '' ?>>
                <?= icone_metier($m['nom']) ?> <?= e($m['nom']) ?> (<?= prix($m['tarif_journalier']) ?>/j)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="grille-sim-deux">
          <div class="champ-sim">
            <label for="sim-jours">Durée : <strong id="val-jours" class="val-accent">3</strong> jour(s)</label>
            <input type="range" id="sim-jours" min="1" max="30" value="3" class="slider-moderne">
          </div>

          <div class="champ-sim">
            <label for="sim-ouvriers">Nombre d'ouvriers : <strong id="val-ouvriers" class="val-accent">1</strong></label>
            <input type="range" id="sim-ouvriers" min="1" max="10" value="1" class="slider-moderne">
          </div>
        </div>

        <div class="boite-resultat-sim">
          <div class="details-calcul">
            <span id="sim-tarif-unitaire">0 DH/jour</span>
            <span>•</span>
            <span id="sim-formule">1 ouvrier × 3 jours</span>
          </div>
          <div class="ligne-total-sim">
            <span class="label-total">Montant total estimé :</span>
            <span class="chiffre-total" id="sim-total">0,00 DH</span>
          </div>
        </div>

        <?php if ($role === 'client'): ?>
          <a id="btn-reserver-sim" href="<?= BASE ?>/client/reserver.php" class="btn-action-sim">
            Réserver avec ce calcul →
          </a>
        <?php else: ?>
          <a href="<?= BASE ?>/register.php" class="btn-action-sim">
            Créer un compte et réserver →
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- 6. NOS RÉALISATIONS RÉCENTES EN PHOTOS -->
<section id="realisations" class="section-contenu fond-alt">
  <div class="conteneur">
    <div class="en-tete-section centre">
      <span class="badge-sous-titre">Galerie Travaux</span>
      <h2 class="titre-section">Nos récentes réalisations en images</h2>
      <p class="texte-section">Chantiers réalisés avec succès par nos ouvriers chez nos clients.</p>
    </div>

    <div class="grille-realisations">
      <?php foreach ($realisations as $r): ?>
        <div class="carte-realisation">
          <div class="image-realisation-conteneur">
            <img src="<?= BASE ?>/assets/images/<?= e($r['image']) ?>" alt="<?= e($r['titre']) ?>" loading="lazy">
            <span class="badge-ville"><?= e($r['ville']) ?></span>
          </div>
          <div class="details-realisation">
            <span class="tag-metier"><?= e($r['metier']) ?></span>
            <h4><?= e($r['titre']) ?></h4>
            <p class="discret"><?= e($r['desc']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 7. NOS OUVRIERS VEDETTES -->
<section class="section-contenu">
  <div class="conteneur">
    <div class="en-tete-section centre">
      <span class="badge-sous-titre">Nos Meilleurs Profils</span>
      <h2 class="titre-section">Faites connaissance avec nos experts</h2>
      <p class="texte-section">Des artisans notés, expérimentés et vérifiés par notre équipe.</p>
    </div>

    <div class="grille-ouvriers-experts">
      <?php foreach ($ouvriers_vedettes as $ouvrier): ?>
        <div class="carte-ouvrier-expert">
          <div class="photo-ouvrier-wrapper">
            <img src="<?= BASE ?>/assets/images/<?= e($ouvrier['photo']) ?>" alt="<?= e($ouvrier['nom']) ?>" loading="lazy">
            <span class="badge-verifie-expert">✓ Vérifié</span>
          </div>
          <div class="corps-ouvrier-expert">
            <h4 class="nom-ouvrier"><?= e($ouvrier['nom']) ?></h4>
            <span class="role-ouvrier"><?= e($ouvrier['metier']) ?></span>
            <div class="stat-ouvrier-ligne">
              <span class="note-etoile"><?= e($ouvrier['note']) ?></span>
              <span class="nb-missions"><?= e($ouvrier['missions']) ?></span>
            </div>
            <p class="specialite-texte"><?= e($ouvrier['desc']) ?></p>
            <div class="tags-ouvrier">
              <?php foreach ($ouvrier['tags'] as $tag): ?>
                <span><?= e($tag) ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 8. POURQUOI CHOISIR LOCAOUVRIERS (CONFIANCE & IMAGE) -->
<section class="section-contenu fond-alt">
  <div class="conteneur grille-pourquoi-nous">
    <div class="image-pourquoi-wrapper">
      <img src="<?= BASE ?>/assets/images/app-trust.jpg" alt="Confiance et garantie LocaOuvriers" class="image-confiance" loading="lazy">
      <div class="badge-superpose-confiance">
        <span class="icone-bouclier">🛡️</span>
        <div>
          <strong>100% de Sérénité</strong>
          <small>Garantie remplacement & support dédié</small>
        </div>
      </div>
    </div>

    <div class="texte-pourquoi">
      <span class="badge-sous-titre">Pourquoi LocaOuvriers ?</span>
      <h2 class="titre-section">La garantie d'un chantier sans stress</h2>
      <p class="texte-section">Fini les mauvaises surprises et les négociations pénibles dans la rue :</p>

      <div class="liste-arguments">
        <div class="argument-item">
          <div class="argument-icone">📋</div>
          <div>
            <h4>Profils Contrôlés et Vérifiés</h4>
            <p>CNIE vérifiée, antécédents contrôlés et compétences validées.</p>
          </div>
        </div>

        <div class="argument-item">
          <div class="argument-icone">💰</div>
          <div>
            <h4>Tarif Fixe à la Journée</h4>
            <p>Pas de devis imprévu : le tarif journalier est clair dès le départ.</p>
          </div>
        </div>

        <div class="argument-item">
          <div class="argument-icone">🔄</div>
          <div>
            <h4>Remplacement Gratuit Immédiat</h4>
            <p>En cas d'absence imprévue, un remplaçant qualifié prend le relais.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 9. FAQ ACCORDÉON -->
<section id="faq" class="section-contenu fond-alt">
  <div class="conteneur bloc-faq-etroit">
    <div class="en-tete-section centre">
      <span class="badge-sous-titre">Des Réponses Claires</span>
      <h2 class="titre-section">Foire Aux Questions</h2>
    </div>

    <div class="accordeon-faq">
      <?php foreach ($faqs as $index => $faq): ?>
        <details class="item-faq" <?= $index === 0 ? 'open' : '' ?>>
          <summary class="question-faq">
            <span><?= e($faq['q']) ?></span>
            <span class="faq-chevron">▾</span>
          </summary>
          <div class="reponse-faq"><?= e($faq['r']) ?></div>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 11. APPEL À L'ACTION FINAL (CTA) -->
<section class="section-cta-final">
  <div class="conteneur">
    <div class="boite-cta-fond">
      <div class="cta-overlay"></div>
      <div class="cta-contenu-texte">
        <span class="badge-cta-pill">⚡ COMMENCEZ AUJOURD'HUI</span>
        <h2 class="titre-cta-final">Trouvez votre artisan qualifié en quelques clics</h2>
        <p class="sous-titre-cta">Réservation en 2 minutes, disponibilité garantie sous 24 heures.</p>
        <div class="boutons-cta-groupe">
          <?php if ($role === 'client'): ?>
            <a href="<?= BASE ?>/client/reserver.php" class="btn-cta-principal">Réserver un ouvrier →</a>
          <?php else: ?>
            <a href="<?= BASE ?>/register.php" class="btn-cta-principal">Créer un compte & réserver →</a>
            <a href="<?= BASE ?>/login.php" class="btn-cta-secondaire">Se connecter</a>
          <?php endif; ?>
          <a href="tel:+212522001122" class="btn-cta-secondaire">📞 Nous appeler au 05 22 00 11 22</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
