<?php
require 'includes/fonctions.php';
$titre = 'Inscription';

if (isset($_SESSION['user'])) {
    aller_a(accueil_role($_SESSION['user']['role']));
}

$nom = $email = $tel = '';
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom   = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tel   = trim($_POST['telephone'] ?? '');
    $mdp   = $_POST['password'] ?? '';
    $mdp2  = $_POST['password2'] ?? '';

    // 1. Validation du Nom
    $v_nom = valider_nom($nom);
    if ($v_nom !== true) {
        $erreurs['nom'] = $v_nom;
    }

    // 2. Validation de l'Email
    $v_email = valider_email($email);
    if ($v_email !== true) {
        $erreurs['email'] = $v_email;
    } elseif ((int)valeur('SELECT COUNT(*) FROM users WHERE email = ?', [$email]) > 0) {
        $erreurs['email'] = 'Cette adresse email est déjà enregistrée. Veuillez vous connecter.';
    }

    // 3. Validation du Téléphone (exactement 10 chiffres)
    $v_tel = valider_telephone($tel);
    if ($v_tel !== true) {
        $erreurs['telephone'] = $v_tel;
    }

    // 4. Validation du Mot de passe (min 8 caractères, au moins 1 lettre et 1 chiffre)
    $v_mdp = valider_mot_de_passe($mdp, $mdp2);
    if ($v_mdp !== true) {
        if (strlen($mdp) < 8 || !preg_match('/[a-zA-Z]/', $mdp) || !preg_match('/[0-9]/', $mdp)) {
            $erreurs['password'] = 'Le mot de passe doit comporter au moins 8 caractères avec au moins une lettre et un chiffre.';
        }
        if ($mdp !== $mdp2) {
            $erreurs['password2'] = 'La confirmation ne correspond pas au mot de passe saisi.';
        }
    }

    // Si aucune erreur, on enregistre l'utilisateur
    if (empty($erreurs)) {
        $st = $pdo->prepare("INSERT INTO users (nom, email, mot_de_passe, telephone, role) VALUES (?, ?, ?, ?, 'client')");
        $st->execute([$nom, $email, password_hash($mdp, PASSWORD_DEFAULT), $tel]);
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$pdo->lastInsertId(), 'nom' => $nom, 'role' => 'client'];
        message('Bienvenue ! Votre compte client a été créé avec succès.');
        aller_a('client/reserver.php');
    } else {
        message('Veuillez corriger les erreurs indiquées ci-dessous dans le formulaire.', 'err');
    }
}

require 'includes/header.php';
?>

