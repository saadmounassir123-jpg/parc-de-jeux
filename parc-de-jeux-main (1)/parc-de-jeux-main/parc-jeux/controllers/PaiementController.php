<?php
// Contrôleur de paiement en ligne (Carte bancaire, Guichet, Virement)
class PaiementController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        exigerRole(['admin', 'responsable', 'agent', 'client']);
    }

    public function actionIndex() {
        redirect(url('accueil'));
    }

    // Récapitulatif de la commande / réservation et choix du mode de paiement
    public function actionRecapitulatif() {
        $type = $_GET['type'] ?? 'achat';
        $id = (int)($_GET['id'] ?? 0);
        $role = roleActuel();
        $idClient = idClientSession();
        if ($role === 'client' && !$idClient && estConnecte()) {
            $idClient = (new Client($this->pdo))->obtenirOuCreerPourUtilisateur(idUtilisateur());
        }


        $objet = null;
        $montantTotal = 0;
        $montantBrut = 0;
        $remise = 0;
        $codePromo = '';
        $reference = '';
        $statut = '';

        if ($type === 'achat') {
            $achatModel = new Achat($this->pdo);
            $objet = $achatModel->trouverComplet($id);
            if (!$objet) {
                flash('erreur', 'Commande de billets introuvable.');
                redirect(url('billets', 'types'));
            }
            if ($role === 'client' && (int)$objet['id_client'] !== (int)$idClient) {
                flash('erreur', 'Accès refusé.');
                redirect(url('accueil'));
            }
            $montantTotal = (float)$objet['montant_total'];
            $montantBrut = (float)$objet['montant_brut'];
            $remise = (float)$objet['remise'];
            $codePromo = $objet['code_promo'];
            $reference = $objet['numero_achat'];
            $statut = $objet['statut'];
        } elseif ($type === 'reservation') {
            $resModel = new Reservation($this->pdo);
            $objet = $resModel->trouverComplet($id);
            if (!$objet) {
                flash('erreur', 'Réservation introuvable.');
                redirect(url('reservations'));
            }
            if ($role === 'client' && (int)$objet['id_client'] !== (int)$idClient) {
                flash('erreur', 'Accès refusé.');
                redirect(url('accueil'));
            }
            $montantTotal = (float)$objet['montant'];
            $remise = (float)($objet['remise'] ?? 0);
            $montantBrut = $montantTotal + $remise;
            $codePromo = $objet['code_promo'] ?? '';
            $reference = $objet['numero_reservation'];
            $statut = $objet['statut'];
        } else {
            flash('erreur', 'Type de paiement invalide.');
            redirect(url('accueil'));
        }

        // Si déjà payé, rediriger directement vers la page de succès / reçu
        if ($statut === 'paye' || $statut === 'payee') {
            flash('succes', 'Ce paiement a déjà été effectué.');
            redirect(url('paiement', 'succes', ['type' => $type, 'id' => $id]));
        }

        vue('paiement/recapitulatif', [
            'type' => $type,
            'id' => $id,
            'objet' => $objet,
            'montantTotal' => $montantTotal,
            'montantBrut' => $montantBrut,
            'remise' => $remise,
            'codePromo' => $codePromo,
            'reference' => $reference,
            'statut' => $statut,
            'titrePage' => 'Règlement de votre commande — ' . $reference,
        ]);
    }

    // Traitement du paiement (validation carte ou choix guichet)
    public function actionProcess() {
        if (!estPost()) {
            redirect(url('accueil'));
        }

        $type = post('type');
        $id = (int)post('id');
        $mode = post('mode_paiement', 'carte');
        $role = roleActuel();
        $idClient = idClientSession();
        if ($role === 'client' && !$idClient && estConnecte()) {
            $idClient = (new Client($this->pdo))->obtenirOuCreerPourUtilisateur(idUtilisateur());
        }

        if ($type === 'achat') {

            $achatModel = new Achat($this->pdo);
            $achat = $achatModel->trouver($id);
            if (!$achat || ($role === 'client' && (int)$achat['id_client'] !== (int)$idClient)) {
                flash('erreur', 'Commande introuvable ou non autorisée.');
                redirect(url('achats'));
            }
            if ($achat['statut'] === 'paye') {
                redirect(url('paiement', 'succes', ['type' => 'achat', 'id' => $id]));
            }

            $refPaiement = 'TXN-' . strtoupper($mode) . '-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // Si paiement par carte bancaire : simulation de validation bancaire
            if ($mode === 'carte') {
                $numCarte = preg_replace('/\s+/', '', post('numero_carte'));
                $exp = post('expiration');
                $cvv = post('cvv');
                $titulaire = trim(post('titulaire'));

                if (strlen($numCarte) < 13 || strlen($numCarte) > 19) {
                    flash('erreur', 'Numéro de carte bancaire invalide (16 chiffres requis).');
                    redirect(url('paiement', 'recapitulatif', ['type' => 'achat', 'id' => $id]));
                }
                if ($titulaire === '') {
                    flash('erreur', 'Nom du titulaire de la carte obligatoire.');
                    redirect(url('paiement', 'recapitulatif', ['type' => 'achat', 'id' => $id]));
                }
                if (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $exp)) {
                    flash('erreur', 'Date d\'expiration invalide (format MM/AA attendu).');
                    redirect(url('paiement', 'recapitulatif', ['type' => 'achat', 'id' => $id]));
                }
                if (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
                    flash('erreur', 'Cryptogramme visuel (CVV) invalide (3 chiffres attendus).');
                    redirect(url('paiement', 'recapitulatif', ['type' => 'achat', 'id' => $id]));
                }

                $err = $achatModel->enregistrerPaiement($id, 'carte', $refPaiement);
            } elseif ($mode === 'especes') {
                // Paiement prévu au guichet lors du retrait
                $refPaiement = 'GUICHET-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
                $err = ''; // Statut reste en_attente jusqu'au paiement physique
            } else {
                $err = $achatModel->enregistrerPaiement($id, 'virement', $refPaiement);
            }

            if ($err !== '') {
                flash('erreur', $err);
                redirect(url('paiement', 'recapitulatif', ['type' => 'achat', 'id' => $id]));
            }

            flash('succes', $mode === 'especes' 
                ? 'Commande enregistrée avec succès. Vous pourrez régler en espèces à la billetterie du parc.' 
                : 'Paiement effectué avec succès ! Réf : ' . $refPaiement);
            redirect(url('paiement', 'succes', ['type' => 'achat', 'id' => $id]));

        } elseif ($type === 'reservation') {
            $resModel = new Reservation($this->pdo);
            $resa = $resModel->trouver($id);
            if (!$resa || ($role === 'client' && (int)$resa['id_client'] !== (int)$idClient)) {
                flash('erreur', 'Réservation introuvable ou non autorisée.');
                redirect(url('reservations'));
            }
            if ($resa['statut'] === 'payee') {
                redirect(url('paiement', 'succes', ['type' => 'reservation', 'id' => $id]));
            }

            $refPaiement = 'TXN-' . strtoupper($mode) . '-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            if ($mode === 'carte') {
                $numCarte = preg_replace('/\s+/', '', post('numero_carte'));
                $exp = post('expiration');
                $cvv = post('cvv');
                $titulaire = trim(post('titulaire'));

                if (strlen($numCarte) < 13 || strlen($numCarte) > 19) {
                    flash('erreur', 'Numéro de carte bancaire invalide.');
                    redirect(url('paiement', 'recapitulatif', ['type' => 'reservation', 'id' => $id]));
                }
                if ($titulaire === '') {
                    flash('erreur', 'Nom du titulaire de la carte obligatoire.');
                    redirect(url('paiement', 'recapitulatif', ['type' => 'reservation', 'id' => $id]));
                }
                if (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $exp)) {
                    flash('erreur', 'Date d\'expiration invalide (MM/AA).');
                    redirect(url('paiement', 'recapitulatif', ['type' => 'reservation', 'id' => $id]));
                }
                if (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
                    flash('erreur', 'Cryptogramme (CVV) invalide.');
                    redirect(url('paiement', 'recapitulatif', ['type' => 'reservation', 'id' => $id]));
                }

                $err = $resModel->changerStatut($id, 'payee', 'carte', $refPaiement);
            } elseif ($mode === 'especes') {
                $err = $resModel->changerStatut($id, 'confirmee');
            } else {
                $err = $resModel->changerStatut($id, 'payee', 'virement', $refPaiement);
            }

            if ($err !== '') {
                flash('erreur', $err);
                redirect(url('paiement', 'recapitulatif', ['type' => 'reservation', 'id' => $id]));
            }

            flash('succes', $mode === 'especes'
                ? 'Réservation confirmée. Le règlement se fera sur place à la billetterie.'
                : 'Paiement en ligne validé avec succès ! Réf : ' . $refPaiement);
            redirect(url('paiement', 'succes', ['type' => 'reservation', 'id' => $id]));
        } else {
            flash('erreur', 'Opération invalide.');
            redirect(url('accueil'));
        }
    }

    // Page de confirmation de paiement et reçu / billet numérique
    public function actionSucces() {
        $type = $_GET['type'] ?? 'achat';
        $id = (int)($_GET['id'] ?? 0);
        $role = roleActuel();
        $idClient = idClientSession();
        if ($role === 'client' && !$idClient && estConnecte()) {
            $idClient = (new Client($this->pdo))->obtenirOuCreerPourUtilisateur(idUtilisateur());
        }

        $objet = null;

        if ($type === 'achat') {
            $objet = (new Achat($this->pdo))->trouverComplet($id);
        } else {
            $objet = (new Reservation($this->pdo))->trouverComplet($id);
        }

        if (!$objet) {
            flash('erreur', 'Élément introuvable.');
            redirect(url('accueil'));
        }

        if ($role === 'client' && (int)$objet['id_client'] !== (int)$idClient) {
            flash('erreur', 'Accès refusé.');
            redirect(url('accueil'));
        }

        vue('paiement/succes', [
            'type' => $type,
            'id' => $id,
            'objet' => $objet,
            'titrePage' => 'Confirmation de paiement & Billet électronique',
        ]);
    }
}
