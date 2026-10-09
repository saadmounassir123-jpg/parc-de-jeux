<?php
// Modèle Recette : les recettes sont créées automatiquement quand un paiement est validé
class Recette {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function creerPourAchat($idAchat) {
        $req = $this->pdo->prepare("SELECT COUNT(*) FROM recette WHERE id_achat = ?");
        $req->execute([$idAchat]);
        if ($req->fetchColumn() > 0) return;   // éviter un doublon

        $req = $this->pdo->prepare("SELECT montant_total FROM achat WHERE id_achat = ?");
        $req->execute([$idAchat]);
        $montant = $req->fetchColumn();

        // Si la commande contient un abonnement, la source est "abonnement"
        $req = $this->pdo->prepare("SELECT COUNT(*) FROM billet b JOIN type_billet t ON t.id_type_billet = b.id_type_billet
                                    WHERE b.id_achat = ? AND t.nom = 'Abonnement'");
        $req->execute([$idAchat]);
        $source = ($req->fetchColumn() > 0) ? 'abonnement' : 'billet';

        $req = $this->pdo->prepare("INSERT INTO recette (date_recette, montant, source, id_achat) VALUES (NOW(), ?, ?, ?)");
        $req->execute([$montant, $source, $idAchat]);
    }

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
