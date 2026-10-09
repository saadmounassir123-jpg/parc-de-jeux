<?php
// Gestion des réservations
class ReservationController {
    private $pdo, $reservation;
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->reservation = new Reservation($pdo);
    }

    public function actionIndex() {
        exigerRole(['admin', 'agent', 'responsable', 'client']);
        $role = roleActuel();
        $filtres = ['date_debut' => $_GET['date_debut'] ?? '', 'date_fin' => $_GET['date_fin'] ?? '', 'statut' => $_GET['statut'] ?? '',
                    'id_client' => $_GET['id_client'] ?? '', 'id_jeu' => $_GET['id_jeu'] ?? '', 'id_responsable' => $_GET['id_responsable'] ?? ''];
        $idClient = ($role === 'client') ? (idClientSession() ?: -1) : null;
        $idResp = ($role === 'responsable') ? (idResponsableSession() ?: -1) : null;
        vue('reservations/index', [
            'reservations' => $this->reservation->tous($filtres, $idClient, $idResp), 'filtres' => $filtres,
            'clients' => in_array($role, ['admin', 'agent']) ? (new Client($this->pdo))->tous() : [],
            'jeux' => (new Jeu($this->pdo))->actifs(), 'responsables' => (new Responsable($this->pdo))->tous(),
        ]);
    }

    public function actionCreer() {
        $role = roleActuel();
        // Un responsable ne crée pas de réservation (planning seulement)
        if (estConnecte() && !in_array($role, ['admin', 'agent', 'client'], true)) {
            flash('erreur', "Accès refusé : vous n'avez pas le droit d'accéder à cette page.");
            redirect(url('jeux'));
        }

        $erreurs = [];
        $form = ['id_client' => '', 'date_visite' => date('Y-m-d', strtotime('+1 day')), 'nombre_personnes' => 1, 'jeux' => []];

        // Reprendre le formulaire après un échec de finalisation (session)
        if (!estPost() && !empty($_SESSION['reservation_en_attente'])) {
            $s = $_SESSION['reservation_en_attente'];
            $form['date_visite'] = $s['date_visite'] ?? $form['date_visite'];
            $form['nombre_personnes'] = $s['nombre_personnes'] ?? 1;
            $form['jeux'] = $s['jeux'] ?? [];
        }

        if (estPost()) {
            $form['id_client'] = ($role === 'client') ? (string)idClientSession() : post('id_client');
            $form['date_visite'] = post('date_visite');
            $form['nombre_personnes'] = post('nombre_personnes');
            $form['jeux'] = (isset($_POST['jeux']) && is_array($_POST['jeux'])) ? array_map('intval', $_POST['jeux']) : [];

            $nb = ctype_digit((string)$form['nombre_personnes']) ? (int)$form['nombre_personnes'] : 0;
            if (!dateValide($form['date_visite'])) $erreurs[] = "Date de visite invalide.";
            if ($nb < 1) $erreurs[] = "Le nombre de personnes doit être au moins 1.";

            if (in_array($role, ['admin', 'agent'], true)) {
                if (!(new Client($this->pdo))->trouver((int)$form['id_client'])) $erreurs[] = "Choisissez un client.";
            }

            if (!$erreurs) {
                if ($role === '') {
                    // Visiteur : on vérifie sans créer, puis on stocke en session
                    $ctrl = $this->reservation->verifier(null, $form['date_visite'], $nb, $form['jeux'], false);
                    if ($ctrl['erreurs']) {
                        $erreurs = $ctrl['erreurs'];
                    } else {
                        $_SESSION['reservation_en_attente'] = [
                            'date_visite' => $form['date_visite'],
                            'nombre_personnes' => $nb,
                            'jeux' => $form['jeux'],
                        ];
                        flash('erreur', 'Connectez-vous ou créez un compte pour finaliser votre réservation.');
                        redirect(url('auth', 'login'));
                    }
                } else {
                    $idClient = ($role === 'client') ? (int)idClientSession() : (int)$form['id_client'];
                    $statut = ($role === 'client') ? 'en_attente' : 'confirmee';
                    $res = $this->reservation->creer($idClient, $form['date_visite'], $nb, $form['jeux'], $statut);
                    if (isset($res['erreurs'])) {
                        $erreurs = $res['erreurs'];
                    } else {
                        unset($_SESSION['reservation_en_attente']);
                        if ($role === 'client') {
                            flash('succes', 'Réservation enregistrée. Présentez-vous à la billetterie du parc.');
                            redirect(url('reservations', 'confirmation', ['id' => $res['id']]));
                        }
                        flash('succes', "Réservation créée.");
                        redirect(url('reservations'));
                    }
                }
            }

            // En cas d'erreur, on conserve les données pour pré-remplir
            $_SESSION['reservation_en_attente'] = [
                'date_visite' => $form['date_visite'],
                'nombre_personnes' => $nb,
                'jeux' => $form['jeux'],
            ];
        }

        vue('reservations/form', [
            'form' => $form, 'erreurs' => $erreurs,
            'jeux' => (new Jeu($this->pdo))->reservables(),
            'clients' => in_array($role, ['admin', 'agent'], true) ? (new Client($this->pdo))->tous() : [],
        ]);
    }

    public function actionConfirmation() {
        exigerRole(['admin', 'agent', 'client']);
        $id = (int)($_GET['id'] ?? 0);
        $resa = $this->reservation->trouverComplet($id);
        if (!$resa) {
            flash('erreur', 'Réservation introuvable.');
            redirect(roleActuel() === 'client' ? url('reservations') : url('reservations'));
        }
        if (roleActuel() === 'client' && (int)$resa['id_client'] !== (int)idClientSession()) {
            flash('erreur', "Accès refusé : vous n'avez pas le droit d'accéder à cette page.");
            redirect(url('reservations'));
        }
        vue('reservations/confirmation', ['resa' => $resa]);
    }

    public function actionImprimer() {
        exigerRole(['admin', 'agent', 'client']);
        $id = (int)($_GET['id'] ?? 0);
        $resa = $this->reservation->trouverComplet($id);
        if (!$resa) {
            die('Réservation introuvable.');
        }
        if (roleActuel() === 'client' && (int)$resa['id_client'] !== (int)idClientSession()) {
            die('Accès refusé.');
        }
        require RACINE . '/views/reservations/imprimer.php';
    }

    // Changer le statut : confirmer, payer (billetterie), utiliser, annuler
    public function actionStatut() {
        exigerRole(['admin', 'agent', 'client']);
        $id = (int)post('id');
        $nouveau = post('statut');
        $role = roleActuel();
        $resa = $this->reservation->trouver($id);

        if (!estPost() || !$resa) { flash('erreur', "Réservation introuvable."); redirect(url('reservations')); }

        // Le client ne peut qu'annuler ses propres réservations (avant la date de visite)
        $autorise = in_array($role, ['admin', 'agent'], true) ||
                    ($role === 'client' && (int)$resa['id_client'] === (int)idClientSession() && $nouveau === 'annulee');
        if (!$autorise) { flash('erreur', "Vous n'avez pas le droit d'effectuer cette action."); redirect(url('reservations')); }

        $message = $this->reservation->changerStatut($id, $nouveau, post('mode_paiement'));
        if ($message === '') flash('succes', "Statut mis à jour : " . libelle($nouveau) . ".");
        else flash('erreur', $message);
        redirect(url('reservations'));
    }
}
