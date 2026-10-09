<?php
// Tableau de bord (administrateur)
class DashboardController {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
        exigerRole(['admin']);
    }

    public function actionIndex() {
        $dateDebut = $_GET['date_debut'] ?? '';
        $dateFin = $_GET['date_fin'] ?? '';
        // Sans filtre : toute la période
        $debut = dateValide($dateDebut) ? $dateDebut : '1900-01-01';
        $fin = dateValide($dateFin) ? $dateFin : '2999-12-31';

        $jeu = new Jeu($this->pdo);
        $recette = new Recette($this->pdo);
        $depense = new Depense($this->pdo);

        $totalRecettes = $recette->total($debut, $fin);
        $totalDepenses = $depense->total($debut, $fin);

        // Données du graphique : 6 derniers mois
        $recettesMois = array_column($recette->parMois(), 'total', 'mois');
        $depensesMois = array_column($depense->parMois(), 'total', 'mois');
        $labels = []; $serieRecettes = []; $serieDepenses = [];
        for ($i = 5; $i >= 0; $i--) {
            $mois = date('Y-m', strtotime("first day of -$i month"));
            $labels[] = $mois;
            $serieRecettes[] = (float)($recettesMois[$mois] ?? 0);
            $serieDepenses[] = (float)($depensesMois[$mois] ?? 0);
        }

        vue('dashboard/index', [
            'dateDebut' => $dateDebut, 'dateFin' => $dateFin,
            'totalJeux' => $jeu->compterTotal(), 'etats' => $jeu->compterParEtat(),
            'nbReservations' => (new Reservation($this->pdo))->compter($debut, $fin),
            'nbBillets' => (new Achat($this->pdo))->compterBillets($debut, $fin),
            'totalRecettes' => $totalRecettes, 'totalDepenses' => $totalDepenses,
            'resultat' => $totalRecettes - $totalDepenses,
            'sources' => $recette->parSource($debut, $fin),
            'labels' => $labels, 'serieRecettes' => $serieRecettes, 'serieDepenses' => $serieDepenses,
        ]);
    }
}
