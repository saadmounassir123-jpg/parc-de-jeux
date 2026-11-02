<?php
/**
 * Tableau de bord Administrateur - LocaOuvriers
 * Vue d'ensemble claire, structurée et intuitive de l'activité
 */
require '../includes/fonctions.php';
exiger_role('admin');
$titre = 'Tableau de bord Administrateur';

// Actions rapides depuis le tableau de bord (Accepter, Refuser, Marquer payé)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id > 0) {
        if ($action === 'accepter') {
            $pdo->prepare("UPDATE reservations SET statut = 'acceptee' WHERE id = ? AND statut = 'en_attente'")->execute([$id]);
            message("Réservation #$id acceptée. Vous pouvez maintenant affecter un ouvrier disponible.");
        } elseif ($action === 'refuser') {
            $pdo->prepare("UPDATE reservations SET statut = 'refusee' WHERE id = ? AND statut = 'en_attente'")->execute([$id]);
            message("Réservation #$id refusée.", 'err');
        } elseif ($action === 'payee') {
            $pdo->prepare("UPDATE reservations SET paye = 1 WHERE id = ?")->execute([$id]);
            message("Paiement enregistré pour la réservation #$id.");
        }
    }
    aller_a('admin/index.php');
}

// 1. Chiffres clés (KPIs)
$total_reservations = (int)valeur('SELECT COUNT(*) FROM reservations');
$en_attente         = (int)valeur("SELECT COUNT(*) FROM reservations WHERE statut = 'en_attente'");
$en_cours           = (int)valeur("SELECT COUNT(*) FROM reservations WHERE statut IN ('acceptee', 'affectee', 'en_cours')");
$terminees          = (int)valeur("SELECT COUNT(*) FROM reservations WHERE statut = 'terminee'");

