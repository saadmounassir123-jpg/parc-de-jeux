<?php $titrePage = 'Utilisateurs'; ?>
<div class="entete"><h1>Comptes utilisateurs</h1><a class="btn" href="<?= e(url('utilisateurs', 'ajouter')) ?>">+ Ajouter un compte</a></div>
<div class="tableau"><table>
    <tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Actif</th><th>Actions</th></tr>
    <?php foreach ($utilisateurs as $u): ?>
    <tr>
        <td><?= e($u['prenom'] . ' ' . $u['nom']) ?></td>
        <td><?= e($u['email']) ?></td>
        <td><?= e(libelle($u['role'])) ?></td>
        <td><?= $u['actif'] ? badge('actif') : badge('inactif') ?></td>
        <td class="actions">
            <a href="<?= e(url('utilisateurs', 'modifier', ['id' => $u['id_utilisateur']])) ?>">Modifier</a>
            <form method="post" action="<?= e(url('utilisateurs', 'basculer')) ?>" data-confirm="Changer l'état de ce compte ?">
                <input type="hidden" name="id" value="<?= (int)$u['id_utilisateur'] ?>">
                <button class="lien" type="submit"><?= $u['actif'] ? 'Désactiver' : 'Réactiver' ?></button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table></div>
