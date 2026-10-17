<?php
// Modèle Maintenance : interventions et pannes signalées
class Maintenance {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function tous($f, $idResponsable = null) {
        $sql = "SELECT m.*, j.nom AS jeu_nom, r.nom AS resp_nom, r.prenom AS resp_prenom
                FROM maintenance m JOIN jeu j ON j.id_jeu = m.id_jeu
                LEFT JOIN responsable r ON r.id_responsable = m.id_responsable WHERE 1=1";
        $p = [];
        if ($f['id_jeu'] !== '') { $sql .= " AND m.id_jeu = ?"; $p[] = $f['id_jeu']; }
        if ($f['etat'] !== '')   { $sql .= " AND m.etat = ?";   $p[] = $f['etat']; }
        if ($idResponsable)      { $sql .= " AND j.id_responsable = ?"; $p[] = $idResponsable; }
        $sql .= " ORDER BY m.date_intervention DESC, m.id_maintenance DESC";
        $req = $this->pdo->prepare($sql);
        $req->execute($p);
        return $req->fetchAll();
    }

    public function parJeu($idJeu) {
        $req = $this->pdo->prepare("SELECT * FROM maintenance WHERE id_jeu = ? ORDER BY date_intervention DESC");
        $req->execute([$idJeu]);
        return $req->fetchAll();
    }

    public function trouver($id) {
        $req = $this->pdo->prepare("SELECT * FROM maintenance WHERE id_maintenance = ?");
        $req->execute([$id]);
        return $req->fetch();
    }

    public function ajouter($d) {
        $req = $this->pdo->prepare("INSERT INTO maintenance (type_intervention, description, date_intervention, cout, etat, date_prochaine, id_jeu, id_responsable)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $req->execute([$d['type_intervention'], $d['description'], $d['date_intervention'], $d['cout'], $d['etat'],
                       $d['date_prochaine'], $d['id_jeu'], $d['id_responsable']]);
    }

    public function modifier($id, $d) {
        $req = $this->pdo->prepare("UPDATE maintenance SET type_intervention=?, description=?, date_intervention=?, cout=?, etat=?,
                                    date_prochaine=?, id_jeu=?, id_responsable=? WHERE id_maintenance=?");
        $req->execute([$d['type_intervention'], $d['description'], $d['date_intervention'], $d['cout'], $d['etat'],
                       $d['date_prochaine'], $d['id_jeu'], $d['id_responsable'], $id]);
    }
}
