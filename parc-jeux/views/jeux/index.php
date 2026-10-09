<?php $titrePage = 'Jeux'; $role = roleActuel(); $personnel = estPersonnel(); ?>
<div class="entete">
    <h1><?= $role === 'responsable' ? 'Mes jeux' : 'Les jeux du parc' ?></h1>
    <?php if ($role === 'admin'): ?><a class="btn" href="<?= e(url('jeux', 'ajouter')) ?>">+ Ajouter un jeu</a><?php endif; ?>
</div>
<?php if (!$personnel && function_exists('bandeauBilletterie')) bandeauBilletterie(); ?>
<form method="get" class="filtres">
    <input type="hidden" name="page" value="jeux">
    <input type="text" name="q" placeholder="Rechercher un jeu..." value="<?= e($filtres['q']) ?>">
    <select name="categorie"><option value="">Toutes catégories</option>
        <?php foreach (CATEGORIES_JEUX as $cat): ?><option <?= selectionne($filtres['categorie'], $cat) ?>><?= e($cat) ?></option><?php endforeach; ?>
    </select>
    <select name="etat"><option value="">Tous les états</option>
        <?php foreach (ETATS_JEU as $etat): ?><option value="<?= $etat ?>" <?= selectionne($filtres['etat'], $etat) ?>><?= e(libelle($etat)) ?></option><?php endforeach; ?>
    </select>
    <?php if ($role === 'admin'): ?>
    <select name="id_responsable"><option value="">Tous les responsables</option>
        <?php foreach ($responsables as $r): ?><option value="<?= $r['id_responsable'] ?>" <?= selectionne($filtres['id_responsable'], $r['id_responsable']) ?>><?= e($r['prenom'] . ' ' . $r['nom']) ?></option><?php endforeach; ?>
    </select>
    <?php endif; ?>
    <button class="btn" type="submit">Filtrer</button>
</form>

<?php if (!$personnel): ?>
<div class="grille-jeux">
    <?php foreach ($jeux as $j): ?>
    <article class="carte-jeu" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
        <?php $imgFile = 'public/img/jeu_' . $j['id_jeu'] . '.jpg'; ?>
        <?php if (file_exists(RACINE . '/' . $imgFile)): ?>
            <img src="<?= e($imgFile) ?>" alt="<?= e($j['nom']) ?>" style="width: 100%; height: 200px; object-fit: cover;">
        <?php else: ?>
            <div style="width: 100%; height: 200px; background: #cbd5e1; display:flex; align-items:center; justify-content:center; color: white;">Image indisponible</div>
        <?php endif; ?>
        
        <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
            <div><?= $j['actif'] ? badge($j['etat']) : badge('inactif') ?> <span class="badge" style="background:#e2e8f0; color:#475569;"><?= e($j['categorie']) ?></span></div>
            <h3 style="margin-top: 1rem;"><a href="<?= e(url('jeux', 'detail', ['id' => $j['id_jeu']])) ?>"><?= e($j['nom']) ?></a></h3>
            <p class="meta">Dès <?= (int)$j['age_minimum'] ?> ans • <?= (int)$j['duree_session'] ?> min</p>
            <p class="prix" style="margin-top: auto; padding-top: 1rem;"><?= montant($j['tarif']) ?> / pers.</p>
            <a class="btn" style="display:block; text-align:center;" href="<?= e(url('jeux', 'detail', ['id' => $j['id_jeu']])) ?>">Voir le détail</a>
        </div>
    </article>
    <?php endforeach; ?>
</div>
<?php if (!$jeux): ?><p>Aucun jeu trouvé.</p><?php endif; ?>
<?php else: ?>
<div class="tableau"><table>
    <tr><th>Nom</th><th>Catégorie</th><th>Âge min.</th><th>Capacité</th><th>Durée</th><th>Tarif</th><th>État</th><th>Responsable</th><th>Actions</th></tr>
    <?php foreach ($jeux as $j): ?>
    <tr class="<?= $j['actif'] ? '' : 'inactif' ?>" style="<?= !$j['actif'] ? 'opacity: 0.6;' : '' ?>">
        <td><a href="<?= e(url('jeux', 'detail', ['id' => $j['id_jeu']])) ?>"><strong><?= e($j['nom']) ?></strong></a></td>
        <td><?= e($j['categorie']) ?></td>
        <td><?= (int)$j['age_minimum'] ?> ans</td>
        <td><?= (int)$j['capacite'] ?></td>
        <td><?= (int)$j['duree_session'] ?> min</td>
        <td><?= montant($j['tarif']) ?></td>
        <td><?= $j['actif'] ? badge($j['etat']) : badge('inactif') ?></td>
        <td><?= $j['resp_nom'] ? e($j['resp_prenom'] . ' ' . $j['resp_nom']) : '-' ?></td>
        <td class="actions">
            <?php if ($role === 'admin'): ?>
                <a class="lien" href="<?= e(url('jeux', 'modifier', ['id' => $j['id_jeu']])) ?>">Modifier</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$jeux): ?><tr><td colspan="9">Aucun jeu trouvé.</td></tr><?php endif; ?>
</table></div>
<?php endif; ?>
