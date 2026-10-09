<?php
// Modèle User : comptes de connexion (table utilisateur)
class User {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function trouverParEmail($email) {
        $req = $this->pdo->prepare("SELECT * FROM utilisateur WHERE email = ? AND actif = 1");
        $req->execute([$email]);
        return $req->fetch();
    }

    public function trouver($id) {
        $req = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = ?");
        $req->execute([$id]);
        return $req->fetch();
    }

    public function tous() {
        return $this->pdo->query("SELECT * FROM utilisateur ORDER BY role, nom")->fetchAll();
    }

    public function emailExiste($email, $exceptId = 0) {
        $req = $this->pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ? AND id_utilisateur <> ?");
        $req->execute([$email, $exceptId]);
        return $req->fetchColumn() > 0;
    }

    public function ajouter($d) {
        $req = $this->pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role, id_responsable, id_client)
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
        $req->execute([$d['nom'], $d['prenom'], $d['email'], password_hash($d['mot_de_passe'], PASSWORD_DEFAULT),
                       $d['role'], $d['id_responsable'], $d['id_client']]);
        return $this->pdo->lastInsertId();
    }

    public function modifier($id, $d) {
        $req = $this->pdo->prepare("UPDATE utilisateur SET nom=?, prenom=?, email=?, role=?, id_responsable=?, id_client=? WHERE id_utilisateur=?");
        $req->execute([$d['nom'], $d['prenom'], $d['email'], $d['role'], $d['id_responsable'], $d['id_client'], $id]);
        if ($d['mot_de_passe'] !== '') {   // mot de passe changé seulement s'il est rempli
            $req = $this->pdo->prepare("UPDATE utilisateur SET mot_de_passe=? WHERE id_utilisateur=?");
            $req->execute([password_hash($d['mot_de_passe'], PASSWORD_DEFAULT), $id]);
        }
    }

    public function changerActif($id, $actif) {
        $req = $this->pdo->prepare("UPDATE utilisateur SET actif = ? WHERE id_utilisateur = ?");
        $req->execute([$actif, $id]);
    }
}
