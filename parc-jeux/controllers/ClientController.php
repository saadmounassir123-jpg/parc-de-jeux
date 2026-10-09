<?php
// Gestion des clients (administrateur et agent)
class ClientController {
    private $pdo, $client;
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->client = new Client($pdo);
        exigerRole(['admin', 'agent']);
    }

    public function actionIndex() {
        $q = trim($_GET['q'] ?? '');
        vue('clients/index', ['clients' => $this->client->tous($q), 'q' => $q]);
    }

    public function actionDetail() {
        $c = $this->client->trouver((int)($_GET['id'] ?? 0));
        if (!$c) { flash('erreur', 'Client introuvable.'); redirect(url('clients')); }
        $vide = ['date_debut' => '', 'date_fin' => '', 'statut' => '', 'q' => '', 'id_client' => '', 'id_jeu' => '', 'id_responsable' => ''];
        vue('clients/detail', ['c' => $c,
            'achats' => (new Achat($this->pdo))->tous($vide, $c['id_client']),
            'reservations' => (new Reservation($this->pdo))->tous($vide, $c['id_client'])]);
    }

    public function actionAjouter()  { $this->formulaire(null); }
    public function actionModifier() { $this->formulaire((int)($_GET['id'] ?? 0)); }

    private function formulaire($id) {
        $c = ['nom' => '', 'prenom' => '', 'telephone' => '', 'email' => '', 'type_client' => 'individuel'];
        if ($id) {
            $c = $this->client->trouver($id);
            if (!$c) { flash('erreur', 'Client introuvable.'); redirect(url('clients')); }
        }
        $erreurs = [];
        if (estPost()) {
            $c = champsPost(['nom', 'prenom', 'telephone', 'email', 'type_client']);
            if ($c['nom'] === '' || $c['prenom'] === '') $erreurs[] = "Le nom et le prénom sont obligatoires.";
            if ($c['email'] !== '' && !filter_var($c['email'], FILTER_VALIDATE_EMAIL)) $erreurs[] = "Adresse électronique invalide.";
            if ($c['telephone'] !== '' && !preg_match('/^[0-9 +().-]{6,20}$/', $c['telephone'])) $erreurs[] = "Numéro de téléphone invalide.";
            if (!in_array($c['type_client'], ['individuel', 'famille', 'groupe'])) $erreurs[] = "Type de client invalide.";
            if (!$erreurs) {
                if ($id) { $this->client->modifier($id, $c); flash('succes', "Client modifié."); }
                else     { $id = $this->client->ajouter($c);  flash('succes', "Client enregistré."); }
                redirect(url('clients', 'detail', ['id' => $id]));
            }
        }
        vue('clients/form', ['c' => $c, 'id' => $id, 'erreurs' => $erreurs]);
    }
}
