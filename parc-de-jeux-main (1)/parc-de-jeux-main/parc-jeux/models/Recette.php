<?php
// Modèle Recette : les recettes sont créées automatiquement quand un paiement est validé
class Recette {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }


    public function creerPourReservation($idReservation, $montant, $modePaiement = 'carte', $reference = null) {
        $req = $this->pdo->prepare("INSERT INTO recette (date_recette, montant, source, id_reservation, mode_paiement, reference_paiement) VALUES (NOW(), ?, 'reservation', ?, ?, ?)");
        $req->execute([$montant, $idReservation, $modePaiement, $reference]);
    }

    public function supprimerPourReservation($idReservation) {
        $req = $this->pdo->prepare("DELETE FROM recette WHERE id_reservation = ?");
        $req->execute([$idReservation]);
    }

    public function creerPourAchat($idAchat, $montant, $modePaiement = 'carte', $reference = null) {
        $req = $this->pdo->prepare("INSERT INTO recette (date_recette, montant, source, id_achat, mode_paiement, reference_paiement) VALUES (NOW(), ?, 'billet', ?, ?, ?)");
        $req->execute([$montant, $idAchat, $modePaiement, $reference]);
    }

    public function supprimerPourAchat($idAchat) {
        $req = $this->pdo->prepare("DELETE FROM recette WHERE id_achat = ?");
        $req->execute([$idAchat]);
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
