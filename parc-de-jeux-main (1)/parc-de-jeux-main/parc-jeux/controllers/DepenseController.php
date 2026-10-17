<?php
// Gestion des dépenses : ajout par l'administrateur, consultation par le responsable (ses jeux)
class DepenseController {
    private $pdo, $depense;
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->depense = new Depense($pdo);
    }

    public function actionIndex() {
        exigerRole(['admin', 'responsable']);
        $filtres = ['date_debut' => $_GET['date_debut'] ?? '', 'date_fin' => $_GET['date_fin'] ?? '', 'id_jeu' => $_GET['id_jeu'] ?? '',
                    'categorie' => $_GET['categorie'] ?? '', 'type' => $_GET['type'] ?? ''];
        $idResp = (roleActuel() === 'responsable') ? (idResponsableSession() ?: -1) : null;
        $depenses = $this->depense->tous($filtres, $idResp);
        $total = 0;
        foreach ($depenses as $d) { $total += $d['montant']; }
        $jeux = (roleActuel() === 'admin') ? (new Jeu($this->pdo))->actifs()
              : (new Jeu($this->pdo))->tous(['q' => '', 'etat' => '', 'categorie' => '', 'id_responsable' => ''], 'responsable', $idResp);
        vue('depenses/index', ['depenses' => $depenses, 'filtres' => $filtres, 'total' => $total, 'jeux' => $jeux]);
    }

    public function actionAjouter() {
        exigerRole(['admin']);
        $d = ['categorie' => 'maintenance', 'description' => '', 'montant' => '', 'date_depense' => date('Y-m-d'),
              'type' => 'jeu', 'justificatif' => '', 'id_jeu' => $_GET['id_jeu'] ?? ''];
        $erreurs = [];
        if (estPost()) {
            $d = champsPost(['categorie', 'description', 'montant', 'date_depense', 'type', 'justificatif', 'id_jeu']);
            if (!in_array($d['categorie'], CATEGORIES_DEPENSE)) $erreurs[] = "Catégorie invalide.";
            if ($d['description'] === '') $erreurs[] = "La description est obligatoire.";
            if (!is_numeric($d['montant']) || $d['montant'] <= 0) $erreurs[] = "Le montant doit être supérieur à 0.";
            if (!dateValide($d['date_depense'])) $erreurs[] = "Date invalide.";
            if (!in_array($d['type'], ['jeu', 'general'])) $erreurs[] = "Type de dépense invalide.";
            if ($d['type'] === 'jeu' && !(new Jeu($this->pdo))->trouver((int)$d['id_jeu'])) $erreurs[] = "Choisissez le jeu concerné.";
            if (!$erreurs) {
                $d['id_jeu'] = ($d['type'] === 'jeu') ? (int)$d['id_jeu'] : null;   // un frais général n'a pas de jeu
                $d['justificatif'] = ($d['justificatif'] === '') ? null : $d['justificatif'];
                $d['id_utilisateur'] = idUtilisateur();
                $this->depense->ajouter($d);
                flash('succes', "Dépense enregistrée.");
                redirect(url('depenses'));
            }
        }
        vue('depenses/form', ['d' => $d, 'erreurs' => $erreurs, 'jeux' => (new Jeu($this->pdo))->actifs()]);
    }
}
