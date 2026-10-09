<?php
// Types de billets (tarifs), ventes (achats), vérification et utilisation des billets
class BilletController {
    private $pdo, $billet, $achat;
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->billet = new Billet($pdo);
        $this->achat = new Achat($pdo);
    }

    public function actionIndex() { redirect(url('billets', 'types')); }

    // ---------- Tarifs : visibles par tous, modifiables par l'administrateur ----------
    public function actionTypes() {
        vue('billets/types', ['types' => $this->billet->types()]);
    }

    public function actionTypeAjouter()  { exigerRole(['admin']); $this->formulaireType(null); }
    public function actionTypeModifier() { exigerRole(['admin']); $this->formulaireType((int)($_GET['id'] ?? 0)); }

    private function formulaireType($id) {
        $type = ['nom' => '', 'tarif' => '', 'duree_validite' => 1];
        if ($id) {
            $type = $this->billet->trouverType($id);
            if (!$type) { flash('erreur', 'Type de billet introuvable.'); redirect(url('billets', 'types')); }
        }
        $erreurs = [];
        if (estPost()) {
            $type = champsPost(['nom', 'tarif', 'duree_validite']);
            if ($type['nom'] === '') $erreurs[] = "Le nom est obligatoire.";
            if (!is_numeric($type['tarif']) || $type['tarif'] < 0) $erreurs[] = "Le tarif doit être un nombre positif.";
            if (!ctype_digit($type['duree_validite']) || $type['duree_validite'] < 1) $erreurs[] = "La durée de validité doit être d'au moins 1 jour.";
            if (!$erreurs) {
                if ($id) $this->billet->modifierType($id, $type); else $this->billet->ajouterType($type);
                flash('succes', "Type de billet enregistré.");
                redirect(url('billets', 'types'));
            }
        }
        vue('billets/type_form', ['type' => $type, 'id' => $id, 'erreurs' => $erreurs]);
    }

    // ---------- Billetterie du parc (personnel uniquement) ----------
    public function actionAchats() {
        exigerRole(['admin', 'agent']);
        $filtres = ['date_debut' => $_GET['date_debut'] ?? '', 'date_fin' => $_GET['date_fin'] ?? '',
                    'statut' => $_GET['statut'] ?? '', 'q' => trim($_GET['q'] ?? '')];
        vue('achats/index', ['achats' => $this->achat->tous($filtres, null), 'filtres' => $filtres]);
    }

    public function actionDetailAchat() {
        exigerRole(['admin', 'agent']);
        $achat = $this->achat->trouver((int)($_GET['id'] ?? 0));
        if (!$achat) {
            flash('erreur', "Commande introuvable."); redirect(url('billets', 'achats'));
        }
        vue('achats/detail', ['achat' => $achat, 'billets' => $this->billet->parAchat($achat['id_achat'])]);
    }

    // ---------- Vente de billets au guichet ----------
    public function actionVendre() {
        exigerRole(['admin', 'agent']);
        $erreurs = [];
        $form = ['id_client' => '', 'mode_paiement' => 'especes', 'statut_paiement' => 'paye', 'quantites' => []];

        if (estPost()) {
            $form['id_client'] = post('id_client');
            $form['mode_paiement'] = post('mode_paiement');
            $form['statut_paiement'] = post('statut_paiement');
            $form['quantites'] = (isset($_POST['quantite']) && is_array($_POST['quantite'])) ? $_POST['quantite'] : [];

            if (!(new Client($this->pdo))->trouver((int)$form['id_client'])) $erreurs[] = "Choisissez un client.";
            if (!in_array($form['mode_paiement'], ['especes', 'carte'])) $erreurs[] = "Choisissez un mode de paiement.";
            if (!in_array($form['statut_paiement'], ['paye', 'en_attente'])) $erreurs[] = "Statut de paiement invalide.";

            if (!$erreurs) {
                $resultat = $this->achat->creer((int)$form['id_client'], $form['mode_paiement'], $form['statut_paiement'], $form['quantites']);
                if (isset($resultat['erreur'])) {
                    $erreurs[] = $resultat['erreur'];
                } else {
                    flash('succes', "Vente enregistrée. Montant total calculé automatiquement.");
                    redirect(url('billets', 'detailAchat', ['id' => $resultat['id']]));
                }
            }
        }
        vue('achats/form', ['form' => $form, 'erreurs' => $erreurs, 'types' => $this->billet->types(),
                            'clients' => (new Client($this->pdo))->tous()]);
    }

    // Valider le paiement d'une commande en attente
    public function actionPayer() {
        exigerRole(['admin', 'agent']);
        $id = (int)post('id');
        if (estPost() && $this->achat->payer($id, post('mode_paiement'))) {
            flash('succes', "Paiement validé : la recette a été enregistrée.");
        } else {
            flash('erreur', "Impossible de valider ce paiement.");
        }
        redirect(url('billets', 'detailAchat', ['id' => $id]));
    }

    // ---------- Vérifier la validité d'un billet et enregistrer son utilisation ----------
    public function actionVerifier() {
        exigerRole(['admin', 'agent']);
        $code = trim($_GET['code'] ?? '');
        $billet = null; $probleme = '';
        if ($code !== '') {
            $billet = $this->billet->parCode($code);
            if (!$billet) $probleme = "Aucun billet ne correspond à ce code.";
            else $probleme = $this->billet->verifierValidite($billet);
        }
        vue('billets/verifier', ['code' => $code, 'billet' => $billet, 'probleme' => $probleme]);
    }

    public function actionUtiliser() {
        exigerRole(['admin', 'agent']);
        $code = post('code');
        $billet = estPost() ? $this->billet->parCode($code) : null;
        if ($billet && $this->billet->verifierValidite($billet) === '' && $this->billet->utiliser($billet['id_billet'])) {
            flash('succes', "Utilisation du billet $code enregistrée.");
        } else {
            flash('erreur', "Ce billet ne peut pas être utilisé.");
        }
        redirect(url('billets', 'verifier', ['code' => $code]));
    }
}
