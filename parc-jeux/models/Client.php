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

    public function ajouter($d) {
        $req = $this->pdo->prepare("INSERT INTO client (nom, prenom, telephone, email, type_client) VALUES (?, ?, ?, ?, ?)");
        $req->execute([$d['nom'], $d['prenom'], $d['telephone'], $d['email'], $d['type_client']]);
        return $this->pdo->lastInsertId();
    }

    public function modifier($id, $d) {
        $req = $this->pdo->prepare("UPDATE client SET nom=?, prenom=?, telephone=?, email=?, type_client=? WHERE id_client=?");
        $req->execute([$d['nom'], $d['prenom'], $d['telephone'], $d['email'], $d['type_client'], $id]);
    }
}
