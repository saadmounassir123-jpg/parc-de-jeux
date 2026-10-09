<?php $titrePage = 'Tableau de bord'; ?>
<h1>Tableau de bord</h1>
<form method="get" class="filtres">
    <input type="hidden" name="page" value="dashboard">
    <label>Période du</label><input type="date" name="date_debut" value="<?= e($dateDebut) ?>">
    <label>au</label><input type="date" name="date_fin" value="<?= e($dateFin) ?>">
    <button class="btn" type="submit">Filtrer</button>
    <a class="btn gris" href="<?= e(url('dashboard')) ?>">Toute la période</a>
</form>

<div class="cartes">
    <div class="stat"><span>Jeux (actifs)</span><strong><?= (int)$totalJeux ?></strong></div>
    <div class="stat"><span>Réservations</span><strong><?= (int)$nbReservations ?></strong></div>
    <div class="stat"><span>Billets vendus</span><strong><?= (int)$nbBillets ?></strong></div>
    <div class="stat vert"><span>Total recettes</span><strong><?= montant($totalRecettes) ?></strong></div>
    <div class="stat rouge"><span>Total dépenses</span><strong><?= montant($totalDepenses) ?></strong></div>
    <div class="stat <?= $resultat >= 0 ? 'vert' : 'rouge' ?>"><span>Résultat (recettes − dépenses)</span><strong><?= montant($resultat) ?></strong></div>
</div>

<div class="deux-colonnes">
    <div class="carte">
        <h2>État des jeux</h2>
        <table class="mini">
            <?php foreach ($etats as $e1): ?><tr><td><?= badge($e1['etat']) ?></td><td><?= (int)$e1['nb'] ?></td></tr><?php endforeach; ?>
        </table>
        <h2>Recettes par source</h2>
        <table class="mini">
            <?php foreach ($sources as $s): ?><tr><td><?= e(libelle($s['source'])) ?></td><td><?= montant($s['total']) ?></td></tr><?php endforeach; ?>
            <?php if (!$sources): ?><tr><td>Aucune recette.</td></tr><?php endif; ?>
        </table>
    </div>
    <div class="carte">
        <h2>Recettes et dépenses (6 derniers mois)</h2>
        <div id="graphique" class="graphique"
             data-labels="<?= e(json_encode($labels)) ?>"
             data-recettes="<?= e(json_encode($serieRecettes)) ?>"
             data-depenses="<?= e(json_encode($serieDepenses)) ?>"></div>
        <p class="legende"><span class="pastille vert"></span> Recettes <span class="pastille rouge"></span> Dépenses</p>
    </div>
</div>
