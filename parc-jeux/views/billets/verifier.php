<?php $titrePage = 'Vérifier un billet'; ?>
<h1>Vérifier un billet</h1>
<form method="get" class="filtres">
    <input type="hidden" name="page" value="billets"><input type="hidden" name="action" value="verifier">
    <input type="text" name="code" placeholder="Code du billet (ex : BIL-DEMO0002)" value="<?= e($code) ?>" required>
    <button class="btn" type="submit">Vérifier</button>
</form>
<?php if ($code !== ''): ?>
    <?php if (!$billet): ?>
        <div class="message erreur"><?= e($probleme) ?></div>
    <?php else: ?>
        <div class="carte"><dl class="details">
            <dt>Code</dt><dd><?= e($billet['code_unique']) ?></dd>
            <dt>Type</dt><dd><?= e($billet['type_nom']) ?></dd>
            <dt>Commande</dt><dd><?= e($billet['numero_commande']) ?> (<?= e($billet['client_prenom'] . ' ' . $billet['client_nom']) ?>)</dd>
            <dt>Statut</dt><dd><?= badge($billet['statut']) ?></dd>
        </dl></div>
        <?php if ($probleme === ''): ?>
            <div class="message succes">Billet valide.</div>
            <form method="post" action="<?= e(url('billets', 'utiliser')) ?>" data-confirm="Enregistrer l'utilisation de ce billet ?">
                <input type="hidden" name="code" value="<?= e($billet['code_unique']) ?>">
                <button class="btn" type="submit">Enregistrer l'utilisation</button>
            </form>
        <?php else: ?>
            <div class="message erreur"><?= e($probleme) ?></div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
