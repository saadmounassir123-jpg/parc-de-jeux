<?php $titrePage = 'Tableau de bord'; ?>
<div class="entete">
    <h1>Tableau de bord</h1>
</div>
<form method="get" class="filtres">
    <input type="hidden" name="page" value="dashboard">
    <label style="align-self: center;">Période du</label><input type="date" name="date_debut" value="<?= e($dateDebut) ?>">
    <label style="align-self: center;">au</label><input type="date" name="date_fin" value="<?= e($dateFin) ?>">
    <button class="btn" type="submit">Filtrer</button>
    <a class="btn violet" href="<?= e(url('dashboard')) ?>">Toute la période</a>
</form>

<div class="stats">
    <div class="stat-carte"><span>Jeux (actifs)</span><strong><?= (int)$totalJeux ?></strong></div>
    <div class="stat-carte"><span>Réservations</span><strong><?= (int)$nbReservations ?></strong></div>
    <div class="stat-carte"><span>Billets vendus</span><strong><?= (int)$nbBillets ?></strong></div>
    <div class="stat-carte" style="border-bottom: 4px solid var(--couleur-succes);"><span>Total recettes</span><strong style="color: var(--couleur-succes);"><?= montant($totalRecettes) ?></strong></div>
    <div class="stat-carte" style="border-bottom: 4px solid var(--couleur-erreur);"><span>Total dépenses</span><strong style="color: var(--couleur-erreur);"><?= montant($totalDepenses) ?></strong></div>
    <div class="stat-carte" style="border-bottom: 4px solid <?= $resultat >= 0 ? 'var(--couleur-succes)' : 'var(--couleur-erreur)' ?>;"><span>Résultat</span><strong style="color: <?= $resultat >= 0 ? 'var(--couleur-succes)' : 'var(--couleur-erreur)' ?>;"><?= montant($resultat) ?></strong></div>
</div>

<div class="grille-jeux">
    <div class="boite-blanche">
        <h2>État des jeux</h2>
        <table class="mini">
            <?php foreach ($etats as $e1): ?><tr><td><?= badge($e1['etat']) ?></td><td style="text-align: right; font-weight: bold;"><?= (int)$e1['nb'] ?></td></tr><?php endforeach; ?>
        </table>
        <h2 style="margin-top: 2rem;">Recettes par source</h2>
        <table class="mini">
            <?php foreach ($sources as $s): ?><tr><td><?= e(libelle($s['source'])) ?></td><td style="text-align: right; font-weight: bold;"><?= montant($s['total']) ?></td></tr><?php endforeach; ?>
            <?php if (!$sources): ?><tr><td colspan="2">Aucune recette.</td></tr><?php endif; ?>
        </table>
    </div>
    <div class="boite-blanche">
        <h2>Recettes et dépenses (6 derniers mois)</h2>
        <div id="graphique"
             data-labels="<?= e(json_encode($labels)) ?>"
             data-recettes="<?= e(json_encode($serieRecettes)) ?>"
             data-depenses="<?= e(json_encode($serieDepenses)) ?>"></div>
        <div class="legende-graph">
            <div><span class="pastille vert"></span> Recettes</div>
            <div><span class="pastille rouge"></span> Dépenses</div>
        </div>
    </div>
</div>
