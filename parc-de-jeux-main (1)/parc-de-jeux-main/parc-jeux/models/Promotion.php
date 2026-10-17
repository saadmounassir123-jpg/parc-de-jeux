<?php
// Modèle Promotion : gestion des codes promotionnels et des réductions
class Promotion {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function tous($filtres = []) {
        $sql = "SELECT * FROM promotion WHERE 1=1";
        $p = [];
        if (!empty($filtres['recherche'])) {
            $sql .= " AND (code LIKE ? OR titre LIKE ? OR description LIKE ?)";
            $recherche = '%' . trim($filtres['recherche']) . '%';
            $p[] = $recherche;
            $p[] = $recherche;
            $p[] = $recherche;
        }
        if (isset($filtres['actif']) && $filtres['actif'] !== '') {
            $sql .= " AND actif = ?";
            $p[] = (int)$filtres['actif'];
        }
        $sql .= " ORDER BY actif DESC, id_promotion DESC";
        $req = $this->pdo->prepare($sql);
        $req->execute($p);
        return $req->fetchAll();
    }

    // Promotions actuellement actives et valides pour le public
    public function actifs() {
        $sql = "SELECT * FROM promotion 
                WHERE actif = 1 
                  AND (date_debut IS NULL OR date_debut <= CURDATE())
                  AND (date_fin IS NULL OR date_fin >= CURDATE())
                  AND (nb_utilisations_max IS NULL OR nb_utilisations < nb_utilisations_max)
                ORDER BY valeur DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function trouver($id) {
        $req = $this->pdo->prepare("SELECT * FROM promotion WHERE id_promotion = ?");
        $req->execute([(int)$id]);
        return $req->fetch();
    }

    public function trouverParCode($code) {
        $req = $this->pdo->prepare("SELECT * FROM promotion WHERE UPPER(TRIM(code)) = UPPER(TRIM(?))");
        $req->execute([$code]);
        return $req->fetch();
    }

    // Vérifie un code promo pour un montant donné et calcule la remise
    public function valider($code, $montant) {
        $code = trim((string)$code);
        $montant = (float)$montant;

        if ($code === '') {
            return ['valide' => false, 'message' => "Veuillez saisir un code promo.", 'remise' => 0, 'nouveau_montant' => $montant];
        }

        $promo = $this->trouverParCode($code);
        if (!$promo) {
            return ['valide' => false, 'message' => "Code promo « " . e($code) . " » introuvable.", 'remise' => 0, 'nouveau_montant' => $montant];
        }

        if (!$promo['actif']) {
            return ['valide' => false, 'message' => "Ce code promotionnel n'est plus actif.", 'remise' => 0, 'nouveau_montant' => $montant];
        }

        $aujourdhui = date('Y-m-d');
        if (!empty($promo['date_debut']) && $promo['date_debut'] > $aujourdhui) {
            return ['valide' => false, 'message' => "Cette promotion commencera le " . dateFr($promo['date_debut']) . ".", 'remise' => 0, 'nouveau_montant' => $montant];
        }

        if (!empty($promo['date_fin']) && $promo['date_fin'] < $aujourdhui) {
            return ['valide' => false, 'message' => "Cette promotion a expiré le " . dateFr($promo['date_fin']) . ".", 'remise' => 0, 'nouveau_montant' => $montant];
        }

        if ($promo['nb_utilisations_max'] !== null && (int)$promo['nb_utilisations'] >= (int)$promo['nb_utilisations_max']) {
            return ['valide' => false, 'message' => "Le quota d'utilisations de ce code est épuisé.", 'remise' => 0, 'nouveau_montant' => $montant];
        }

        if ($montant < (float)$promo['montant_minimum']) {
            return [
                'valide' => false,
                'message' => "Un montant minimum de " . montant($promo['montant_minimum']) . " est requis pour bénéficier de cette promotion.",
                'remise' => 0,
                'nouveau_montant' => $montant
            ];
        }

        // Calcul de la remise
        $remise = 0;
        if ($promo['type_reduction'] === 'pourcentage') {
            $remise = round($montant * ((float)$promo['valeur'] / 100), 2);
        } else {
            $remise = min((float)$promo['valeur'], $montant);
        }

        $nouveauMontant = max(0, $montant - $remise);

        return [
            'valide' => true,
            'message' => "Promotion « " . $promo['titre'] . " » appliquée avec succès !",
            'remise' => $remise,
            'nouveau_montant' => $nouveauMontant,
            'promotion' => $promo,
        ];
    }

    public function incrementer($id) {
        $req = $this->pdo->prepare("UPDATE promotion SET nb_utilisations = nb_utilisations + 1 WHERE id_promotion = ?");
        $req->execute([(int)$id]);
    }

    public function ajouter($d) {
        $req = $this->pdo->prepare("INSERT INTO promotion (code, titre, description, type_reduction, valeur, montant_minimum, date_debut, date_fin, nb_utilisations_max, actif)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $req->execute([
            strtoupper(trim($d['code'])),
            trim($d['titre']),
            trim($d['description']),
            $d['type_reduction'],
            (float)$d['valeur'],
            (float)($d['montant_minimum'] ?? 0),
            !empty($d['date_debut']) ? $d['date_debut'] : null,
            !empty($d['date_fin']) ? $d['date_fin'] : null,
            ($d['nb_utilisations_max'] !== '' && $d['nb_utilisations_max'] !== null) ? (int)$d['nb_utilisations_max'] : null,
            isset($d['actif']) ? (int)$d['actif'] : 1
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function modifier($id, $d) {
        $req = $this->pdo->prepare("UPDATE promotion SET code=?, titre=?, description=?, type_reduction=?, valeur=?, montant_minimum=?, date_debut=?, date_fin=?, nb_utilisations_max=?, actif=?
                                    WHERE id_promotion = ?");
        $req->execute([
            strtoupper(trim($d['code'])),
            trim($d['titre']),
            trim($d['description']),
            $d['type_reduction'],
            (float)$d['valeur'],
            (float)($d['montant_minimum'] ?? 0),
            !empty($d['date_debut']) ? $d['date_debut'] : null,
            !empty($d['date_fin']) ? $d['date_fin'] : null,
            ($d['nb_utilisations_max'] !== '' && $d['nb_utilisations_max'] !== null) ? (int)$d['nb_utilisations_max'] : null,
            isset($d['actif']) ? (int)$d['actif'] : 1,
            (int)$id
        ]);
    }

    public function basculerActif($id) {
        $req = $this->pdo->prepare("UPDATE promotion SET actif = 1 - actif WHERE id_promotion = ?");
        $req->execute([(int)$id]);
    }

    public function supprimer($id) {
        $req = $this->pdo->prepare("DELETE FROM promotion WHERE id_promotion = ?");
        $req->execute([(int)$id]);
    }
}
