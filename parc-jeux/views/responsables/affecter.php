<?php $titrePage = 'Affectation'; ?>
<h1>Affecter <?= e($r['prenom'] . ' ' . $r['nom']) ?> à des jeux</h1>
<p>Cochez les jeux dont ce responsable est chargé. Un jeu n'a qu'un seul responsable : cocher un jeu déjà affecté à quelqu'un d'autre le lui retire.</p>
<form method="post" class="formulaire">
    <input type="hidden" name="id" value="<?= (int)$r['id_responsable'] ?>">
    <?php foreach ($jeux as $j): ?>
        <label class="case">
            <input type="checkbox" name="jeux[]" value="<?= (int)$j['id_jeu'] ?>" <?= $j['id_responsable'] == $r['id_responsable'] ? 'checked' : '' ?>>
            <?= e($j['nom']) ?> <small>(<?= e($j['categorie']) ?><?= ($j['id_responsable'] && $j['id_responsable'] != $r['id_responsable']) ? ' — déjà affecté à un autre responsable' : '' ?>)</small>
        </label>
    <?php endforeach; ?>
    <button class="btn" type="submit">Enregistrer l'affectation</button>
    <a class="btn gris" href="<?= e(url('responsables', 'detail', ['id' => $r['id_responsable']])) ?>">Annuler</a>
</form>
