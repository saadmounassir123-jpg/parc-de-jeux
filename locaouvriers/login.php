<?php
require 'includes/fonctions.php';
$titre = 'Connexion';

if (isset($_SESSION['user'])) {
    aller_a(accueil_role($_SESSION['user']['role']));
}

$email = '';
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['password'] ?? '';

    // Validation du format Email
    $v_email = valider_email($email);
    if ($v_email !== true) {
        $erreurs['email'] = $v_email;
    }

    // Validation Mot de passe non vide
    if ($mdp === '') {
        $erreurs['password'] = 'Veuillez saisir votre mot de passe.';
    }

    if (empty($erreurs)) {
        $st = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $st->execute([$email]);
        $u = $st->fetch();

        if (!$u || !password_verify($mdp, $u['mot_de_passe'])) {
            message('Email ou mot de passe incorrect. Veuillez vérifier vos identifiants.', 'err');
            $erreurs['email'] = 'Identifiants invalides.';
            $erreurs['password'] = 'Identifiants invalides.';
        } elseif ($u['statut'] !== 'actif') {
            message("Votre compte est suspendu. Veuillez contacter l'administrateur.", 'err');
        } else {
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => (int)$u['id'], 'nom' => $u['nom'], 'role' => $u['role']];
            aller_a(accueil_role($u['role']));
        }
    } else {
        message('Veuillez remplir correctement les champs de connexion.', 'err');
    }
}

require 'includes/header.php';
?>

