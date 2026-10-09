<?php $titrePage = 'Responsables'; ?>
<div class="entete"><h1>Responsables</h1><a class="btn" href="<?= e(url('responsables', 'ajouter')) ?>">+ Ajouter un responsable</a></div>
<form method="get" class="filtres">
    <input type="hidden" name="page" value="responsables">
    <input type="text" name="q" placeholder="Rechercher (nom, prénom, fonction)..." value="<?= e($q) ?>">
    <button class="btn" type="submit">Rechercher</button>
</form>
<div class="tableau"><table>
    <tr><th>Nom</th><th>Fonction</th><th>Téléphone</th><th>Email</th><th>Embauche</th><th>Jeux</th><th>Statut</th><th>Actions</th></tr>
    <?php foreach ($responsables as $r): ?>
    <tr>
        <td><a href="<?= e(url('responsables', 'detail', ['id' => $r['id_responsable']])) ?>"><?= e($r['prenom'] . ' ' . $r['nom']) ?></a></td>
        <td><?= e($r['fonction']) ?></td><td><?= e($r['telephone']) ?></td><td><?= e($r['email']) ?></td>
        <td><?= dateFr($r['date_embauche']) ?></td><td><?= (int)$r['nb_jeux'] ?></td><td><?= badge($r['statut']) ?></td>
        <td class="actions">
            <a href="<?= e(url('responsables', 'modifier', ['id' => $r['id_responsable']])) ?>">Modifier</a>
            <?php if ($r['statut'] === 'actif'): ?><a href="<?= e(url('responsables', 'affecter', ['id' => $r['id_responsable']])) ?>">Affecter</a><?php endif; ?>
            <form method="post" action="<?= e(url('responsables', 'basculer')) ?>" data-confirm="Changer le statut de ce responsable ?">
                <input type="hidden" name="id" value="<?= (int)$r['id_responsable'] ?>">
                <button class="lien" type="submit"><?= $r['statut'] === 'actif' ? 'Désactiver' : 'Réactiver' ?></button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$responsables): ?><tr><td colspan="8">Aucun responsable trouvé.</td></tr><?php endif; ?>
</table></div>
