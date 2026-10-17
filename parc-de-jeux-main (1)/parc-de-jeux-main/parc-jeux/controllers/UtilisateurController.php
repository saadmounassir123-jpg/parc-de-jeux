<?php
// Gestion des comptes et des rôles (administrateur)
class UtilisateurController {
    private $pdo, $user;
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->user = new User($pdo);
        exigerRole(['admin']);
    }

    public function actionIndex() {
        vue('utilisateurs/index', ['utilisateurs' => $this->user->tous()]);
    }

    public function actionAjouter()  { $this->formulaire(null); }
    public function actionModifier() { $this->formulaire((int)($_GET['id'] ?? 0)); }

    private function formulaire($id) {
        $compte = ['nom' => '', 'prenom' => '', 'email' => '', 'mot_de_passe' => '', 'role' => 'agent', 'id_responsable' => '', 'id_client' => ''];
        if ($id) {
            $compte = $this->user->trouver($id);
            if (!$compte) { flash('erreur', 'Utilisateur introuvable.'); redirect(url('utilisateurs')); }
            $compte['mot_de_passe'] = '';
        }
        $erreurs = [];
        if (estPost()) {
            $compte = champsPost(['nom', 'prenom', 'email', 'mot_de_passe', 'role', 'id_responsable', 'id_client']);
            if ($compte['nom'] === '' || $compte['prenom'] === '') $erreurs[] = "Le nom et le prénom sont obligatoires.";
            if (!filter_var($compte['email'], FILTER_VALIDATE_EMAIL)) $erreurs[] = "Adresse électronique invalide.";
            elseif ($this->user->emailExiste($compte['email'], (int)$id)) $erreurs[] = "Cette adresse électronique est déjà utilisée.";
            if (!in_array($compte['role'], ['admin', 'responsable', 'agent', 'client'])) $erreurs[] = "Rôle invalide.";
            if (!$id && strlen($compte['mot_de_passe']) < 6) $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères.";
            if ($id && $compte['mot_de_passe'] !== '' && strlen($compte['mot_de_passe']) < 6) $erreurs[] = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
            if ($compte['role'] === 'responsable' && $compte['id_responsable'] === '') $erreurs[] = "Choisissez la fiche responsable liée à ce compte.";
            if ($compte['role'] === 'client' && $compte['id_client'] === '') $erreurs[] = "Choisissez la fiche client liée à ce compte.";

            if (!$erreurs) {
                // Un compte n'est lié qu'à la fiche correspondant à son rôle
                $compte['id_responsable'] = ($compte['role'] === 'responsable') ? (int)$compte['id_responsable'] : null;
                $compte['id_client'] = ($compte['role'] === 'client') ? (int)$compte['id_client'] : null;
                if ($id) $this->user->modifier($id, $compte); else $this->user->ajouter($compte);
                flash('succes', "Compte enregistré.");
                redirect(url('utilisateurs'));
            }
        }
        vue('utilisateurs/form', ['compte' => $compte, 'id' => $id, 'erreurs' => $erreurs,
                                  'responsables' => (new Responsable($this->pdo))->tous(), 'clients' => (new Client($this->pdo))->tous()]);
    }

    public function actionBasculer() {
        $id = (int)post('id');
        if (!estPost() || $id === (int)idUtilisateur()) {
            flash('erreur', "Action impossible (vous ne pouvez pas désactiver votre propre compte).");
            redirect(url('utilisateurs'));
        }
        $compte = $this->user->trouver($id);
        if ($compte) {
            $this->user->changerActif($id, $compte['actif'] ? 0 : 1);
            flash('succes', $compte['actif'] ? "Compte désactivé." : "Compte réactivé.");
        }
        redirect(url('utilisateurs'));
    }
}
