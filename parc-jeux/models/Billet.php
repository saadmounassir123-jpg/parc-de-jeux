<?php
// Modèle Billet : types de billets (tarifs) et billets vendus
class Billet {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    // ----- Types de billets -----
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

    // ----- Billets vendus -----
    public function parAchat($idAchat) {
        $req = $this->pdo->prepare("SELECT b.*, t.nom AS type_nom FROM billet b
                                    JOIN type_billet t ON t.id_type_billet = b.id_type_billet
                                    WHERE b.id_achat = ? ORDER BY b.id_billet");
        $req->execute([$idAchat]);
        return $req->fetchAll();
    }

    public function parCode($code) {
        $req = $this->pdo->prepare("SELECT b.*, t.nom AS type_nom, t.duree_validite, a.date_achat, a.statut_paiement, a.numero_commande,
                                           c.nom AS client_nom, c.prenom AS client_prenom
                                    FROM billet b
                                    JOIN type_billet t ON t.id_type_billet = b.id_type_billet
                                    JOIN achat a ON a.id_achat = b.id_achat
                                    JOIN client c ON c.id_client = a.id_client
                                    WHERE b.code_unique = ?");
        $req->execute([$code]);
        return $req->fetch();
    }

    // Un billet ne peut être utilisé qu'une seule fois
    public function utiliser($idBillet) {
        $req = $this->pdo->prepare("UPDATE billet SET statut = 'utilise', date_utilisation = NOW() WHERE id_billet = ? AND statut = 'valide'");
        $req->execute([$idBillet]);
        return $req->rowCount() === 1;
    }

    // Un billet est valide s'il est payé, non utilisé et dans sa durée de validité
    public function verifierValidite($billet) {
        if ($billet['statut_paiement'] !== 'paye') return "Le paiement de cette commande n'est pas validé.";
        if ($billet['statut'] === 'utilise')       return "Ce billet a déjà été utilisé le " . dateFr($billet['date_utilisation']) . ".";
        $limite = date('Y-m-d', strtotime($billet['date_achat'] . ' +' . ($billet['duree_validite'] - 1) . ' days'));
        if (date('Y-m-d') > $limite)               return "Ce billet est expiré (valable jusqu'au " . dateFr($limite) . ").";
        return '';   // chaîne vide = billet valide
    }
}
