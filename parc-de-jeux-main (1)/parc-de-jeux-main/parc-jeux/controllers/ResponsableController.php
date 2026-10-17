<?php
// Gestion des responsables (administrateur)
class ResponsableController {
    private $pdo, $responsable;
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->responsable = new Responsable($pdo);
        exigerRole(['admin']);
    }

    public function actionIndex() {
        $q = trim($_GET['q'] ?? '');
        vue('responsables/index', ['responsables' => $this->responsable->tous($q), 'q' => $q]);
    }

    public function actionDetail() {
        $r = $this->responsable->trouver((int)($_GET['id'] ?? 0));
        if (!$r) { flash('erreur', 'Responsable introuvable.'); redirect(url('responsables')); }
        vue('responsables/detail', ['r' => $r, 'jeux' => $this->responsable->jeuxDe($r['id_responsable'])]);
    }

    public function actionAjouter()  { $this->formulaire(null); }
    public function actionModifier() { $this->formulaire((int)($_GET['id'] ?? 0)); }

    private function formulaire($id) {
        $r = ['nom' => '', 'prenom' => '', 'telephone' => '', 'email' => '', 'fonction' => '', 'date_embauche' => date('Y-m-d'), 'statut' => 'actif'];
        if ($id) {
            $r = $this->responsable->trouver($id);
            if (!$r) { flash('erreur', 'Responsable introuvable.'); redirect(url('responsables')); }
        }
        $erreurs = [];
        if (estPost()) {
            $r = champsPost(['nom', 'prenom', 'telephone', 'email', 'fonction', 'date_embauche', 'statut']);
            if ($r['nom'] === '' || $r['prenom'] === '') $erreurs[] = "Le nom et le prénom sont obligatoires.";
            if ($r['email'] !== '' && !filter_var($r['email'], FILTER_VALIDATE_EMAIL)) $erreurs[] = "Adresse électronique invalide.";
            if ($r['telephone'] !== '' && !preg_match('/^[0-9 +().-]{6,20}$/', $r['telephone'])) $erreurs[] = "Numéro de téléphone invalide.";
            if (!dateValide($r['date_embauche'])) $erreurs[] = "Date d'embauche invalide.";
            if (!in_array($r['statut'], ['actif', 'inactif'])) $erreurs[] = "Statut invalide.";
            if (!$erreurs) {
                if ($id) { $this->responsable->modifier($id, $r); flash('succes', "Responsable modifié."); }
                else     { $id = $this->responsable->ajouter($r); flash('succes', "Responsable créé. Vous pouvez maintenant l'affecter à des jeux."); }
                redirect(url('responsables', 'detail', ['id' => $id]));
            }
        }
        vue('responsables/form', ['r' => $r, 'id' => $id, 'erreurs' => $erreurs]);
    }

    public function actionBasculer() {
        $id = (int)post('id');
        $r = estPost() ? $this->responsable->trouver($id) : null;
        if ($r) {
            $nouveau = ($r['statut'] === 'actif') ? 'inactif' : 'actif';
            $this->responsable->changerStatut($id, $nouveau);
            flash('succes', $nouveau === 'inactif' ? "Responsable désactivé (ses jeux n'ont plus de responsable)." : "Responsable réactivé.");
        }
        redirect(url('responsables'));
    }

    // Affecter un responsable à un ou plusieurs jeux
    public function actionAffecter() {
        $r = $this->responsable->trouver((int)($_GET['id'] ?? $_POST['id'] ?? 0));
        if (!$r || $r['statut'] !== 'actif') { flash('erreur', 'Responsable introuvable ou inactif.'); redirect(url('responsables')); }
        if (estPost()) {
            $ids = (isset($_POST['jeux']) && is_array($_POST['jeux'])) ? $_POST['jeux'] : [];
            $this->responsable->affecter($r['id_responsable'], $ids);
            flash('succes', "Affectations enregistrées.");
            redirect(url('responsables', 'detail', ['id' => $r['id_responsable']]));
        }
        vue('responsables/affecter', ['r' => $r, 'jeux' => (new Jeu($this->pdo))->actifs()]);
    }
}
