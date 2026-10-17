<?php $titrePage = 'Clients'; ?>
<div class="entete"><h1>Clients</h1><a class="btn" href="<?= e(url('clients', 'ajouter')) ?>">+ Ajouter un client</a></div>
<form method="get" class="filtres">
    <input type="hidden" name="page" value="clients">
    <input type="text" name="q" placeholder="Rechercher (nom, email, téléphone)..." value="<?= e($q) ?>">
    <button class="btn" type="submit">Rechercher</button>
</form>
<div class="tableau"><table>
    <tr><th>Nom</th><th>Téléphone</th><th>Email</th><th>Type</th><th>Actions</th></tr>
    <?php foreach ($clients as $c): ?>
    <tr>
        <td><a href="<?= e(url('clients', 'detail', ['id' => $c['id_client']])) ?>"><?= e($c['prenom'] . ' ' . $c['nom']) ?></a></td>
        <td><?= e($c['telephone']) ?></td><td><?= e($c['email']) ?></td><td><?= e(libelle($c['type_client'])) ?></td>
        <td class="actions"><a href="<?= e(url('clients', 'detail', ['id' => $c['id_client']])) ?>">Consulter</a>
            <a href="<?= e(url('clients', 'modifier', ['id' => $c['id_client']])) ?>">Modifier</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$clients): ?><tr><td colspan="5">Aucun client trouvé.</td></tr><?php endif; ?>
</table></div>
