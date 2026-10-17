<?php
// Gestion des jeux
class JeuController {
    private $pdo, $jeu, $responsable;
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->jeu = new Jeu($pdo);
        $this->responsable = new Responsable($pdo);
    }

    // Liste + recherche (tous les rôles ; le modèle limite l'affichage selon le rôle)
    public function actionIndex() {
        $filtres = ['q' => trim($_GET['q'] ?? ''), 'etat' => $_GET['etat'] ?? '', 'categorie' => $_GET['categorie'] ?? '',
                    'id_responsable' => $_GET['id_responsable'] ?? ''];
        $jeux = $this->jeu->tous($filtres, roleActuel(), idResponsableSession());
        vue('jeux/index', ['jeux' => $jeux, 'filtres' => $filtres, 'responsables' => $this->responsable->tous()]);
    }

    // Détails d'un jeu (+ planning, dépenses et maintenance pour l'admin / le responsable)
    public function actionDetail() {
        $jeu = $this->jeu->trouver((int)($_GET['id'] ?? 0));
        if (!$jeu) { flash('erreur', 'Jeu introuvable.'); redirect(url('jeux')); }

        $role = roleActuel();
        if ($role === 'responsable' && $jeu['id_responsable'] != idResponsableSession()) {
            flash('erreur', "Ce jeu ne vous est pas affecté."); redirect(url('jeux'));
        }
        if (!in_array($role, ['admin', 'responsable'], true) && !$jeu['actif']) {
            flash('erreur', "Ce jeu n'est plus disponible."); redirect(url('jeux'));
        }
        $donnees = ['jeu' => $jeu, 'reservations' => [], 'depenses' => [], 'maintenances' => []];
        if (in_array($role, ['admin', 'responsable', 'agent'])) $donnees['reservations'] = (new Reservation($this->pdo))->parJeu($jeu['id_jeu']);
        if (in_array($role, ['admin', 'responsable'])) {
            $donnees['depenses'] = (new Depense($this->pdo))->parJeu($jeu['id_jeu']);
            $donnees['maintenances'] = (new Maintenance($this->pdo))->parJeu($jeu['id_jeu']);
        }
        vue('jeux/detail', $donnees);
    }

    public function actionAjouter()  { exigerRole(['admin']); $this->formulaire(null); }
    public function actionModifier() { exigerRole(['admin']); $this->formulaire((int)($_GET['id'] ?? 0)); }

    private function formulaire($id) {
        $jeu = ['nom' => '', 'description' => '', 'categorie' => 'Manège', 'age_minimum' => 0, 'capacite' => '', 'duree_session' => '',
                'tarif' => '', 'etat' => 'disponible', 'date_mise_service' => date('Y-m-d'), 'id_responsable' => ''];
        if ($id) {
            $jeu = $this->jeu->trouver($id);
            if (!$jeu) { flash('erreur', 'Jeu introuvable.'); redirect(url('jeux')); }
        }
        $erreurs = [];
        if (estPost()) {
            $jeu = champsPost(['nom', 'description', 'categorie', 'age_minimum', 'capacite', 'duree_session', 'tarif', 'etat', 'date_mise_service', 'id_responsable']);
            $erreurs = $this->valider($jeu);
            if (!$erreurs) {
                $jeu['id_responsable'] = ($jeu['id_responsable'] === '') ? null : (int)$jeu['id_responsable'];
                if ($id) { $this->jeu->modifier($id, $jeu); flash('succes', "Jeu modifié."); }
                else     { $id = $this->jeu->ajouter($jeu);  flash('succes', "Jeu ajouté."); }
                redirect(url('jeux', 'detail', ['id' => $id]));
            }
        }
        vue('jeux/form', ['jeu' => $jeu, 'id' => $id, 'erreurs' => $erreurs, 'responsables' => $this->responsable->actifs()]);
    }

    private function valider($j) {
        $erreurs = [];
        if ($j['nom'] === '') $erreurs[] = "Le nom est obligatoire.";
        if (!in_array($j['categorie'], CATEGORIES_JEUX)) $erreurs[] = "Catégorie invalide.";
        if (!ctype_digit($j['age_minimum']))   $erreurs[] = "L'âge minimum doit être un nombre positif ou 0.";
        if (!ctype_digit($j['capacite']) || $j['capacite'] < 1)         $erreurs[] = "La capacité doit être un nombre supérieur à 0.";
        if (!ctype_digit($j['duree_session']) || $j['duree_session'] < 1) $erreurs[] = "La durée de session doit être supérieure à 0.";
        if (!is_numeric($j['tarif']) || $j['tarif'] < 0) $erreurs[] = "Le tarif doit être un nombre positif.";
        if (!in_array($j['etat'], ETATS_JEU)) $erreurs[] = "État invalide.";
        if (!dateValide($j['date_mise_service'])) $erreurs[] = "Date de mise en service invalide.";
        return $erreurs;
    }

    // Désactiver / réactiver un jeu (il n'est jamais supprimé)
    public function actionBasculer() {
        exigerRole(['admin']);
        $id = (int)post('id');
        $jeu = estPost() ? $this->jeu->trouver($id) : null;
        if ($jeu) {
            $this->jeu->changerActif($id, $jeu['actif'] ? 0 : 1);
            flash('succes', $jeu['actif'] ? "Jeu désactivé." : "Jeu réactivé.");
        }
        redirect(url('jeux'));
    }
}
