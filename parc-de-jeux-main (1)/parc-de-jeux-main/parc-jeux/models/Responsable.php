<?php
// Modèle Responsable : accès à la table responsable
class Responsable {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function tous($q = '') {
        $req = $this->pdo->prepare("SELECT r.*, (SELECT COUNT(*) FROM jeu j WHERE j.id_responsable = r.id_responsable) AS nb_jeux
                                    FROM responsable r WHERE r.nom LIKE ? OR r.prenom LIKE ? OR r.fonction LIKE ? ORDER BY r.nom");
        $req->execute(['%' . $q . '%', '%' . $q . '%', '%' . $q . '%']);
        return $req->fetchAll();
    }

    public function actifs() {
        return $this->pdo->query("SELECT * FROM responsable WHERE statut = 'actif' ORDER BY nom")->fetchAll();
    }

    public function trouver($id) {
        $req = $this->pdo->prepare("SELECT * FROM responsable WHERE id_responsable = ?");
        $req->execute([$id]);
        return $req->fetch();
    }

    public function ajouter($d) {
        $req = $this->pdo->prepare("INSERT INTO responsable (nom, prenom, telephone, email, fonction, date_embauche, statut) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $req->execute([$d['nom'], $d['prenom'], $d['telephone'], $d['email'], $d['fonction'], $d['date_embauche'], $d['statut']]);
        return $this->pdo->lastInsertId();
    }

    public function modifier($id, $d) {
        $req = $this->pdo->prepare("UPDATE responsable SET nom=?, prenom=?, telephone=?, email=?, fonction=?, date_embauche=?, statut=? WHERE id_responsable=?");
        $req->execute([$d['nom'], $d['prenom'], $d['telephone'], $d['email'], $d['fonction'], $d['date_embauche'], $d['statut'], $id]);
    }

    // Désactiver : le responsable n'est plus affecté à aucun jeu
    public function changerStatut($id, $statut) {
        $req = $this->pdo->prepare("UPDATE responsable SET statut = ? WHERE id_responsable = ?");
        $req->execute([$statut, $id]);
        if ($statut === 'inactif') {
            $req = $this->pdo->prepare("UPDATE jeu SET id_responsable = NULL WHERE id_responsable = ?");
            $req->execute([$id]);
        }
    }

    public function jeuxDe($id) {
        $req = $this->pdo->prepare("SELECT * FROM jeu WHERE id_responsable = ? ORDER BY nom");
        $req->execute([$id]);
        return $req->fetchAll();
    }

    // Affecte le responsable aux jeux cochés (et retire les autres)
    public function affecter($id, $idsJeux) {
        $this->pdo->beginTransaction();
        $req = $this->pdo->prepare("UPDATE jeu SET id_responsable = NULL WHERE id_responsable = ?");
        $req->execute([$id]);
        $req = $this->pdo->prepare("UPDATE jeu SET id_responsable = ? WHERE id_jeu = ?");
        foreach ($idsJeux as $idJeu) {
            $req->execute([$id, (int)$idJeu]);
        }
        $this->pdo->commit();
    }
}
