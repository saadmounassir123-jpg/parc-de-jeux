<?php if (($modeAffichage ?? 'public') === 'admin'): ?>
    </main>
    <footer class="pied pied-admin">🎪 FunPark — Espace interne • Projet étudiant PHP / MySQL</footer>
</div>
<?php elseif (($modeAffichage ?? 'public') === 'auth'): ?>
<footer class="pied pied-auth"><a href="<?= e(url('accueil')) ?>">← Retour à l'accueil</a></footer>
<?php else: ?>
</main>
<footer class="pied pied-public">
    <div class="pied-grille">
        <div>
            <strong>🎪 FunPark</strong>
            <p>Parc d'attractions fictif — projet étudiant de gestion.</p>
        </div>
        <div>
            <strong>📍 Nous trouver</strong>
            <p>Avenue des Manèges, 20000 Casablanca<br>Ouvert tous les jours, 10h — 22h</p>
        </div>
        <div>
            <strong>📞 Contact</strong>
            <p>05 22 00 00 00<br>contact@funpark.test</p>
        </div>
        <div>
            <strong>🎟️ Billetterie</strong>
            <p>Réservation gratuite en ligne.<br>Paiement et billets au guichet du parc.</p>
        </div>
    </div>
    <p class="pied-copie">© <?= date('Y') ?> FunPark — Données fictives à but pédagogique</p>
</footer>
<?php endif; ?>
<script src="public/js/script.js"></script>
</body>
</html>