$en_mission_jour    = (int)valeur('SELECT COUNT(DISTINCT worker_id) FROM assignments WHERE date_mission = CURDATE()');
$ouvriers_dispos    = (int)valeur("SELECT COUNT(*) FROM workers WHERE statut = 'actif'
                                   AND id NOT IN (SELECT worker_id FROM assignments WHERE date_mission = CURDATE())
                                   AND id NOT IN (SELECT worker_id FROM absences WHERE date_absence = CURDATE())");
$total_ouvriers     = (int)valeur("SELECT COUNT(*) FROM workers WHERE statut = 'actif'");
$total_clients      = (int)valeur("SELECT COUNT(*) FROM users WHERE role = 'client'");

$ca_mois            = (float)valeur('SELECT COALESCE(SUM(montant), 0) FROM reservations
                                     WHERE paye = 1 AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())');
$ca_total           = (float)valeur('SELECT COALESCE(SUM(montant), 0) FROM reservations WHERE paye = 1');

// 2. Réservations en attente (Priorité n°1)
$reservations_urgentes = $pdo->query("SELECT r.*, t.nom AS metier, u.nom AS client_nom, u.telephone AS client_tel, u.email AS client_email
                                      FROM reservations r
                                      JOIN trades t ON t.id = r.trade_id
                                      JOIN users u ON u.id = r.client_id
                                      WHERE r.statut = 'en_attente'
                                      ORDER BY r.created_at ASC")->fetchAll();

// 3. Missions planifiées aujourd'hui
$missions_jour = $pdo->query("SELECT a.*, w.id AS wid, u.nom AS ouvrier_nom, u.telephone AS ouvrier_tel,
                                     t.nom AS metier, r.adresse, r.id AS reservation_id, uc.nom AS client_nom
                              FROM assignments a
                              JOIN workers w ON w.id = a.worker_id
                              JOIN users u ON u.id = w.user_id
                              JOIN trades t ON t.id = w.trade_id
                              JOIN reservations r ON r.id = a.reservation_id
                              JOIN users uc ON uc.id = r.client_id
                              WHERE a.date_mission = CURDATE()
                              ORDER BY a.statut DESC, u.nom ASC")->fetchAll();

// 4. Dernières réservations enregistrées (les 8 plus récentes)
$dernieres_reservations = $pdo->query("SELECT r.*, t.nom AS metier, u.nom AS client_nom, u.telephone AS client_tel,
                                             (SELECT COUNT(DISTINCT worker_id) FROM assignments a WHERE a.reservation_id = r.id) AS nb_affectes
                                      FROM reservations r
                                      JOIN trades t ON t.id = r.trade_id
                                      JOIN users u ON u.id = r.client_id
                                      ORDER BY r.created_at DESC LIMIT 8")->fetchAll();

// 5. Répartition des ouvriers par métier
$metiers_equipe = $pdo->query("SELECT t.*,
                                     (SELECT COUNT(*) FROM workers w WHERE w.trade_id = t.id AND w.statut = 'actif') AS nb_ouvriers,
                                     (SELECT COUNT(*) FROM reservations r WHERE r.trade_id = t.id) AS nb_demandes
                              FROM trades t ORDER BY t.nom ASC")->fetchAll();

require '../includes/header.php';
?>

<!-- BANDEAU SUPÉRIEUR ADMIN -->
<div class="carte" style="background: linear-gradient(135deg, var(--marine-sombre), var(--marine)); color:#fff; border:none; padding: 28px 32px; margin-bottom: 24px;">
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
    <div>
      <span style="background:rgba(255,183,3,0.2); color:var(--jaune); font-weight:700; font-size:12px; padding:4px 10px; border-radius:20px; text-transform:uppercase;">
        Espace Administrateur
      </span>
      <h1 style="color:#fff; margin: 8px 0 4px; font-size:28px;">Tableau de bord de gestion</h1>
      <p style="color:#cbd5e1; margin:0; font-size:15px;">
        Aujourd'hui, <strong><?= date('d/m/Y') ?></strong> • Vue d'ensemble des opérations et du personnel
      </p>
    </div>

    <!-- Raccourcis d'actions rapides -->
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
      <a href="<?= BASE ?>/admin/reservations.php" class="btn" style="padding:10px 18px; font-size:14px;">
        📋 Toutes les réservations (<?= $total_reservations ?>)
      </a>
      <a href="<?= BASE ?>/admin/ouvriers.php" class="btn clair" style="padding:10px 18px; font-size:14px;">
        👷 Gérer les ouvriers (<?= $total_ouvriers ?>)
      </a>
    </div>
  </div>
</div>

<!-- GRILLE DES CHIFFRES CLÉS (KPIS) -->
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px; margin-bottom: 28px;">
  
  <!-- KPI 1 : Chiffre d'affaires du mois -->
  <div class="carte" style="margin-bottom:0; border-top: 5px solid var(--orange); padding: 20px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
      <div>
        <span class="discret" style="font-size:13px; font-weight:700; text-transform:uppercase;">Chiffre du mois</span>
        <div style="font-size:26px; font-weight:800; color:var(--marine-sombre); margin: 4px 0;"><?= prix($ca_mois) ?></div>
        <small class="discret">Total encaissé : <?= prix($ca_total) ?></small>
      </div>
      <span style="font-size:32px;">💰</span>
    </div>
  </div>

  <!-- KPI 2 : Réservations en attente (ALERTE) -->
  <div class="carte" style="margin-bottom:0; border-top: 5px solid <?= $en_attente > 0 ? '#ef4444' : '#10b981' ?>; padding: 20px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
      <div>
        <span class="discret" style="font-size:13px; font-weight:700; text-transform:uppercase;">En attente de traitement</span>
        <div style="font-size:28px; font-weight:800; color:<?= $en_attente > 0 ? '#dc2626' : '#10b981' ?>; margin: 4px 0;">
          <?= $en_attente ?>
        </div>
        <small class="discret"><?= $en_attente > 0 ? '⚠️ Action requise' : '✅ Aucune demande en attente' ?></small>
      </div>
      <span style="font-size:32px;"><?= $en_attente > 0 ? '⏳' : '✨' ?></span>
    </div>
  </div>

  <!-- KPI 3 : Ouvriers en mission aujourd'hui -->
  <div class="carte" style="margin-bottom:0; border-top: 5px solid #2563eb; padding: 20px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
      <div>
        <span class="discret" style="font-size:13px; font-weight:700; text-transform:uppercase;">Sur le terrain aujourd'hui</span>
        <div style="font-size:28px; font-weight:800; color:var(--marine-sombre); margin: 4px 0;">
          <?= $en_mission_jour ?> <span style="font-size:16px; font-weight:600; color:var(--gris);">/ <?= $total_ouvriers ?></span>
        </div>
        <small class="discret"><?= $ouvriers_dispos ?> ouvrier(s) disponible(s)</small>
      </div>
      <span style="font-size:32px;">👷</span>
    </div>
  </div>

  <!-- KPI 4 : Total clients -->
  <div class="carte" style="margin-bottom:0; border-top: 5px solid #10b981; padding: 20px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
      <div>
        <span class="discret" style="font-size:13px; font-weight:700; text-transform:uppercase;">Clients enregistrés</span>
        <div style="font-size:28px; font-weight:800; color:var(--marine-sombre); margin: 4px 0;"><?= $total_clients ?></div>
        <small class="discret"><?= $total_reservations ?> réservations passées</small>
      </div>
      <span style="font-size:32px;">👥</span>
    </div>
  </div>

</div>

<!-- SECTION 1 : RÉSERVATIONS EN ATTENTE (ACTION PRIORITAIRE) -->
<?php if ($en_attente > 0): ?>
  <div class="carte" style="border: 2px solid #fecaca; background: #fffbfb; margin-bottom: 28px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px; flex-wrap:wrap; gap:10px;">
      <div>
        <h2 style="color:#991b1b; margin:0; font-size:20px; display:flex; align-items:center; gap:8px;">
          <span>🚨</span> Réservations en attente d'approbation (<?= count($reservations_urgentes) ?>)
        </h2>
        <p class="discret" style="margin:4px 0 0;">Ces clients attendent votre confirmation pour démarrer leur chantier.</p>
      </div>
      <a href="<?= BASE ?>/admin/reservations.php?statut=en_attente" class="btn rouge petit">Gérer toutes les réservations en attente →</a>
    </div>

    <div class="tableau">
      <table>
        <thead>
          <tr>
            <th>Réf.</th>
            <th>Client</th>
            <th>Métier & Quantité</th>
            <th>Dates & Durée</th>
            <th>Lieu</th>
            <th>Montant</th>
            <th style="text-align:right;">Actions rapides</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reservations_urgentes as $r): ?>
            <?php $jours = count(dates_entre($r['date_debut'], $r['date_fin'])); ?>
            <tr>
              <td><strong>#<?= (int)$r['id'] ?></strong></td>
              <td>
                <b><?= e($r['client_nom']) ?></b><br>
                <a href="tel:<?= e($r['client_tel']) ?>" style="font-size:13px; color:var(--bleu-royal);">📞 <?= e($r['client_tel']) ?></a>
              </td>
              <td>
                <span style="font-size:17px;"><?= icone_metier($r['metier']) ?></span> <b><?= e($r['metier']) ?></b><br>
                <span class="discret"><?= (int)$r['nombre_ouvriers'] ?> ouvrier(s) demandé(s)</span>
              </td>
              <td>
                Du <?= date_fr($r['date_debut']) ?><br>au <?= date_fr($r['date_fin']) ?>
                <span class="discret">(<?= $jours ?> j)</span>
              </td>
              <td><span style="font-size:13px;"><?= e($r['adresse']) ?></span></td>
              <td><strong style="color:var(--orange); font-size:16px;"><?= prix($r['montant']) ?></strong></td>
              <td style="text-align:right;">
                <!-- Formulaire d'acceptation directe -->
                <form method="post" class="en-ligne" onsubmit="return confirm('Accepter cette réservation ?');">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <input type="hidden" name="action" value="accepter">
                  <button type="submit" class="btn vert petit" title="Accepter la réservation">✔ Accepter</button>
                </form>
                <!-- Formulaire de refus -->
                <form method="post" class="en-ligne" onsubmit="return confirm('Refuser cette réservation ?');">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <input type="hidden" name="action" value="refuser">
                  <button type="submit" class="btn rouge petit" title="Refuser">✖ Refuser</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

<!-- SECTION EN 2 COLONNES : MISSIONS DU JOUR & DISPONIBILITÉ ÉQUIPE -->
<div style="display:grid; grid-template-columns: 1.3fr 1fr; gap: 24px; margin-bottom: 28px;">

  <!-- COLONNE GAUCHE : MISSIONS DU JOUR -->
  <div class="carte" style="margin-bottom:0;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
      <h2 style="margin:0; font-size:19px;">📅 Missions actives aujourd'hui (<?= count($missions_jour) ?>)</h2>
      <span class="badge actif"><?= $en_mission_jour ?> ouvrier(s) sur site</span>
    </div>

    <?php if (!$missions_jour): ?>
      <div style="text-align:center; padding: 30px; background:var(--fond); border-radius:12px; color:var(--gris);">
        <span style="font-size:36px; display:block; margin-bottom:8px;">🏖️</span>
        <strong>Aucune mission planifiée aujourd'hui.</strong><br>
        <small>Les ouvriers actifs sont actuellement disponibles pour de nouvelles affectations.</small>
      </div>
    <?php else: ?>
      <div class="tableau">
        <table>
          <thead>
            <tr>
              <th>Ouvrier</th>
              <th>Métier</th>
              <th>Client & Adresse</th>
              <th>Statut</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($missions_jour as $m): ?>
              <tr>
                <td>
                  <b><?= e($m['ouvrier_nom']) ?></b><br>
                  <a href="tel:<?= e($m['ouvrier_tel']) ?>" style="font-size:12px; color:var(--gris);">📞 <?= e($m['ouvrier_tel']) ?></a>
                </td>
                <td><?= icone_metier($m['metier']) ?> <?= e($m['metier']) ?></td>
                <td>
                  <strong><?= e($m['client_nom']) ?></strong><br>
                  <span class="discret" style="font-size:12px;">📍 <?= e($m['adresse']) ?></span>
                </td>
                <td><?= badge($m['statut']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- COLONNE DROITE : RÉPARTITION ET DISPONIBILITÉ PAR MÉTIER -->
  <div class="carte" style="margin-bottom:0;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
      <h2 style="margin:0; font-size:19px;">🛠️ Effectifs par métier</h2>
      <a href="<?= BASE ?>/admin/ouvriers.php" style="font-size:13px; font-weight:700;">Gérer l'équipe →</a>
    </div>

    <div style="display:grid; gap: 12px;">
      <?php foreach ($metiers_equipe as $m): ?>
        <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 14px; background:var(--fond); border-radius:12px; border:1px solid var(--gris-bordure);">
          <div style="display:flex; align-items:center; gap:10px;">
            <span style="font-size:24px;"><?= icone_metier($m['nom']) ?></span>
            <div>
              <strong style="color:var(--marine-sombre);"><?= e($m['nom']) ?></strong>
              <div class="discret" style="font-size:12px;"><?= prix($m['tarif_journalier']) ?> / jour</div>
            </div>
          </div>
          <div style="text-align:right;">
            <span class="badge <?= (int)$m['nb_ouvriers'] > 0 ? 'actif' : 'inactif' ?>">
              <?= (int)$m['nb_ouvriers'] ?> ouvrier(s)
            </span>
            <div class="discret" style="font-size:11px; margin-top:2px;"><?= (int)$m['nb_demandes'] ?> réservations au total</div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>

<!-- SECTION 3 : DERNIÈRES RÉSERVATIONS ENREGISTRÉES -->
<div class="carte">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 18px; flex-wrap:wrap; gap:12px;">
    <div>
      <h2 style="margin:0; font-size:20px;">📋 Dernières réservations enregistrées</h2>
      <p class="discret" style="margin:4px 0 0;">Historique récent de l'activité sur le site.</p>
    </div>
    <a href="<?= BASE ?>/admin/reservations.php" class="btn" style="padding:9px 16px; font-size:13px;">Voir les <?= $total_reservations ?> réservations →</a>
  </div>

  <div class="tableau">
    <table>
      <thead>
        <tr>
          <th>Réf.</th>
          <th>Date de demande</th>
          <th>Client</th>
          <th>Métier</th>
          <th>Période</th>
          <th>Montant</th>
          <th>Statut</th>
          <th>Paiement</th>
          <th style="text-align:right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($dernieres_reservations as $r): ?>
          <tr>
            <td><strong>#<?= (int)$r['id'] ?></strong></td>
            <td><span class="discret" style="font-size:13px;"><?= date_fr($r['created_at']) ?></span></td>
            <td>
              <b><?= e($r['client_nom']) ?></b><br>
              <span class="discret" style="font-size:12px;">📞 <?= e($r['client_tel']) ?></span>
            </td>
            <td><?= icone_metier($r['metier']) ?> <?= e($r['metier']) ?> <small>(×<?= (int)$r['nombre_ouvriers'] ?>)</small></td>
            <td>
              Du <?= date_fr($r['date_debut']) ?><br>au <?= date_fr($r['date_fin']) ?>
            </td>
            <td><strong style="color:var(--orange);"><?= prix($r['montant']) ?></strong></td>
            <td><?= badge($r['statut']) ?></td>
            <td>
              <?php if ($r['paye']): ?>
                <span class="badge paye">Payé</span>
              <?php else: ?>
                <span class="badge a_payer">À payer</span>
              <?php endif; ?>
            </td>
            <td style="text-align:right;">
              <a href="<?= BASE ?>/admin/reservations.php?statut=<?= e($r['statut']) ?>" class="btn petit clair" style="color:var(--marine); border-color:var(--gris-bordure);">
                Détails →
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require '../includes/footer.php'; ?>
