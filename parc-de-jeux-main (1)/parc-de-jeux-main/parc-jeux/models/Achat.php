<?php
// Modèle Achat : gestion des achats de billets en ligne, paniers et facturation
class Achat {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function tous($filtres = [], $idClient = null) {
        $sql = "SELECT a.*, c.nom, c.prenom, c.email, c.telephone,
                (SELECT SUM(quantite) FROM achat_ligne WHERE id_achat = a.id_achat) AS total_billets,
                (SELECT GROUP_CONCAT(CONCAT(tb.nom, ' x', al.quantite) SEPARATOR ', ')
                 FROM achat_ligne al JOIN type_billet tb ON tb.id_type_billet = al.id_type_billet
                 WHERE al.id_achat = a.id_achat) AS details_billets
                FROM achat a 
                JOIN client c ON c.id_client = a.id_client 
                WHERE 1=1";
        $p = [];
        if (!empty($filtres['date_debut'])) { $sql .= " AND DATE(a.date_achat) >= ?"; $p[] = $filtres['date_debut']; }
        if (!empty($filtres['date_fin']))   { $sql .= " AND DATE(a.date_achat) <= ?"; $p[] = $filtres['date_fin']; }
        if (!empty($filtres['statut']))     { $sql .= " AND a.statut = ?";            $p[] = $filtres['statut']; }
        if (!empty($filtres['id_client']))  { $sql .= " AND a.id_client = ?";         $p[] = (int)$filtres['id_client']; }
        if ($idClient)                      { $sql .= " AND a.id_client = ?";         $p[] = (int)$idClient; }

        $sql .= " ORDER BY a.date_achat DESC, a.id_achat DESC";
        $req = $this->pdo->prepare($sql);
        $req->execute($p);
        return $req->fetchAll();
    }

    public function trouver($id) {
        $req = $this->pdo->prepare("SELECT * FROM achat WHERE id_achat = ?");
        $req->execute([(int)$id]);
        return $req->fetch();
    }

