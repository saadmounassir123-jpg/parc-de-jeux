<?php $titrePage = 'Gestion des promotions'; ?>
<div class="entete">
    <h1>🎁 Codes promotionnels & Offres</h1>
    <a class="btn" href="<?= e(url('promotions', 'ajouter')) ?>">+ Nouvelle promotion</a>
</div>

<form method="get" class="filtres">
    <input type="hidden" name="page" value="promotions">
    <input type="text" name="recherche" placeholder="Rechercher code, titre..." value="<?= e($filtres['recherche']) ?>">
    <select name="actif">
        <option value="">Tous les statuts</option>
        <option value="1" <?= selectionne($filtres['actif'], '1') ?>>Actifs uniquement</option>
        <option value="0" <?= selectionne($filtres['actif'], '0') ?>>Inactifs uniquement</option>
    </select>
    <button class="btn" type="submit">Filtrer</button>
    <?php if ($filtres['recherche'] !== '' || $filtres['actif'] !== ''): ?>
        <a class="btn gris" href="<?= e(url('promotions')) ?>">Réinitialiser</a>
    <?php endif; ?>
</form>

<div class="tableau">
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Titre / Description</th>
                <th>Réduction</th>
                <th>Min. Achat</th>
                <th>Validité</th>
                <th>Utilisations</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($promotions as $p): ?>
            <?php 
                $expiree = (!empty($p['date_fin']) && $p['date_fin'] < date('Y-m-d'));
                $epuisee = ($p['nb_utilisations_max'] !== null && (int)$p['nb_utilisations'] >= (int)$p['nb_utilisations_max']);
            ?>
            <tr>
                <td><strong class="code-badge"><?= e($p['code']) ?></strong></td>
                <td>
                    <strong><?= e($p['titre']) ?></strong><br>
                    <small class="texte-muet"><?= e($p['description']) ?></small>
                </td>
                <td>
                    <span class="badge b-vert">
                        <?= $p['type_reduction'] === 'pourcentage' ? '-' . (float)$p['valeur'] . '%' : '-' . montant($p['valeur']) ?>
                    </span>
                </td>
                <td><?= (float)$p['montant_minimum'] > 0 ? montant($p['montant_minimum']) : 'Aucun' ?></td>
                <td>
                    <small>
                        Du : <?= dateFr($p['date_debut']) ?><br>
                        Au : <?= dateFr($p['date_fin']) ?>
                    </small>
                </td>
                <td>
                    <?= (int)$p['nb_utilisations'] ?>
                    <?php if ($p['nb_utilisations_max'] !== null): ?>
                        / <?= (int)$p['nb_utilisations_max'] ?>
                    <?php else: ?>
                        (illimité)
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!$p['actif']): ?>
                        <span class="badge b-hors_service">Inactif</span>
                    <?php elseif ($expiree): ?>
                        <span class="badge b-annulee">Expiré</span>
                    <?php elseif ($epuisee): ?>
                        <span class="badge b-en_attente">Épuisé</span>
                    <?php else: ?>
                        <span class="badge b-disponible">Actif</span>
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <a class="lien" href="<?= e(url('promotions', 'modifier', ['id' => $p['id_promotion']])) ?>">Modifier</a>
                    <form method="post" action="<?= e(url('promotions', 'basculer')) ?>" style="display:inline">
                        <input type="hidden" name="id" value="<?= (int)$p['id_promotion'] ?>">
                        <button class="lien" type="submit"><?= $p['actif'] ? 'Désactiver' : 'Activer' ?></button>
                    </form>
                    <form method="post" action="<?= e(url('promotions', 'supprimer')) ?>" style="display:inline" data-confirm="Supprimer définitivement cette promotion ?">
                        <input type="hidden" name="id" value="<?= (int)$p['id_promotion'] ?>">
                        <button class="lien rouge" type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$promotions): ?>
                <tr><td colspan="8">Aucune promotion trouvée.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
