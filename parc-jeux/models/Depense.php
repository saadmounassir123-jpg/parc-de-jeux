<?php
// Modèle Depense : dépenses liées à un jeu ou frais généraux
class Depense {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function tous($f, $idResponsable = null) {
        $sql = "SELECT d.*, j.nom AS jeu_nom, u.nom AS user_nom, u.prenom AS user_prenom
                FROM depense d LEFT JOIN jeu j ON j.id_jeu = d.id_jeu
                JOIN utilisateur u ON u.id_utilisateur = d.id_utilisateur WHERE 1=1";
        $p = [];
        if ($f['date_debut'] !== '') { $sql .= " AND d.date_depense >= ?"; $p[] = $f['date_debut']; }
        if ($f['date_fin'] !== '')   { $sql .= " AND d.date_depense <= ?"; $p[] = $f['date_fin']; }
        if ($f['id_jeu'] !== '')     { $sql .= " AND d.id_jeu = ?";        $p[] = $f['id_jeu']; }
        if ($f['categorie'] !== '')  { $sql .= " AND d.categorie = ?";     $p[] = $f['categorie']; }
        if ($f['type'] !== '')       { $sql .= " AND d.type = ?";          $p[] = $f['type']; }
        if ($idResponsable) {   // un responsable ne voit que les dépenses de ses jeux
            $sql .= " AND d.id_jeu IN (SELECT id_jeu FROM jeu WHERE id_responsable = ?)";
            $p[] = $idResponsable;
        }
        $sql .= " ORDER BY d.date_depense DESC, d.id_depense DESC";
        $req = $this->pdo->prepare($sql);
        $req->execute($p);
        return $req->fetchAll();
    }

    public function parJeu($idJeu) {
        $req = $this->pdo->prepare("SELECT * FROM depense WHERE id_jeu = ? ORDER BY date_depense DESC");
        $req->execute([$idJeu]);
        return $req->fetchAll();
    }

    public function ajouter($d) {
        $req = $this->pdo->prepare("INSERT INTO depense (categorie, description, montant, date_depense, type, justificatif, id_jeu, id_utilisateur)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $req->execute([$d['categorie'], $d['description'], $d['montant'], $d['date_depense'], $d['type'],
                       $d['justificatif'], $d['id_jeu'], $d['id_utilisateur']]);
    }

    public function total($debut, $fin) {
        $req = $this->pdo->prepare("SELECT COALESCE(SUM(montant), 0) FROM depense WHERE date_depense BETWEEN ? AND ?");
        $req->execute([$debut, $fin]);
        return $req->fetchColumn();
    }

    public function parMois() {
        return $this->pdo->query("SELECT DATE_FORMAT(date_depense, '%Y-%m') AS mois, SUM(montant) AS total FROM depense GROUP BY mois")->fetchAll();
    }
}
