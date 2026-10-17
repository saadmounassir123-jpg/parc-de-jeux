<?php
// Modèle Client : accès à la table client
class Client {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function tous($q = '') {
        $req = $this->pdo->prepare("SELECT * FROM client WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? OR telephone LIKE ? ORDER BY nom");
        $like = '%' . $q . '%';
        $req->execute([$like, $like, $like, $like]);
        return $req->fetchAll();
    }

    public function trouver($id) {
        $req = $this->pdo->prepare("SELECT * FROM client WHERE id_client = ?");
        $req->execute([$id]);
        return $req->fetch();
    }

    public function trouverParEmail($email) {
        $req = $this->pdo->prepare("SELECT * FROM client WHERE email = ? LIMIT 1");
        $req->execute([$email]);
        return $req->fetch();
    }

    public function ajouter($d) {
        $req = $this->pdo->prepare("INSERT INTO client (nom, prenom, telephone, email, type_client) VALUES (?, ?, ?, ?, ?)");
        $req->execute([$d['nom'], $d['prenom'], $d['telephone'], $d['email'], $d['type_client']]);
        return (int)$this->pdo->lastInsertId();
    }

    public function modifier($id, $d) {
        $req = $this->pdo->prepare("UPDATE client SET nom=?, prenom=?, telephone=?, email=?, type_client=? WHERE id_client=?");
        $req->execute([$d['nom'], $d['prenom'], $d['telephone'], $d['email'], $d['type_client'], $id]);
    }


    // Récupère ou crée automatiquement un profil client pour un utilisateur donné
    public function obtenirOuCreerPourUtilisateur($userOrId) {
        if (is_numeric($userOrId)) {
            $user = (new User($this->pdo))->trouver((int)$userOrId);
        } else {
            $user = $userOrId;
        }
        if (!$user) return null;

        // 1. Si l'utilisateur a déjà un id_client valide dans la table client
        if (!empty($user['id_client'])) {
            $existant = $this->trouver((int)$user['id_client']);
            if ($existant) {
                return (int)$existant['id_client'];
            }
        }

        // 2. Sinon chercher par email dans client
        if (!empty($user['email'])) {
            $existant = $this->trouverParEmail($user['email']);
            if ($existant) {
                $idClient = (int)$existant['id_client'];
                $this->pdo->prepare("UPDATE utilisateur SET id_client = ? WHERE id_utilisateur = ?")
                          ->execute([$idClient, (int)$user['id_utilisateur']]);
                if (isset($_SESSION['user']) && (int)($_SESSION['user']['id'] ?? 0) === (int)$user['id_utilisateur']) {
                    $_SESSION['user']['id_client'] = $idClient;
                }
                return $idClient;
            }
        }

        // 3. Sinon créer un nouveau profil client associé
        $nom = !empty($user['nom']) ? $user['nom'] : 'Client';
        $prenom = !empty($user['prenom']) ? $user['prenom'] : 'Utilisateur';
        $email = !empty($user['email']) ? $user['email'] : ('client_' . uniqid() . '@parcjeux.local');

        $idClient = (int)$this->ajouter([
            'nom' => $nom,
            'prenom' => $prenom,
            'telephone' => '0600000000',
            'email' => $email,
            'type_client' => 'individuel',
        ]);

        $this->pdo->prepare("UPDATE utilisateur SET id_client = ? WHERE id_utilisateur = ?")
                  ->execute([$idClient, (int)$user['id_utilisateur']]);

        if (isset($_SESSION['user']) && (int)($_SESSION['user']['id'] ?? 0) === (int)$user['id_utilisateur']) {
            $_SESSION['user']['id_client'] = $idClient;
        }

        return $idClient;
    }
}

