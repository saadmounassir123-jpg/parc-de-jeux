<?php $titrePage = 'Jeux'; $role = roleActuel(); $personnel = estPersonnel(); ?>
<div class="entete">
    <h1><?= $role === 'responsable' ? 'Mes jeux' : 'Les jeux du parc' ?></h1>
    <?php if ($role === 'admin'): ?><a class="btn" href="<?= e(url('jeux', 'ajouter')) ?>">+ Ajouter un jeu</a><?php endif; ?>
</div>
<?php if (!$personnel) bandeauBilletterie(); ?>
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
    <article class="carte-jeu">
        <div><?= $j['actif'] ? badge($j['etat']) : badge('inactif') ?> <span class="meta"><?= e($j['categorie']) ?></span></div>
        <h3><a href="<?= e(url('jeux', 'detail', ['id' => $j['id_jeu']])) ?>"><?= e($j['nom']) ?></a></h3>
        <p class="meta">Dès <?= (int)$j['age_minimum'] ?> ans · <?= (int)$j['duree_session'] ?> min</p>
        <p class="prix"><?= montant($j['tarif']) ?> / pers.</p>
        <a class="btn" href="<?= e(url('jeux', 'detail', ['id' => $j['id_jeu']])) ?>">Détails</a>
    </article>
    <?php endforeach; ?>
</div>
<?php if (!$jeux): ?><p>Aucun jeu trouvé.</p><?php endif; ?>
<?php else: ?>
<div class="tableau"><table>
    <tr><th>Nom</th><th>Catégorie</th><th>Âge min.</th><th>Capacité</th><th>Durée</th><th>Tarif</th><th>État</th><th>Responsable</th><th>Actions</th></tr>
    <?php foreach ($jeux as $j): ?>
    <tr class="<?= $j['actif'] ? '' : 'inactif' ?>">
        <td><a href="<?= e(url('jeux', 'detail', ['id' => $j['id_jeu']])) ?>"><?= e($j['nom']) ?></a></td>
        <td><?= e($j['categorie']) ?></td>
        <td><?= (int)$j['age_minimum'] ?> ans</td>
        <td><?= (int)$j['capacite'] ?></td>
        <td><?= (int)$j['duree_session'] ?> min</td>
        <td><?= montant($j['tarif']) ?></td>
        <td><?= $j['actif'] ? badge($j['etat']) : badge('inactif') ?></td>
        <td><?= $j['resp_nom'] ? e($j['resp_prenom'] . ' ' . $j['resp_nom']) : '-' ?></td>
        <td class="actions">
            <a href="<?= e(url('jeux', 'detail', ['id' => $j['id_jeu']])) ?>">Détails</a>
            <?php if ($role === 'admin'): ?>
                <a href="<?= e(url('jeux', 'modifier', ['id' => $j['id_jeu']])) ?>">Modifier</a>
                <form method="post" action="<?= e(url('jeux', 'basculer')) ?>" data-confirm="<?= $j['actif'] ? 'Désactiver' : 'Réactiver' ?> ce jeu ?">
                    <input type="hidden" name="id" value="<?= (int)$j['id_jeu'] ?>">
                    <button class="lien" type="submit"><?= $j['actif'] ? 'Désactiver' : 'Réactiver' ?></button>
                </form>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$jeux): ?><tr><td colspan="9">Aucun jeu trouvé.</td></tr><?php endif; ?>
</table></div>
<?php endif; ?>