<div class="disposition-auth">
  <!-- Colonne Formulaire de connexion -->
  <div class="carte" style="border-top: 5px solid var(--orange);">
    <div style="margin-bottom: 24px;">
      <span style="font-size: 34px;">🔐</span>
      <h1 style="margin-top: 8px; font-size: 28px; color: var(--marine-sombre);">Connexion à votre espace</h1>
      <p class="discret" style="margin: 4px 0 0;">Accédez en temps réel à vos réservations, plannings et devis.</p>
    </div>

    <form method="post" novalidate id="form-login">
      <!-- Champ Email -->
      <div class="champ <?= isset($erreurs['email']) ? 'a-erreur' : '' ?>">
        <label for="email">Adresse Email <span class="requis" title="Champ obligatoire">*</span></label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" 
               placeholder="ex: client@site.com" required maxlength="150" autofocus>
        <small class="aide-champ">Votre email enregistré lors de votre inscription.</small>
        <?php if (isset($erreurs['email'])): ?>
          <div class="message-erreur-champ">⚠️ <?= e($erreurs['email']) ?></div>
        <?php endif; ?>
      </div>

      <!-- Champ Mot de passe -->
      <div class="champ <?= isset($erreurs['password']) ? 'a-erreur' : '' ?>">
        <label for="password">Mot de passe <span class="requis" title="Champ obligatoire">*</span></label>
        <input type="password" id="password" name="password" placeholder="Votre mot de passe" required>
        <small class="aide-champ">Mot de passe sécurisé associé à votre compte.</small>
        <?php if (isset($erreurs['password'])): ?>
          <div class="message-erreur-champ">⚠️ <?= e($erreurs['password']) ?></div>
        <?php endif; ?>
      </div>

      <button class="btn" type="submit" style="width:100%; font-size:16px; padding: 15px; margin-top: 10px;">
        Se connecter à mon compte →
      </button>
    </form>

    <div style="margin-top: 26px; padding-top: 20px; border-top: 1px solid var(--gris-bordure); text-align:center; font-size: 14.5px;">
      <p class="discret">
        Vous n'avez pas encore de compte client ? <br>
        <a href="<?= BASE ?>/register.php" style="color: var(--orange-vif); font-weight: 700; font-size: 15px;">
          Créer un compte gratuitement en 2 minutes →
        </a>
      </p>
      <div style="margin-top: 14px; display:inline-flex; align-items:center; gap:6px; font-size:12.5px; color:var(--gris); background:var(--fond); padding:6px 14px; border-radius:20px; border:1px solid var(--gris-bordure);">
        <span>🔒</span> Données protégées & transmission chiffrée SSL
      </div>
    </div>
  </div>

  <!-- Colonne Réassurance & Comptes Démo -->
  <div class="carte-reassurance-auth">
    <div>
      <span style="background: rgba(255,183,3,0.2); color: var(--jaune); font-weight: 800; font-size: 12px; padding: 4px 12px; border-radius: 20px; text-transform: uppercase;">
        Plateforme Certifiée
      </span>
      <h2 style="color: #fff; margin: 16px 0 10px; font-size: 24px;">Pourquoi choisir LocaOuvriers ?</h2>
      <p style="color: #cbd5e1; font-size: 15px; line-height: 1.6;">
        La plateforme marocaine de référence pour louer des artisans et ouvriers qualifiés à la journée en toute sérénité.
      </p>

      <div style="margin-top: 24px; display: flex; flex-direction: column; gap: 14px;">
        <div style="display: flex; gap: 12px; align-items: flex-start;">
          <span style="background: rgba(16,185,129,0.2); color: #10b981; font-size: 16px; font-weight: 800; border-radius: 50%; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">✓</span>
          <div>
            <strong style="color: #fff; font-size: 14.5px;">Artisans 100% vérifiés et notés</strong>
            <p style="color: #94a3b8; font-size: 13px; margin: 2px 0 0;">CNIE, casier judiciaire et références contrôlés systématiquement.</p>
          </div>
        </div>

        <div style="display: flex; gap: 12px; align-items: flex-start;">
          <span style="background: rgba(16,185,129,0.2); color: #10b981; font-size: 16px; font-weight: 800; border-radius: 50%; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">✓</span>
          <div>
            <strong style="color: #fff; font-size: 14.5px;">Tarifs transparents à la journée</strong>
            <p style="color: #94a3b8; font-size: 13px; margin: 2px 0 0;">Prix fixes et connus à l'avance dès 300 DH/jour, sans surprise.</p>
          </div>
        </div>

        <div style="display: flex; gap: 12px; align-items: flex-start;">
          <span style="background: rgba(16,185,129,0.2); color: #10b981; font-size: 16px; font-weight: 800; border-radius: 50%; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">✓</span>
          <div>
            <strong style="color: #fff; font-size: 14.5px;">Remplacement garanti sous 24h</strong>
            <p style="color: #94a3b8; font-size: 13px; margin: 2px 0 0;">En cas d'imprévu, un ouvrier de niveau équivalent prend le relais sans surcoût.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Boîte de Test Rapide / Comptes Démo -->
    <div class="comptes-demo-rapides">
      <div style="font-weight: 700; color: var(--jaune); font-size: 13px; text-transform: uppercase; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
        <span>⚡</span> Remplissage rapide Démo (1 Clic)
      </div>
      <p style="color: #cbd5e1; font-size: 12.5px; margin-bottom: 12px;">
        Cliquez sur un profil pour tester instantanément les sessions Administrateur, Client ou Ouvrier :
      </p>
      <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        <button type="button" class="btn-demo-fill" onclick="remplirDemo('admin@site.com', 'admin123')">
          👑 Admin
        </button>
        <button type="button" class="btn-demo-fill" onclick="remplirDemo('client@site.com', 'admin123')">
          👤 Client
        </button>
        <button type="button" class="btn-demo-fill" onclick="remplirDemo('ouvrier@site.com', 'admin123')">
          👷 Ouvrier
        </button>
      </div>
    </div>
  </div>
</div>

<script>
function remplirDemo(email, mdp) {
  var champEmail = document.getElementById('email');
  var champMdp   = document.getElementById('password');
  if (champEmail && champMdp) {
    champEmail.value = email;
    champMdp.value   = mdp;
    champEmail.dispatchEvent(new Event('input', { bubbles: true }));
    champMdp.dispatchEvent(new Event('input', { bubbles: true }));
    champEmail.focus();
  }
}
</script>

<?php require 'includes/footer.php'; ?>