<div class="disposition-auth">
  <!-- Colonne Formulaire Inscription -->
  <div class="carte" style="border-top: 5px solid var(--jaune);">
    <div style="margin-bottom: 24px;">
      <span style="font-size: 34px;">✨</span>
      <h1 style="margin-top: 8px; font-size: 28px; color: var(--marine-sombre);">Créer un compte Client</h1>
      <p class="discret" style="margin: 4px 0 0;">Rejoignez LocaOuvriers et réservez des artisans certifiés en 2 minutes.</p>
    </div>

    <form method="post" novalidate id="form-register">
      <!-- Champ Nom complet -->
      <div class="champ <?= isset($erreurs['nom']) ? 'a-erreur' : '' ?>">
        <label for="nom">Nom complet <span class="requis" title="Champ obligatoire">*</span></label>
        <input type="text" id="nom" name="nom" value="<?= e($nom) ?>" 
               placeholder="ex: Mohammed Alami" required minlength="2" maxlength="100" 
               pattern="^[a-zA-ZÀ-ÿ\s\-']{2,100}$" autofocus>
        <small class="aide-champ">Lettres, espaces et tirets uniquement (au moins 2 caractères).</small>
        <?php if (isset($erreurs['nom'])): ?>
          <div class="message-erreur-champ">⚠️ <?= e($erreurs['nom']) ?></div>
        <?php endif; ?>
      </div>

      <!-- Champ Email -->
      <div class="champ <?= isset($erreurs['email']) ? 'a-erreur' : '' ?>">
        <label for="email">Adresse Email <span class="requis" title="Champ obligatoire">*</span></label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" 
               placeholder="ex: mohammed@email.com" required maxlength="150">
        <small class="aide-champ">Format valide requis (ex: nom@domaine.com).</small>
        <?php if (isset($erreurs['email'])): ?>
          <div class="message-erreur-champ">⚠️ <?= e($erreurs['email']) ?></div>
        <?php endif; ?>
      </div>

      <!-- Champ Téléphone -->
      <div class="champ <?= isset($erreurs['telephone']) ? 'a-erreur' : '' ?>">
        <label for="telephone">Numéro de Téléphone <span class="requis" title="Champ obligatoire">*</span></label>
        <input type="tel" id="telephone" name="telephone" value="<?= e($tel) ?>" 
               placeholder="ex: 0612345678" required pattern="[0-9]{10}" maxlength="10" minlength="10" inputmode="numeric">
        <small class="aide-champ">Exactement 10 chiffres (chiffres uniquement, sans espaces ni tirets).</small>
        <?php if (isset($erreurs['telephone'])): ?>
          <div class="message-erreur-champ">⚠️ <?= e($erreurs['telephone']) ?></div>
        <?php endif; ?>
      </div>

      <!-- Champs Mot de passe et confirmation -->
      <div class="deux">
        <div class="champ <?= isset($erreurs['password']) ? 'a-erreur' : '' ?>">
          <label for="password">Mot de passe <span class="requis" title="Champ obligatoire">*</span></label>
          <input type="password" id="password" name="password" placeholder="Min. 8 caractères" 
                 required minlength="8" pattern="(?=.*[A-Za-z])(?=.*\d).{8,}">
          <small class="aide-champ">8 car. min. avec au moins 1 lettre et 1 chiffre.</small>
          <?php if (isset($erreurs['password'])): ?>
            <div class="message-erreur-champ">⚠️ <?= e($erreurs['password']) ?></div>
          <?php endif; ?>
        </div>

        <div class="champ <?= isset($erreurs['password2']) ? 'a-erreur' : '' ?>">
          <label for="password2">Confirmation <span class="requis" title="Champ obligatoire">*</span></label>
          <input type="password" id="password2" name="password2" placeholder="Retapez votre mot de passe" 
                 required minlength="8">
          <small class="aide-champ">Doit être strictement identique au mot de passe.</small>
          <?php if (isset($erreurs['password2'])): ?>
            <div class="message-erreur-champ">⚠️ <?= e($erreurs['password2']) ?></div>
          <?php endif; ?>
        </div>
      </div>

      <button class="btn" type="submit" style="width:100%; font-size:16px; padding: 15px; margin-top: 10px;">
        Créer mon compte & Réserver →
      </button>
    </form>

    <div style="margin-top: 26px; padding-top: 20px; border-top: 1px solid var(--gris-bordure); text-align:center; font-size: 14.5px;">
      <p class="discret">
        Vous avez déjà un compte ? <br>
        <a href="<?= BASE ?>/login.php" style="color: var(--orange-vif); font-weight: 700; font-size: 15px;">
          Connectez-vous directement ici →
        </a>
      </p>
      <div style="margin-top: 14px; display:inline-flex; align-items:center; gap:6px; font-size:12.5px; color:var(--gris); background:var(--fond); padding:6px 14px; border-radius:20px; border:1px solid var(--gris-bordure);">
        <span>🛡️</span> Vos données sont 100% sécurisées et confidentielles
      </div>
    </div>
  </div>

  <!-- Colonne Réassurance Inscription -->
  <div class="carte-reassurance-auth">
    <div>
      <span style="background: rgba(255,183,3,0.2); color: var(--jaune); font-weight: 800; font-size: 12px; padding: 4px 12px; border-radius: 20px; text-transform: uppercase;">
        Inscription Rapide & Gratuite
      </span>
      <h2 style="color: #fff; margin: 16px 0 10px; font-size: 24px;">Vos privilèges membres</h2>
      <p style="color: #cbd5e1; font-size: 15px; line-height: 1.6;">
        En rejoignant LocaOuvriers, vous simplifiez tous vos travaux et interventions à domicile ou en entreprise.
      </p>

      <div style="margin-top: 26px; display: flex; flex-direction: column; gap: 18px;">
        <div style="display: flex; gap: 14px; align-items: flex-start;">
          <span style="background: var(--jaune); color: var(--marine-sombre); font-size: 14px; font-weight: 900; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">1</span>
          <div>
            <strong style="color: #fff; font-size: 15px;">Réservation en 3 clics</strong>
            <p style="color: #94a3b8; font-size: 13px; margin: 3px 0 0;">Sélectionnez votre métier, vos dates et obtenez un devis immédiat.</p>
          </div>
        </div>

        <div style="display: flex; gap: 14px; align-items: flex-start;">
          <span style="background: var(--jaune); color: var(--marine-sombre); font-size: 14px; font-weight: 900; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">2</span>
          <div>
            <strong style="color: #fff; font-size: 15px;">Artisans certifiés & évalués</strong>
            <p style="color: #94a3b8; font-size: 13px; margin: 3px 0 0;">Accédez aux meilleurs profils avec les notes et avis vérifiés.</p>
          </div>
        </div>

        <div style="display: flex; gap: 14px; align-items: flex-start;">
          <span style="background: var(--jaune); color: var(--marine-sombre); font-size: 14px; font-weight: 900; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">3</span>
          <div>
            <strong style="color: #fff; font-size: 15px;">Historique & Suivi en direct</strong>
            <p style="color: #94a3b8; font-size: 13px; margin: 3px 0 0;">Consultez vos factures, plannings et statut d'intervention 24h/24.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Témoignage / Note -->
    <div style="background: rgba(255,255,255,0.08); border-radius: 14px; padding: 18px; margin-top: 24px; border: 1px solid rgba(255,255,255,0.12);">
      <div style="color: #f59e0b; font-size: 16px; margin-bottom: 6px;">★★★★★</div>
      <p style="color: #e2e8f0; font-size: 13.5px; font-style: italic; margin-bottom: 8px;">
        « Service irréprochable ! J'ai trouvé un peintre et un plombier pour mon appartement en moins de 24h. Travail très soigné. »
      </p>
      <small style="color: var(--jaune); font-weight: 700;">— Mehdi B., Casablanca</small>
    </div>
  </div>
</div>

<?php require 'includes/footer.php'; ?>
