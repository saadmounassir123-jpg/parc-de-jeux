</main>

<!-- Bandeau de Réassurance Confiance -->
<section class="section-reassurance">
  <div class="conteneur grille-reassurance">
    <div class="item-reassurance">
      <div class="icone-reassurance">🛡️</div>
      <div class="texte-reassurance">
        <h4>Artisans 100% Vérifiés</h4>
        <p>Identité, expérience et qualifications rigoureusement contrôlées.</p>
      </div>
    </div>
    <div class="item-reassurance">
      <div class="icone-reassurance">💎</div>
      <div class="texte-reassurance">
        <h4>Tarifs Clairs & Fixes</h4>
        <p>Prix à la journée sans frais cachés ni mauvaise surprise.</p>
      </div>
    </div>
    <div class="item-reassurance">
      <div class="icone-reassurance">⚡</div>
      <div class="texte-reassurance">
        <h4>Intervention Sous 24h</h4>
        <p>Affectation rapide et remplacement immédiat en cas d'imprévu.</p>
      </div>
    </div>
    <div class="item-reassurance">
      <div class="icone-reassurance">🤝</div>
      <div class="texte-reassurance">
        <h4>Satisfaction Garantie</h4>
        <p>Suivi dédié de chaque mission par notre équipe support 7j/7.</p>
      </div>
    </div>
  </div>
</section>

<!-- Grand Pied de page moderne -->
<footer class="grand-pied">
  <div class="conteneur">
    <div class="grille-pied">
      <!-- Colonne 1 : À propos -->
      <div class="col-pied">
        <a href="<?= BASE ?>/index.php" class="logo-pied-lien">
          <img class="logo-pied-grand" src="<?= BASE ?>/logo.php?full=1&texte=%23ffffff" alt="<?= SITE ?>">
        </a>
        <p class="desc-pied">
          LocaOuvriers est la plateforme de référence pour réserver des ouvriers et artisans qualifiés à la journée partout au Maroc. Simplicité, rapidité et confiance absolue.
        </p>
        <div class="badges-securite">
          <span class="badge-pill">🔒 Service Sécurisé</span>
          <span class="badge-pill">⭐ Noté 4.9/5</span>
        </div>
      </div>

      <!-- Colonne 2 : Métiers disponibles -->
      <div class="col-pied">
        <h4 class="titre-col-pied">Nos Métiers Populaires</h4>
        <ul class="liens-pied">
          <li><a href="<?= BASE ?>/index.php#metiers">🌿 Jardiniers & Paysagistes</a></li>
          <li><a href="<?= BASE ?>/index.php#metiers">🔧 Plombiers Sanitaires</a></li>
          <li><a href="<?= BASE ?>/index.php#metiers">⚡ Électriciens Bâtiment</a></li>
          <li><a href="<?= BASE ?>/index.php#metiers">🎨 Peintres & Décorateurs</a></li>
          <li><a href="<?= BASE ?>/index.php#metiers">🛡️ Agents de Sécurité & Gardiens</a></li>
          <li><a href="<?= BASE ?>/index.php#metiers">🔨 Maçons & Rénovateurs</a></li>
        </ul>
      </div>

      <!-- Colonne 3 : Navigation utile -->
      <div class="col-pied">
        <h4 class="titre-col-pied">Accès Rapide</h4>
        <ul class="liens-pied">
          <li><a href="<?= BASE ?>/index.php#comment-marche">Comment ça marche ?</a></li>
          <li><a href="<?= BASE ?>/index.php#simulateur">Calculateur de tarif en direct</a></li>
          <li><a href="<?= BASE ?>/index.php#realisations">Nos réalisations en photos</a></li>
          <li><a href="<?= BASE ?>/index.php#faq">Foire aux questions (FAQ)</a></li>
          <li><a href="<?= BASE ?>/login.php">Espace Client</a></li>
          <li><a href="<?= BASE ?>/register.php">Créer un compte particulier</a></li>
        </ul>
      </div>

      <!-- Colonne 4 : Contact & Urgence -->
      <div class="col-pied">
        <h4 class="titre-col-pied">Besoin d'aide ?</h4>
        <p class="contact-ligne">
          <span class="icone-ct">📞</span>
          <span><strong>Service Client :</strong><br><a href="tel:+212522001122">+212 5 22 00 11 22</a></span>
        </p>
        <p class="contact-ligne">
          <span class="icone-ct">✉️</span>
          <span><strong>Email Support :</strong><br><a href="mailto:contact@locaouvriers.ma">contact@locaouvriers.ma</a></span>
        </p>
        <p class="contact-ligne">
          <span class="icone-ct">📍</span>
          <span>Casablanca, Rabat, Marrakech, Tanger et tout le Maroc</span>
        </p>
        <div class="horaires-box">
          🕒 <strong>Horaires :</strong> Lun - Dim : 8h00 - 20h00
        </div>
      </div>
    </div>

    <!-- Barre inférieure de droits -->
    <div class="bas-de-pied">
      <div class="copyright">
        © <?= date('Y') ?> <strong><?= SITE ?></strong> – Tous droits réservés. Plateforme de location d'ouvriers à la journée.
      </div>
      <div class="liens-legaux">
        <a href="#confidentialite">Confidentialité</a>
        <span>•</span>
        <a href="#conditions">Conditions Générales</a>
        <span>•</span>
        <a href="#mentions">Mentions Légales</a>
      </div>
    </div>
  </div>
</footer>

<!-- Bouton Remonter en haut -->
<button id="btnRemonter" class="bouton-remonter" aria-label="Remonter en haut">↑</button>

</body>
</html>
