<?php
// Modèle Recette : les recettes sont créées automatiquement quand un paiement est validé
class Recette {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }


    public function creerPourReservation($idReservation, $montant) {
        $req = $this->pdo->prepare("INSERT INTO recette (date_recette, montant, source, id_reservation) VALUES (NOW(), ?, 'reservation', ?)");
        $req->execute([$montant, $idReservation]);
    }

    public function supprimerPourReservation($idReservation) {
        $req = $this->pdo->prepare("DELETE FROM recette WHERE id_reservation = ?");
        $req->execute([$idReservation]);
    }

    public function total($debut, $fin) {
        $req = $this->pdo->prepare("SELECT COALESCE(SUM(montant), 0) FROM recette WHERE DATE(date_recette) BETWEEN ? AND ?");
        $req->execute([$debut, $fin]);
        return $req->fetchColumn();
    }

    public function parSource($debut, $fin) {
        $req = $this->pdo->prepare("SELECT source, SUM(montant) AS total FROM recette WHERE DATE(date_recette) BETWEEN ? AND ? GROUP BY source");
        $req->execute([$debut, $fin]);
        return $req->fetchAll();
    }

    public function parMois() {
        return $this->pdo->query("SELECT DATE_FORMAT(date_recette, '%Y-%m') AS mois, SUM(montant) AS total FROM recette GROUP BY mois")->fetchAll();
    }
}
