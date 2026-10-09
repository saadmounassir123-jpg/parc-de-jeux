<?php $titrePage = 'Responsable'; ?>
<div class="entete"><h1><?= e($r['prenom'] . ' ' . $r['nom']) ?> <?= badge($r['statut']) ?></h1>
    <div>
        <a class="btn" href="<?= e(url('responsables', 'modifier', ['id' => $r['id_responsable']])) ?>">Modifier</a>
        <?php if ($r['statut'] === 'actif'): ?><a class="btn orange" href="<?= e(url('responsables', 'affecter', ['id' => $r['id_responsable']])) ?>">Affecter à des jeux</a><?php endif; ?>
        <a class="btn gris" href="<?= e(url('responsables')) ?>">Retour</a>
    </div></div>
<div class="carte"><dl class="details">
    <dt>Fonction</dt><dd><?= e($r['fonction']) ?></dd>
    <dt>Téléphone</dt><dd><?= e($r['telephone']) ?></dd>
    <dt>Email</dt><dd><?= e($r['email']) ?></dd>
    <dt>Date d'embauche</dt><dd><?= dateFr($r['date_embauche']) ?></dd>
</dl></div>
<h2>Jeux affectés</h2>
<div class="tableau"><table>
    <tr><th>Jeu</th><th>Catégorie</th><th>État</th></tr>
    <?php foreach ($jeux as $j): ?>
    <tr><td><a href="<?= e(url('jeux', 'detail', ['id' => $j['id_jeu']])) ?>"><?= e($j['nom']) ?></a></td><td><?= e($j['categorie']) ?></td><td><?= badge($j['etat']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$jeux): ?><tr><td colspan="3">Aucun jeu affecté.</td></tr><?php endif; ?>
</table></div>
