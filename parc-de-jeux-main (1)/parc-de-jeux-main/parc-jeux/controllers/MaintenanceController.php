<?php
// Maintenance : interventions (administrateur) et signalement de pannes (responsable)
class MaintenanceController {
    private $pdo, $maintenance;
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->maintenance = new Maintenance($pdo);
    }

    public function actionIndex() {
        exigerRole(['admin', 'responsable']);
        $filtres = ['id_jeu' => $_GET['id_jeu'] ?? '', 'etat' => $_GET['etat'] ?? ''];
        $idResp = (roleActuel() === 'responsable') ? (idResponsableSession() ?: -1) : null;
        vue('maintenance/index', ['maintenances' => $this->maintenance->tous($filtres, $idResp), 'filtres' => $filtres,
                                  'jeux' => (new Jeu($this->pdo))->actifs()]);
    }

    public function actionAjouter()  { exigerRole(['admin']); $this->formulaire(null); }
    public function actionModifier() { exigerRole(['admin']); $this->formulaire((int)($_GET['id'] ?? 0)); }

    private function formulaire($id) {
        $m = ['id_jeu' => $_GET['id_jeu'] ?? '', 'type_intervention' => '', 'description' => '', 'date_intervention' => date('Y-m-d'),
              'cout' => '0', 'etat' => 'planifiee', 'date_prochaine' => '', 'id_responsable' => ''];
        if ($id) {
            $m = $this->maintenance->trouver($id);
            if (!$m) { flash('erreur', 'Intervention introuvable.'); redirect(url('maintenance')); }
        }
        $erreurs = [];
        if (estPost()) {
            $m = champsPost(['id_jeu', 'type_intervention', 'description', 'date_intervention', 'cout', 'etat', 'date_prochaine', 'id_responsable']);
            $jeu = (new Jeu($this->pdo))->trouver((int)$m['id_jeu']);
            if (!$jeu) $erreurs[] = "Choisissez le jeu concerné.";
            if ($m['type_intervention'] === '') $erreurs[] = "Le type d'intervention est obligatoire.";
            if (!dateValide($m['date_intervention'])) $erreurs[] = "Date d'intervention invalide.";
            if (!is_numeric($m['cout']) || $m['cout'] < 0) $erreurs[] = "Le coût doit être un nombre positif ou 0.";
            if (!in_array($m['etat'], ['signalee', 'planifiee', 'en_cours', 'terminee'])) $erreurs[] = "État invalide.";
            if ($m['date_prochaine'] !== '' && !dateValide($m['date_prochaine'])) $erreurs[] = "Date de prochaine maintenance invalide.";
            if (!$erreurs) {
                $m['date_prochaine'] = ($m['date_prochaine'] === '') ? null : $m['date_prochaine'];
                $m['id_responsable'] = ($m['id_responsable'] === '') ? null : (int)$m['id_responsable'];
                if ($id) $this->maintenance->modifier($id, $m); else $this->maintenance->ajouter($m);
                $this->synchroniserEtatJeu($jeu, $m['etat']);
                flash('succes', "Intervention enregistrée.");
                redirect(url('maintenance'));
            }
        }
        vue('maintenance/form', ['m' => $m, 'id' => $id, 'erreurs' => $erreurs, 'jeux' => (new Jeu($this->pdo))->actifs(),
                                 'responsables' => (new Responsable($this->pdo))->actifs()]);
    }

    // Règle simple : intervention en cours => jeu "en maintenance" ; terminée => jeu "disponible"
    private function synchroniserEtatJeu($jeu, $etatIntervention) {
        $modele = new Jeu($this->pdo);
        if ($etatIntervention === 'en_cours') $modele->changerEtat($jeu['id_jeu'], 'en_maintenance');
        if ($etatIntervention === 'terminee' && $jeu['etat'] === 'en_maintenance') $modele->changerEtat($jeu['id_jeu'], 'disponible');
    }

    // Le responsable signale une panne sur l'un de ses jeux
    public function actionSignaler() {
        exigerRole(['admin', 'responsable']);
        $idResp = (roleActuel() === 'responsable') ? (idResponsableSession() ?: -1) : null;
        $jeux = (new Jeu($this->pdo))->tous(['q' => '', 'etat' => '', 'categorie' => '', 'id_responsable' => ''], roleActuel(), $idResp);
        $s = ['id_jeu' => $_GET['id_jeu'] ?? '', 'description' => ''];
        $erreurs = [];
        if (estPost()) {
            $s = champsPost(['id_jeu', 'description']);
            $jeu = (new Jeu($this->pdo))->trouver((int)$s['id_jeu']);
            if (!$jeu || ($idResp && $jeu['id_responsable'] != $idResp)) $erreurs[] = "Choisissez l'un de vos jeux.";
            if ($s['description'] === '') $erreurs[] = "Décrivez la panne ou le besoin.";
            if (!$erreurs) {
                $this->maintenance->ajouter(['type_intervention' => 'Panne signalée', 'description' => $s['description'],
                    'date_intervention' => date('Y-m-d'), 'cout' => 0, 'etat' => 'signalee', 'date_prochaine' => null,
                    'id_jeu' => $jeu['id_jeu'], 'id_responsable' => $jeu['id_responsable']]);
                flash('succes', "Panne signalée. L'administrateur va la traiter.");
                redirect(url('maintenance'));
            }
        }
        vue('maintenance/signaler', ['s' => $s, 'erreurs' => $erreurs, 'jeux' => $jeux]);
    }
}
