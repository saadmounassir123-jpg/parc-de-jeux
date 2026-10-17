<?php
// Modèle Jeu : accès à la table jeu
class Jeu {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    // Liste avec recherche / filtres. Le rôle limite ce que l'on voit.
    public function tous($f, $role, $idResponsable) {
        $sql = "SELECT j.*, r.nom AS resp_nom, r.prenom AS resp_prenom
                FROM jeu j LEFT JOIN responsable r ON r.id_responsable = j.id_responsable WHERE 1=1";
        $p = [];
        if ($f['q'] !== '')              { $sql .= " AND (j.nom LIKE ? OR j.description LIKE ?)"; $p[] = '%' . $f['q'] . '%'; $p[] = '%' . $f['q'] . '%'; }
        if ($f['etat'] !== '')           { $sql .= " AND j.etat = ?";           $p[] = $f['etat']; }
        if ($f['categorie'] !== '')      { $sql .= " AND j.categorie = ?";      $p[] = $f['categorie']; }
        if ($f['id_responsable'] !== '') { $sql .= " AND j.id_responsable = ?"; $p[] = $f['id_responsable']; }
        if ($role === 'responsable')     { $sql .= " AND j.id_responsable = ?"; $p[] = $idResponsable; }
        elseif ($role !== 'admin')       { $sql .= " AND j.actif = 1"; }
        $sql .= " ORDER BY j.nom";
        $req = $this->pdo->prepare($sql);
        $req->execute($p);
        return $req->fetchAll();
    }

    public function trouver($id) {
        $req = $this->pdo->prepare("SELECT j.*, r.nom AS resp_nom, r.prenom AS resp_prenom
                                    FROM jeu j LEFT JOIN responsable r ON r.id_responsable = j.id_responsable
                                    WHERE j.id_jeu = ?");
        $req->execute([$id]);
        return $req->fetch();
    }

    // Jeux que l'on peut réserver : actifs et disponibles
    public function reservables() {
        return $this->pdo->query("SELECT * FROM jeu WHERE actif = 1 AND etat IN ('disponible','en_fonctionnement') ORDER BY nom")->fetchAll();
    }

    public function actifs() {
        return $this->pdo->query("SELECT * FROM jeu WHERE actif = 1 ORDER BY nom")->fetchAll();
    }

    public function ajouter($d) {
        $req = $this->pdo->prepare("INSERT INTO jeu (nom, description, categorie, age_minimum, capacite, duree_session, tarif, etat, date_mise_service, id_responsable)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $req->execute([$d['nom'], $d['description'], $d['categorie'], $d['age_minimum'], $d['capacite'], $d['duree_session'],
                       $d['tarif'], $d['etat'], $d['date_mise_service'], $d['id_responsable']]);
        return $this->pdo->lastInsertId();
    }

    public function modifier($id, $d) {
        $req = $this->pdo->prepare("UPDATE jeu SET nom=?, description=?, categorie=?, age_minimum=?, capacite=?, duree_session=?,
                                    tarif=?, etat=?, date_mise_service=?, id_responsable=? WHERE id_jeu=?");
        $req->execute([$d['nom'], $d['description'], $d['categorie'], $d['age_minimum'], $d['capacite'], $d['duree_session'],
                       $d['tarif'], $d['etat'], $d['date_mise_service'], $d['id_responsable'], $id]);
    }

    public function changerActif($id, $actif) {
        $req = $this->pdo->prepare("UPDATE jeu SET actif = ? WHERE id_jeu = ?");
        $req->execute([$actif, $id]);
    }

    public function changerEtat($id, $etat) {
        $req = $this->pdo->prepare("UPDATE jeu SET etat = ? WHERE id_jeu = ?");
        $req->execute([$etat, $id]);
    }

    // Statistiques pour le tableau de bord
    public function compterParEtat() {
        return $this->pdo->query("SELECT etat, COUNT(*) AS nb FROM jeu WHERE actif = 1 GROUP BY etat")->fetchAll();
    }

    public function compterTotal() {
        return $this->pdo->query("SELECT COUNT(*) FROM jeu WHERE actif = 1")->fetchColumn();
    }
}
