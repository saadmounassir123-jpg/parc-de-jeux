<?php
// Modèle Reservation : réservations de jeux + contrôles (disponibilité, capacité, doublons)
class Reservation {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    // Règles de changement de statut (statut actuel => statuts possibles)
    const TRANSITIONS = [
        'en_attente' => ['confirmee', 'annulee'],
        'confirmee'  => ['payee', 'annulee'],
        'payee'      => ['utilisee', 'annulee'],
        'utilisee'   => [],
        'annulee'    => [],
    ];

    public function tous($f, $idClient = null, $idResponsable = null) {
        $sql = "SELECT r.*, c.nom, c.prenom,
                (SELECT GROUP_CONCAT(j.nom SEPARATOR ', ') FROM reservation_jeu rj JOIN jeu j ON j.id_jeu = rj.id_jeu
                 WHERE rj.id_reservation = r.id_reservation) AS jeux
                FROM reservation r JOIN client c ON c.id_client = r.id_client WHERE 1=1";
        $p = [];
        if ($f['date_debut'] !== '') { $sql .= " AND r.date_visite >= ?"; $p[] = $f['date_debut']; }
        if ($f['date_fin'] !== '')   { $sql .= " AND r.date_visite <= ?"; $p[] = $f['date_fin']; }
        if ($f['statut'] !== '')     { $sql .= " AND r.statut = ?";       $p[] = $f['statut']; }
        if ($f['id_client'] !== '')  { $sql .= " AND r.id_client = ?";    $p[] = $f['id_client']; }
        if ($f['id_jeu'] !== '') {
            $sql .= " AND r.id_reservation IN (SELECT id_reservation FROM reservation_jeu WHERE id_jeu = ?)";
            $p[] = $f['id_jeu'];
        }
        if ($f['id_responsable'] !== '') {
            $sql .= " AND r.id_reservation IN (SELECT rj.id_reservation FROM reservation_jeu rj JOIN jeu j ON j.id_jeu = rj.id_jeu WHERE j.id_responsable = ?)";
            $p[] = $f['id_responsable'];
        }
        if ($idClient) { $sql .= " AND r.id_client = ?"; $p[] = $idClient; }
        if ($idResponsable) {   // un responsable ne voit que les réservations de ses jeux
            $sql .= " AND r.id_reservation IN (SELECT rj.id_reservation FROM reservation_jeu rj JOIN jeu j ON j.id_jeu = rj.id_jeu WHERE j.id_responsable = ?)";
            $p[] = $idResponsable;
        }
        $sql .= " ORDER BY r.date_visite DESC, r.id_reservation DESC";
        $req = $this->pdo->prepare($sql);
        $req->execute($p);
        return $req->fetchAll();
    }

    public function trouver($id) {
        $req = $this->pdo->prepare("SELECT * FROM reservation WHERE id_reservation = ?");
        $req->execute([$id]);
        return $req->fetch();
    }

