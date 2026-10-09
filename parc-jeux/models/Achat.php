<?php
// Modèle Achat : vente de billets (table achat + création des billets)
class Achat {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function tous($f, $idClient = null) {
        $sql = "SELECT a.*, c.nom, c.prenom, (SELECT COUNT(*) FROM billet b WHERE b.id_achat = a.id_achat) AS nb_billets
                FROM achat a JOIN client c ON c.id_client = a.id_client WHERE 1=1";
        $p = [];
        if ($f['date_debut'] !== '') { $sql .= " AND DATE(a.date_achat) >= ?"; $p[] = $f['date_debut']; }
        if ($f['date_fin'] !== '')   { $sql .= " AND DATE(a.date_achat) <= ?"; $p[] = $f['date_fin']; }
        if ($f['statut'] !== '')     { $sql .= " AND a.statut_paiement = ?";   $p[] = $f['statut']; }
        if ($f['q'] !== '')          { $sql .= " AND (c.nom LIKE ? OR c.prenom LIKE ? OR a.numero_commande LIKE ?)"; $p[] = '%' . $f['q'] . '%'; $p[] = '%' . $f['q'] . '%'; $p[] = '%' . $f['q'] . '%'; }
        if ($idClient)               { $sql .= " AND a.id_client = ?"; $p[] = $idClient; }
        $sql .= " ORDER BY a.date_achat DESC";
        $req = $this->pdo->prepare($sql);
        $req->execute($p);
        return $req->fetchAll();
    }

    public function trouver($id) {
        $req = $this->pdo->prepare("SELECT a.*, c.nom, c.prenom FROM achat a JOIN client c ON c.id_client = a.id_client WHERE a.id_achat = ?");
        $req->execute([$id]);
        return $req->fetch();
    }

    // Enregistre une vente. $quantites = [id_type_billet => quantité]
    // Le montant total est calculé ICI à partir des tarifs de la base (pas depuis le formulaire).
    // Retourne ['erreur' => '...'] ou ['id' => numéro de l'achat].
    public function creer($idClient, $mode, $statutPaiement, $quantites) {
        $this->pdo->beginTransaction();
        try {
            $lignes = [];
            $total = 0;
            foreach ($quantites as $idType => $qte) {
                $qte = (int)$qte;
                if ($qte <= 0) continue;
                if ($qte > 50) throw new Exception("Maximum 50 billets par type.");
                $req = $this->pdo->prepare("SELECT * FROM type_billet WHERE id_type_billet = ?");
                $req->execute([(int)$idType]);
                $type = $req->fetch();
                if (!$type) continue;
                $lignes[] = ['type' => $type, 'qte' => $qte];
                $total += $type['tarif'] * $qte;
            }
            if (count($lignes) === 0) throw new Exception("Choisissez au moins un billet.");

            $req = $this->pdo->prepare("INSERT INTO achat (numero_commande, date_achat, montant_total, mode_paiement, statut_paiement, id_client)
                                        VALUES (?, NOW(), ?, ?, ?, ?)");
            $req->execute(['TMP' . uniqid(), $total, $mode, $statutPaiement, $idClient]);
            $idAchat = $this->pdo->lastInsertId();

            $numero = 'CMD-' . date('Y') . '-' . str_pad($idAchat, 5, '0', STR_PAD_LEFT);
            $req = $this->pdo->prepare("UPDATE achat SET numero_commande = ? WHERE id_achat = ?");
            $req->execute([$numero, $idAchat]);

            // Une ligne "billet" par billet vendu, avec un code unique
            $req = $this->pdo->prepare("INSERT INTO billet (code_unique, prix, id_achat, id_type_billet) VALUES (?, ?, ?, ?)");
            foreach ($lignes as $ligne) {
                for ($i = 0; $i < $ligne['qte']; $i++) {
                    $code = 'BIL-' . strtoupper(bin2hex(random_bytes(4)));
                    $req->execute([$code, $ligne['type']['tarif'], $idAchat, $ligne['type']['id_type_billet']]);
                }
            }

            if ($statutPaiement === 'paye') {
                (new Recette($this->pdo))->creerPourAchat($idAchat);
            }
            $this->pdo->commit();
            return ['id' => $idAchat];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['erreur' => $e->getMessage()];
        }
    }

    // Validation du paiement d'une commande "en attente"
    public function payer($id, $mode) {
        $this->pdo->beginTransaction();
        $req = $this->pdo->prepare("UPDATE achat SET statut_paiement = 'paye', mode_paiement = ? WHERE id_achat = ? AND statut_paiement = 'en_attente'");
        $req->execute([$mode, $id]);
        if ($req->rowCount() === 1) {
            (new Recette($this->pdo))->creerPourAchat($id);
        }
        $this->pdo->commit();
        return $req->rowCount() === 1;
    }

    // Billets vendus sur une période (commandes payées)
    public function compterBillets($debut, $fin) {
        $req = $this->pdo->prepare("SELECT COUNT(*) FROM billet b JOIN achat a ON a.id_achat = b.id_achat
                                    WHERE a.statut_paiement = 'paye' AND DATE(a.date_achat) BETWEEN ? AND ?");
        $req->execute([$debut, $fin]);
        return $req->fetchColumn();
    }
}
