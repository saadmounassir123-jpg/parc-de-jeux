<?php
// Contrôleur des promotions et codes promotionnels
class PromotionController {
    private $pdo, $promotion;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->promotion = new Promotion($pdo);
    }

    public function actionIndex() {
        if (estPersonnel()) {
            exigerRole(['admin']);
            $filtres = [
                'recherche' => $_GET['recherche'] ?? '',
                'actif' => $_GET['actif'] ?? '',
            ];
            $promotions = $this->promotion->tous($filtres);
            vue('promotions/admin_index', [
                'promotions' => $promotions,
                'filtres' => $filtres,
                'titrePage' => 'Gestion des promotions',
            ]);
        } else {
            // Page publique des offres et promotions
            $promotions = $this->promotion->actifs();
            vue('promotions/public_index', [
                'promotions' => $promotions,
                'titrePage' => 'Offres & Promotions — FunPark',
            ]);
        }
    }

    public function actionAjouter() {
        exigerRole(['admin']);
        $this->formulairePromo(null);
    }

    public function actionModifier() {
        exigerRole(['admin']);
        $id = (int)($_GET['id'] ?? 0);
        $this->formulairePromo($id);
    }

    private function formulairePromo($id) {
        $promo = [
            'code' => '',
            'titre' => '',
            'description' => '',
            'type_reduction' => 'pourcentage',
            'valeur' => '',
            'montant_minimum' => '0',
            'date_debut' => '',
            'date_fin' => '',
            'nb_utilisations_max' => '',
            'actif' => 1,
        ];

        if ($id) {
            $promo = $this->promotion->trouver($id);
            if (!$promo) {
                flash('erreur', 'Promotion introuvable.');
                redirect(url('promotions'));
            }
        }

        $erreurs = [];
        if (estPost()) {
            $promo = champsPost(['code', 'titre', 'description', 'type_reduction', 'valeur', 'montant_minimum', 'date_debut', 'date_fin', 'nb_utilisations_max']);
            $promo['actif'] = isset($_POST['actif']) ? 1 : 0;

            if ($promo['code'] === '') $erreurs[] = "Le code promo est obligatoire.";
            if ($promo['titre'] === '') $erreurs[] = "Le titre de la promotion est obligatoire.";
            if (!in_array($promo['type_reduction'], ['pourcentage', 'montant_fixe'], true)) $erreurs[] = "Type de réduction invalide.";
            if (!is_numeric($promo['valeur']) || (float)$promo['valeur'] <= 0) $erreurs[] = "La valeur de réduction doit être un nombre positif.";
            if ($promo['type_reduction'] === 'pourcentage' && (float)$promo['valeur'] > 100) $erreurs[] = "Un pourcentage ne peut pas dépasser 100%.";
            if (!is_numeric($promo['montant_minimum']) || (float)$promo['montant_minimum'] < 0) $erreurs[] = "Le montant minimum doit être positif ou nul.";

            // Vérification unicité code
            $existant = $this->promotion->trouverParCode($promo['code']);
            if ($existant && (!$id || (int)$existant['id_promotion'] !== (int)$id)) {
                $erreurs[] = "Le code « " . e(strtoupper($promo['code'])) . " » est déjà utilisé par une autre promotion.";
            }

            if (!$erreurs) {
                if ($id) {
                    $this->promotion->modifier($id, $promo);
                    flash('succes', "Promotion « " . e($promo['code']) . " » modifiée avec succès.");
                } else {
                    $this->promotion->ajouter($promo);
                    flash('succes', "Nouvelle promotion « " . e(strtoupper($promo['code'])) . " » créée avec succès.");
                }
                redirect(url('promotions'));
            }
        }

        vue('promotions/form', [
            'promo' => $promo,
            'id' => $id,
            'erreurs' => $erreurs,
            'titrePage' => $id ? 'Modifier la promotion' : 'Ajouter une promotion',
        ]);
    }

    public function actionBasculer() {
        exigerRole(['admin']);
        $id = (int)post('id');
        if (estPost() && $id) {
            $this->promotion->basculerActif($id);
            flash('succes', "Statut de la promotion mis à jour.");
        }
        redirect(url('promotions'));
    }

    public function actionSupprimer() {
        exigerRole(['admin']);
        $id = (int)post('id');
        if (estPost() && $id) {
            $this->promotion->supprimer($id);
            flash('succes', "Promotion supprimée.");
        }
        redirect(url('promotions'));
    }

    // Endpoint AJAX pour tester / valider un code promo en direct
    public function actionVerifier() {
        header('Content-Type: application/json; charset=utf-8');
        $code = $_GET['code'] ?? ($_POST['code'] ?? '');
        $montant = (float)($_GET['montant'] ?? ($_POST['montant'] ?? 0));

        $res = $this->promotion->valider($code, $montant);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
