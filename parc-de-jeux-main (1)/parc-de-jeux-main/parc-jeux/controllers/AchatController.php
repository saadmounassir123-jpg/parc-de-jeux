<?php
// Contrôleur de l'achat de billets en ligne et de la billetterie
class AchatController {
    private $pdo, $achat, $billet;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->achat = new Achat($pdo);
        $this->billet = new Billet($pdo);
    }

    public function actionIndex() {
        exigerRole(['admin', 'responsable', 'agent', 'client']);
        $role = roleActuel();
        $idClient = ($role === 'client') ? (idClientSession() ?: -1) : null;

        $filtres = [
            'date_debut' => $_GET['date_debut'] ?? '',
            'date_fin' => $_GET['date_fin'] ?? '',
            'statut' => $_GET['statut'] ?? '',
            'id_client' => $_GET['id_client'] ?? '',
        ];

        $achats = $this->achat->tous($filtres, $idClient);
        $clients = estPersonnel() ? (new Client($this->pdo))->tous() : [];

        vue('achats/index', [
            'achats' => $achats,
            'filtres' => $filtres,
            'clients' => $clients,
            'titrePage' => ($role === 'client') ? 'Mes billets & achats' : 'Ventes de billets',
        ]);
    }

    // Page publique / client de commande de billets
    public function actionBilletterie() {
        $types = $this->billet->types();
        $promotionsActives = (new Promotion($this->pdo))->actifs();

        vue('achats/billetterie', [
            'types' => $types,
            'promotions' => $promotionsActives,
            'titrePage' => 'Billetterie en ligne — Acheter vos billets',
        ]);
    }

    // Traitement de la commande
    public function actionCreer() {
        $role = roleActuel();
        $dateVisite = post('date_visite');
        $codePromo = trim(post('code_promo'));
        $quantites = $_POST['quantites'] ?? [];

        $lignes = [];
        if (is_array($quantites)) {
            foreach ($quantites as $idType => $qte) {
                $qteInt = (int)$qte;
                if ($qteInt > 0) {
                    $lignes[] = [
                        'id_type_billet' => (int)$idType,
                        'quantite' => $qteInt,
                    ];
                }
            }
        }

        if (empty($lignes)) {
            flash('erreur', 'Veuillez sélectionner au moins un billet (quantité supérieure à 0).');
            redirect(url('achats', 'billetterie'));
        }

        if (!dateValide($dateVisite) || $dateVisite < date('Y-m-d')) {
            flash('erreur', 'Veuillez choisir une date de visite valide (à partir d\'aujourd\'hui).');
            redirect(url('achats', 'billetterie'));
        }

        // Si visiteur non connecté : enregistrement en session et redirection vers login
        if (!estConnecte() || $role === '') {
            $_SESSION['achat_en_attente'] = [
                'date_visite' => $dateVisite,
                'lignes' => $lignes,
                'code_promo' => $codePromo,
            ];
            flash('erreur', 'Connectez-vous ou inscrivez-vous pour valider et régler votre commande de billets.');
            redirect(url('auth', 'login'));
        }

        $clientModel = new Client($this->pdo);
        $idClient = 0;

        if ($role === 'client') {
            $idClient = (int)idClientSession();
            // Sécurité : si le client n'a pas son id_client en session, on le résout ou le crée
            if (!$idClient && estConnecte()) {
                $idClient = (int)$clientModel->obtenirOuCreerPourUtilisateur(idUtilisateur());
            }
        } else {
            // Personnel (admin, responsable, agent)
            $choix = post('id_client');
            if ($choix === 'moi' || empty($choix)) {
                // Utilise / crée le compte client associé au compte personnel
                $idClient = (int)$clientModel->obtenirOuCreerPourUtilisateur(idUtilisateur());
            } else {
                $idClient = (int)$choix;
            }
        }

        // Vérification de l'existence du client
        if (!$idClient || !$clientModel->trouver($idClient)) {
            flash('erreur', 'Veuillez choisir un client valide pour cette commande.');
            redirect(url('achats', 'billetterie'));
        }

        $res = $this->achat->creer($idClient, $dateVisite, $lignes, $codePromo);
        if (isset($res['erreurs'])) {
            flash('erreur', implode(' ', $res['erreurs']));
            redirect(url('achats', 'billetterie'));
        }

        flash('succes', 'Commande ' . $res['numero'] . ' validée ! Vous pouvez maintenant procéder au règlement.');
        redirect(url('paiement', 'recapitulatif', ['type' => 'achat', 'id' => $res['id']]));
    }

    public function actionDetail() {
        exigerRole(['admin', 'responsable', 'agent', 'client']);

        $id = (int)($_GET['id'] ?? 0);
        $achat = $this->achat->trouverComplet($id);

        if (!$achat) {
            flash('erreur', 'Commande introuvable.');
            redirect(url('achats'));
        }

        if (roleActuel() === 'client' && (int)$achat['id_client'] !== (int)idClientSession()) {
            flash('erreur', "Accès refusé.");
            redirect(url('achats'));
        }

        vue('achats/detail', [
            'achat' => $achat,
            'titrePage' => 'Détail de la commande ' . $achat['numero_achat'],
        ]);
    }

    public function actionImprimer() {
        exigerRole(['admin', 'responsable', 'agent', 'client']);
        $id = (int)($_GET['id'] ?? 0);
        $achat = $this->achat->trouverComplet($id);

        if (!$achat) {
            die('Commande introuvable.');
        }

        if (roleActuel() === 'client' && (int)$achat['id_client'] !== (int)idClientSession()) {
            die('Accès refusé.');
        }

        require RACINE . '/views/achats/imprimer.php';
    }

    // Gestion du statut par le personnel (ex: encaissement guichet ou annulation)
    public function actionStatut() {
        exigerRole(['admin', 'responsable', 'agent']);
        $id = (int)post('id');

        $nouveau = post('statut');
        $mode = post('mode_paiement', 'especes');
        $role = roleActuel();

        $achat = $this->achat->trouver($id);
        if (!$achat) {
            flash('erreur', 'Commande introuvable.');
            redirect(url('achats'));
        }

        if ($role === 'client' && $nouveau !== 'annule') {
            flash('erreur', "Action non autorisée.");
            redirect(url('achats'));
        }

        if ($nouveau === 'paye' && in_array($role, ['admin', 'agent'], true)) {
            $ref = 'GUICHET-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
            $msg = $this->achat->enregistrerPaiement($id, $mode, $ref);
            if ($msg === '') flash('succes', "Paiement au guichet enregistré avec succès !");
            else flash('erreur', $msg);
        } elseif ($nouveau === 'annule') {
            $msg = $this->achat->annuler($id);
            if ($msg === '') flash('succes', "Commande annulée avec succès.");
            else flash('erreur', $msg);
        }

        redirect(url('achats'));
    }
}
