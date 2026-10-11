<?php
// Modèle Billet : types de billets (tarifs d'accès au parc)
class Billet {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    // ----- Types de billets (tarifs) -----
    public function types() {
        return $this->pdo->query("SELECT * FROM type_billet ORDER BY tarif")->fetchAll();
    }

    public function trouverType($id) {
        $req = $this->pdo->prepare("SELECT * FROM type_billet WHERE id_type_billet = ?");
        $req->execute([$id]);
        return $req->fetch();
    }

    public function ajouterType($d) {
        $req = $this->pdo->prepare("INSERT INTO type_billet (nom, tarif, duree_validite) VALUES (?, ?, ?)");
        $req->execute([$d['nom'], $d['tarif'], $d['duree_validite']]);
    }

    public function modifierType($id, $d) {
        $req = $this->pdo->prepare("UPDATE type_billet SET nom=?, tarif=?, duree_validite=? WHERE id_type_billet=?");
        $req->execute([$d['nom'], $d['tarif'], $d['duree_validite'], $id]);
    }
}
