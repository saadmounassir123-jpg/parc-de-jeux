<?php $titrePage = 'Signaler une panne'; ?>
<h1>Signaler une panne ou un besoin de maintenance</h1>
<?php afficherErreurs($erreurs); ?>
<form method="post" class="formulaire validation">
    <label>Jeu concerné *</label>
    <select name="id_jeu" required><option value="">-- Choisir --</option>
        <?php foreach ($jeux as $j): ?><option value="<?= $j['id_jeu'] ?>" <?= selectionne($s['id_jeu'], $j['id_jeu']) ?>><?= e($j['nom']) ?></option><?php endforeach; ?>
    </select>
    <label>Description du problème *</label><textarea name="description" rows="4" required><?= e($s['description']) ?></textarea>
    <button class="btn orange" type="submit">Signaler</button>
    <a class="btn gris" href="<?= e(url('maintenance')) ?>">Annuler</a>
</form>
