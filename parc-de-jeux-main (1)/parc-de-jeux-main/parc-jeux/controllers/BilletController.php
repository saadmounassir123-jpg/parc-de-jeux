<?php
// Types de billets (tarifs d'accès au parc)
class BilletController {
    private $pdo, $billet;
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->billet = new Billet($pdo);
    }

    public function actionIndex() {
        redirect(url('billets', 'types'));
    }

    // ---------- Tarifs : visibles par tous, modifiables par l'administrateur ----------
    public function actionTypes() {
        vue('billets/types', ['types' => $this->billet->types()]);
    }

    public function actionTypeAjouter() {
        exigerRole(['admin']);
        $this->formulaireType(null);
    }

    public function actionTypeModifier() {
        exigerRole(['admin']);
        $this->formulaireType((int)($_GET['id'] ?? 0));
    }

    private function formulaireType($id) {
        $type = ['nom' => '', 'tarif' => '', 'duree_validite' => 1];
        if ($id) {
            $type = $this->billet->trouverType($id);
            if (!$type) {
                flash('erreur', 'Type de billet introuvable.');
                redirect(url('billets', 'types'));
            }
        }
        $erreurs = [];
        if (estPost()) {
            $type = champsPost(['nom', 'tarif', 'duree_validite']);
            if ($type['nom'] === '') $erreurs[] = "Le nom est obligatoire.";
            if (!is_numeric($type['tarif']) || $type['tarif'] < 0) $erreurs[] = "Le tarif doit être un nombre positif.";
            if (!ctype_digit((string)$type['duree_validite']) || (int)$type['duree_validite'] < 1) $erreurs[] = "La durée de validité doit être d'au moins 1 jour.";
            if (!$erreurs) {
                if ($id) {
                    $this->billet->modifierType($id, $type);
                } else {
                    $this->billet->ajouterType($type);
                }
                flash('succes', "Type de billet enregistré.");
                redirect(url('billets', 'types'));
            }
        }
        vue('billets/type_form', ['type' => $type, 'id' => $id, 'erreurs' => $erreurs]);
    }
}
