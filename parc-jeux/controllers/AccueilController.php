<?php
// Page d'accueil publique du parc (accessible sans connexion)
class AccueilController {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function actionIndex() {
        vue('accueil/index', [
            'jeux' => (new Jeu($this->pdo))->tous(
                ['q' => '', 'etat' => '', 'categorie' => '', 'id_responsable' => ''],
                '',
                null
            ),
            'types' => (new Billet($this->pdo))->types(),
        ], 'public');
    }
}