    // Récupère la commande complète avec ses lignes de billets et les infos du client
    public function trouverComplet($id) {
        $req = $this->pdo->prepare("SELECT a.*, c.nom, c.prenom, c.email, c.telephone, c.type_client
                                    FROM achat a
                                    JOIN client c ON c.id_client = a.id_client
                                    WHERE a.id_achat = ?");
        $req->execute([(int)$id]);
        $achat = $req->fetch();
        if (!$achat) return null;

        $reqLignes = $this->pdo->prepare("SELECT al.*, tb.nom AS type_nom, tb.duree_validite
                                          FROM achat_ligne al
                                          JOIN type_billet tb ON tb.id_type_billet = al.id_type_billet
                                          WHERE al.id_achat = ?");
        $reqLignes->execute([(int)$id]);
        $achat['lignes'] = $reqLignes->fetchAll();

        return $achat;
    }

    public function creer($idClient, $dateVisite, $lignes, $codePromo = '') {
        $erreurs = [];

        if (!$idClient) {
            $erreurs[] = "Compte client obligatoire pour enregistrer un achat de billets.";
        } else {
            $clientExistant = (new Client($this->pdo))->trouver((int)$idClient);
            if (!$clientExistant) {
                $erreurs[] = "Client introuvable ou invalide dans le système.";
            }
        }

        if ($dateVisite < date('Y-m-d')) {
            $erreurs[] = "La date de visite ne peut pas être dans le passé.";
        }
        if (empty($lignes)) {
            $erreurs[] = "Veuillez choisir au moins un billet.";
        }


        $billetModel = new Billet($this->pdo);
        $typesDispos = [];
        foreach ($billetModel->types() as $t) {
            $typesDispos[$t['id_type_billet']] = $t;
        }

        $lignesValides = [];
        $montantBrut = 0;
        foreach ($lignes as $l) {
            $idType = (int)($l['id_type_billet'] ?? 0);
            $qte = (int)($l['quantite'] ?? 0);
            if ($qte <= 0) continue;
            if (!isset($typesDispos[$idType])) {
                $erreurs[] = "Type de billet invalide sélectionné.";
                break;
            }
            $tarif = (float)$typesDispos[$idType]['tarif'];
            $totalLigne = $tarif * $qte;
            $montantBrut += $totalLigne;
            $lignesValides[] = [
                'id_type_billet' => $idType,
                'quantite' => $qte,
                'tarif_unitaire' => $tarif,
                'total_ligne' => $totalLigne,
            ];
        }

        if (empty($lignesValides) && empty($erreurs)) {
            $erreurs[] = "Veuillez sélectionner au moins un billet avec une quantité supérieure à 0.";
        }

        if ($erreurs) {
            return ['erreurs' => $erreurs];
        }

        // Gestion du code promo
        $remise = 0;
        $codePromoValide = null;
        if (!empty($codePromo)) {
            $promoModel = new Promotion($this->pdo);
            $verif = $promoModel->valider($codePromo, $montantBrut);
            if ($verif['valide']) {
                $remise = $verif['remise'];
                $codePromoValide = strtoupper(trim($codePromo));
            } else {
                $erreurs[] = $verif['message'];
                return ['erreurs' => $erreurs];
            }
        }

        $montantTotal = max(0, $montantBrut - $remise);

        $this->pdo->beginTransaction();
        try {
            $req = $this->pdo->prepare("INSERT INTO achat (numero_achat, date_achat, date_visite, montant_brut, remise, montant_total, code_promo, statut, id_client)
                                        VALUES (?, NOW(), ?, ?, ?, ?, ?, 'en_attente', ?)");
            $req->execute(['TMP' . uniqid(), $dateVisite, $montantBrut, $remise, $montantTotal, $codePromoValide, (int)$idClient]);
            $idAchat = (int)$this->pdo->lastInsertId();

            $numeroAchat = 'ACH-' . date('Y') . '-' . str_pad($idAchat, 5, '0', STR_PAD_LEFT);
            $reqNum = $this->pdo->prepare("UPDATE achat SET numero_achat = ? WHERE id_achat = ?");
            $reqNum->execute([$numeroAchat, $idAchat]);

            $reqLigne = $this->pdo->prepare("INSERT INTO achat_ligne (id_achat, id_type_billet, quantite, tarif_unitaire, total_ligne)
                                             VALUES (?, ?, ?, ?, ?)");
            foreach ($lignesValides as $ligne) {
                $reqLigne->execute([
                    $idAchat,
                    $ligne['id_type_billet'],
                    $ligne['quantite'],
                    $ligne['tarif_unitaire'],
                    $ligne['total_ligne']
                ]);
            }

            $this->pdo->commit();
            return ['id' => $idAchat, 'numero' => $numeroAchat, 'montant_total' => $montantTotal];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // Valide le paiement d'un achat et crée la recette
    public function enregistrerPaiement($idAchat, $modePaiement, $reference) {
        $achat = $this->trouver($idAchat);
        if (!$achat) return "Achat introuvable.";
        if ($achat['statut'] === 'paye') return "Cet achat a déjà été réglé.";
        if ($achat['statut'] === 'annule') return "Impossible de régler un achat annulé.";

        $this->pdo->beginTransaction();
        try {
            $req = $this->pdo->prepare("UPDATE achat SET statut = 'paye', mode_paiement = ?, reference_paiement = ? WHERE id_achat = ?");
            $req->execute([$modePaiement, $reference, (int)$idAchat]);

            // Enregistrement de la recette
            $recette = new Recette($this->pdo);
            $recette->creerPourAchat($idAchat, $achat['montant_total'], $modePaiement, $reference);

            // Incrémentation du compteur de promotion si code promo utilisé
            if (!empty($achat['code_promo'])) {
                $promo = (new Promotion($this->pdo))->trouverParCode($achat['code_promo']);
                if ($promo) {
                    (new Promotion($this->pdo))->incrementer($promo['id_promotion']);
                }
            }

            $this->pdo->commit();
            return '';
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function annuler($idAchat) {
        $achat = $this->trouver($idAchat);
        if (!$achat) return "Achat introuvable.";
        if ($achat['statut'] === 'annule') return "Cet achat est déjà annulé.";

        $this->pdo->beginTransaction();
        try {
            $req = $this->pdo->prepare("UPDATE achat SET statut = 'annule' WHERE id_achat = ?");
            $req->execute([(int)$idAchat]);

            if ($achat['statut'] === 'paye') {
                $recette = new Recette($this->pdo);
                $recette->supprimerPourAchat($idAchat);
            }

            $this->pdo->commit();
            return '';
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function compter($debut, $fin) {
        $req = $this->pdo->prepare("SELECT COUNT(*) FROM achat WHERE statut <> 'annule' AND DATE(date_achat) BETWEEN ? AND ?");
        $req->execute([$debut, $fin]);
        return (int)$req->fetchColumn();
    }

    public function totalVentes($debut, $fin) {
        $req = $this->pdo->prepare("SELECT COALESCE(SUM(montant_total), 0) FROM achat WHERE statut = 'paye' AND DATE(date_achat) BETWEEN ? AND ?");
        $req->execute([$debut, $fin]);
        return (float)$req->fetchColumn();
    }
}