    // Réservation + nom du client + liste des jeux (page de confirmation)
    public function trouverComplet($id) {
        $req = $this->pdo->prepare("SELECT r.*, c.nom, c.prenom,
                (SELECT GROUP_CONCAT(j.nom SEPARATOR ', ') FROM reservation_jeu rj JOIN jeu j ON j.id_jeu = rj.id_jeu
                 WHERE rj.id_reservation = r.id_reservation) AS jeux
                FROM reservation r JOIN client c ON c.id_client = r.id_client
                WHERE r.id_reservation = ?");
        $req->execute([$id]);
        return $req->fetch();
    }

    // Réservations à venir d'un jeu (planning)
    public function parJeu($idJeu) {
        $req = $this->pdo->prepare("SELECT r.*, c.nom, c.prenom FROM reservation r
                                    JOIN reservation_jeu rj ON rj.id_reservation = r.id_reservation
                                    JOIN client c ON c.id_client = r.id_client
                                    WHERE rj.id_jeu = ? AND r.statut <> 'annulee' AND r.date_visite >= CURDATE()
                                    ORDER BY r.date_visite");
        $req->execute([$idJeu]);
        return $req->fetchAll();
    }

    // Nombre de personnes déjà réservées pour un jeu à une date
    public function placesPrises($idJeu, $date) {
        $req = $this->pdo->prepare("SELECT COALESCE(SUM(r.nombre_personnes), 0) FROM reservation r
                                    JOIN reservation_jeu rj ON rj.id_reservation = r.id_reservation
                                    WHERE rj.id_jeu = ? AND r.date_visite = ? AND r.statut <> 'annulee'");
        $req->execute([$idJeu, $date]);
        return (int)$req->fetchColumn();
    }

    // Le même client a-t-il déjà une réservation active pour ce jeu à cette date ?
    public function existeDoublon($idClient, $idJeu, $date) {
        $req = $this->pdo->prepare("SELECT COUNT(*) FROM reservation r
                                    JOIN reservation_jeu rj ON rj.id_reservation = r.id_reservation
                                    WHERE r.id_client = ? AND rj.id_jeu = ? AND r.date_visite = ? AND r.statut <> 'annulee'");
        $req->execute([$idClient, $idJeu, $date]);
        return $req->fetchColumn() > 0;
    }

    // Contrôles de disponibilité / capacité / doublon (sans créer la réservation).
    // $idClient peut être null pour un visiteur non encore connecté (le doublon est alors ignoré).
    // $verrouiller = true : FOR UPDATE, à utiliser à l'intérieur d'une transaction.
    // Retourne ['erreurs' => [...], 'montant' => ...]
    public function verifier($idClient, $dateVisite, $nbPersonnes, $idsJeux, $verrouiller = false) {
        $erreurs = [];
        $montant = 0;
        if ($nbPersonnes < 1)                        $erreurs[] = "Le nombre de personnes doit être au moins 1.";
        if ($dateVisite < date('Y-m-d'))             $erreurs[] = "La date de visite ne peut pas être dans le passé.";
        if (!is_array($idsJeux) || count($idsJeux) === 0) $erreurs[] = "Choisissez au moins un jeu.";
        if ($erreurs) return ['erreurs' => $erreurs, 'montant' => 0];

        foreach ($idsJeux as $idJeu) {
            if ($verrouiller) {
                $req = $this->pdo->prepare("SELECT * FROM jeu WHERE id_jeu = ? FOR UPDATE");
            } else {
                $req = $this->pdo->prepare("SELECT * FROM jeu WHERE id_jeu = ?");
            }
            $req->execute([(int)$idJeu]);
            $jeu = $req->fetch();

            if (!$jeu || !$jeu['actif']) {
                $erreurs[] = "Jeu introuvable ou désactivé.";
            } elseif (!in_array($jeu['etat'], ['disponible', 'en_fonctionnement'])) {
                $erreurs[] = $jeu['nom'] . " n'est pas disponible actuellement (" . libelle($jeu['etat']) . ").";
            } else {
                $restant = $jeu['capacite'] - $this->placesPrises($jeu['id_jeu'], $dateVisite);
                if ($nbPersonnes > $restant) {
                    $erreurs[] = "Capacité dépassée pour " . $jeu['nom'] . " : il reste " . max($restant, 0) . " place(s) le " . dateFr($dateVisite) . ".";
                }
                if ($idClient && $this->existeDoublon($idClient, $jeu['id_jeu'], $dateVisite)) {
                    $erreurs[] = "Ce client a déjà une réservation pour " . $jeu['nom'] . " le " . dateFr($dateVisite) . ".";
                }
                $montant += $jeu['tarif'] * $nbPersonnes;
            }
        }
        return ['erreurs' => $erreurs, 'montant' => $montant];
    }

    // Crée une réservation après les contrôles.
    // Retourne ['erreurs' => [...]] ou ['id' => ...]
    public function creer($idClient, $dateVisite, $nbPersonnes, $idsJeux, $statut) {
        $this->pdo->beginTransaction();
        try {
            $ctrl = $this->verifier($idClient, $dateVisite, $nbPersonnes, $idsJeux, true);
            $erreurs = $ctrl['erreurs'];
            $montant = $ctrl['montant'];
            if ($erreurs) {
                $this->pdo->rollBack();
                return ['erreurs' => $erreurs];
            }

            $req = $this->pdo->prepare("INSERT INTO reservation (numero_reservation, date_creation, date_visite, nombre_personnes, montant, statut, id_client)
                                        VALUES (?, NOW(), ?, ?, ?, ?, ?)");
            $req->execute(['TMP' . uniqid(), $dateVisite, $nbPersonnes, $montant, $statut, $idClient]);
            $id = $this->pdo->lastInsertId();

            $req = $this->pdo->prepare("UPDATE reservation SET numero_reservation = ? WHERE id_reservation = ?");
            $req->execute(['RES-' . date('Y') . '-' . str_pad($id, 5, '0', STR_PAD_LEFT), $id]);

            $req = $this->pdo->prepare("INSERT INTO reservation_jeu (id_reservation, id_jeu) VALUES (?, ?)");
            foreach ($idsJeux as $idJeu) {
                $req->execute([$id, (int)$idJeu]);
            }
            $this->pdo->commit();
            return ['id' => $id];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // Change le statut en respectant les règles. Retourne un message d'erreur ('' si tout va bien).
    // Règles d'annulation : possible seulement si en attente / confirmée / payée ET avant la date de visite.
    // Une réservation payée annulée est remboursée : sa recette est supprimée.
    public function changerStatut($id, $nouveau, $mode = '') {
        $resa = $this->trouver($id);
        if (!$resa) return "Réservation introuvable.";
        if (!in_array($nouveau, self::TRANSITIONS[$resa['statut']])) {
            return "Changement impossible : une réservation « " . libelle($resa['statut']) . " » ne peut pas passer à « " . libelle($nouveau) . " ».";
        }
        if ($nouveau === 'annulee' && $resa['date_visite'] < date('Y-m-d')) {
            return "Annulation impossible : la date de visite est dépassée.";
        }
        if ($nouveau === 'payee' && !in_array($mode, ['especes', 'carte'])) {
            return "Choisissez un mode de paiement.";
        }

        $this->pdo->beginTransaction();
        $recette = new Recette($this->pdo);
        if ($nouveau === 'payee') {
            $req = $this->pdo->prepare("UPDATE reservation SET statut = 'payee', mode_paiement = ? WHERE id_reservation = ?");
            $req->execute([$mode, $id]);
            $recette->creerPourReservation($id, $resa['montant']);
        } else {
            $req = $this->pdo->prepare("UPDATE reservation SET statut = ? WHERE id_reservation = ?");
            $req->execute([$nouveau, $id]);
            if ($nouveau === 'annulee' && $resa['statut'] === 'payee') {
                $recette->supprimerPourReservation($id);
            }
        }
        $this->pdo->commit();
        return '';
    }

    public function compter($debut, $fin) {
        $req = $this->pdo->prepare("SELECT COUNT(*) FROM reservation WHERE statut <> 'annulee' AND DATE(date_creation) BETWEEN ? AND ?");
        $req->execute([$debut, $fin]);
        return $req->fetchColumn();
    }
}
