<?php
// Connexion, inscription visiteur et déconnexion
class AuthController {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function actionLogin() {
        if (estConnecte()) redirect(url('accueil'));
        $erreur = '';
        if (estPost()) {
            $email = post('email');
            $motDePasse = $_POST['mot_de_passe'] ?? '';
            $user = (new User($this->pdo))->trouverParEmail($email);
            if ($user && password_verify($motDePasse, $user['mot_de_passe'])) {
                session_regenerate_id(true);   // sécurité : nouvel identifiant de session
                $_SESSION['user'] = [
                    'id' => $user['id_utilisateur'], 'nom' => $user['nom'], 'prenom' => $user['prenom'],
                    'role' => $user['role'], 'id_responsable' => $user['id_responsable'], 'id_client' => $user['id_client'],
                ];
                finaliserReservationEnAttente($this->pdo);
                redirectApresConnexion();
            }
            $erreur = "Email ou mot de passe incorrect.";
        }
        vueAuth('auth/login', ['erreur' => $erreur, 'titrePage' => 'Connexion']);
    }

    public function actionInscription() {
        if (estConnecte()) redirect(url('accueil'));
        $form = ['nom' => '', 'prenom' => '', 'telephone' => '', 'email' => ''];
        $erreurs = [];

        if (estPost()) {
            $form = champsPost(['nom', 'prenom', 'telephone', 'email']);
            $mdp = $_POST['mot_de_passe'] ?? '';
            $mdp2 = $_POST['mot_de_passe_confirmation'] ?? '';

            if ($form['nom'] === '' || $form['prenom'] === '') $erreurs[] = "Le nom et le prénom sont obligatoires.";
            if ($form['telephone'] === '') $erreurs[] = "Le téléphone est obligatoire.";
            elseif (!preg_match('/^[0-9 +().-]{6,20}$/', $form['telephone'])) $erreurs[] = "Numéro de téléphone invalide.";
            if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) $erreurs[] = "Adresse électronique invalide.";
            elseif ((new User($this->pdo))->emailExiste($form['email'])) $erreurs[] = "Cette adresse électronique est déjà utilisée.";
            if (strlen($mdp) < 6) $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères.";
            if ($mdp !== $mdp2) $erreurs[] = "La confirmation du mot de passe ne correspond pas.";

            if (!$erreurs) {
                $this->pdo->beginTransaction();
                try {
                    $idClient = (new Client($this->pdo))->ajouter([
                        'nom' => $form['nom'], 'prenom' => $form['prenom'],
                        'telephone' => $form['telephone'], 'email' => $form['email'],
                        'type_client' => 'individuel',
                    ]);
                    $idUser = (new User($this->pdo))->ajouter([
                        'nom' => $form['nom'], 'prenom' => $form['prenom'],
                        'email' => $form['email'], 'mot_de_passe' => $mdp,
                        'role' => 'client', 'id_responsable' => null, 'id_client' => $idClient,
                    ]);
                    $this->pdo->commit();
                } catch (Exception $e) {
                    $this->pdo->rollBack();
                    throw $e;
                }
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => $idUser, 'nom' => $form['nom'], 'prenom' => $form['prenom'],
                    'role' => 'client', 'id_responsable' => null, 'id_client' => $idClient,
                ];
                finaliserReservationEnAttente($this->pdo);
                flash('succes', 'Votre compte a été créé. Bienvenue ' . $form['prenom'] . ' !');
                redirect(url('accueil'));
            }
        }
        vueAuth('auth/inscription', ['form' => $form, 'erreurs' => $erreurs, 'titrePage' => 'Créer un compte']);
    }

    public function actionLogout() {
        $_SESSION = [];
        session_destroy();
        redirect(url('accueil'));
    }
}
