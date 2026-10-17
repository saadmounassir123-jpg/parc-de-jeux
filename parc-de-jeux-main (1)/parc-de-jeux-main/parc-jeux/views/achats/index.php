<?php 
$role = roleActuel();
$titrePage = ($role === 'client') ? 'Mes billets & achats' : 'Ventes de billets';
?>

<div class="entete">
    <h1><?= ($role === 'client') ? '🎟️ Mes billets & commandes' : '🎟️ Ventes de billets & billetterie' ?></h1>
    <a class="btn" href="<?= e(url('achats', 'billetterie')) ?>">+ Acheter des billets</a>
</div>

<form method="get" class="filtres">
    <input type="hidden" name="page" value="achats">
    <input type="date" name="date_debut" value="<?= e($filtres['date_debut']) ?>" title="Date début">
    <input type="date" name="date_fin" value="<?= e($filtres['date_fin']) ?>" title="Date fin">
    <select name="statut">
        <option value="">Tous les statuts</option>
        <option value="en_attente" <?= selectionne($filtres['statut'], 'en_attente') ?>>En attente</option>
        <option value="paye" <?= selectionne($filtres['statut'], 'paye') ?>>Payé</option>
        <option value="annule" <?= selectionne($filtres['statut'], 'annule') ?>>Annulé</option>
    </select>
    <?php if (in_array($role, ['admin', 'agent'], true)): ?>
    <select name="id_client">
        <option value="">Tous les clients</option>
        <?php foreach ($clients as $c): ?>
            <option value="<?= $c['id_client'] ?>" <?= selectionne($filtres['id_client'], $c['id_client']) ?>>
                <?= e($c['prenom'] . ' ' . $c['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php endif; ?>
    <button class="btn" type="submit">Filtrer</button>
    <?php if ($filtres['date_debut'] || $filtres['date_fin'] || $filtres['statut'] || $filtres['id_client']): ?>
        <a class="btn gris" href="<?= e(url('achats')) ?>">Réinitialiser</a>
    <?php endif; ?>
</form>

<div class="tableau">
    <table>
        <thead>
            <tr>
                <th>N° Commande</th>
                <?php if ($role !== 'client'): ?><th>Client</th><?php endif; ?>
                <th>Date achat</th>
                <th>Date visite</th>
                <th>Billets</th>
                <th>Total</th>
                <th>Statut</th>
                <th>Règlement</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($achats as $a): ?>
            <tr>
                <td><strong><?= e($a['numero_achat']) ?></strong></td>
                <?php if ($role !== 'client'): ?>
                    <td>
                        <?= e($a['prenom'] . ' ' . $a['nom']) ?><br>
                        <small class="texte-muet"><?= e($a['telephone']) ?></small>
                    </td>
                <?php endif; ?>
                <td><?= dateFr($a['date_achat']) ?></td>
                <td><strong><?= dateFr($a['date_visite']) ?></strong></td>
                <td>
                    <span class="badge b-billet"><?= (int)$a['total_billets'] ?> billet(s)</span><br>
                    <small class="texte-muet"><?= e($a['details_billets']) ?></small>
                </td>
                <td>
                    <strong><?= montant($a['montant_total']) ?></strong>
                    <?php if ((float)$a['remise'] > 0): ?>
                        <br><small style="color:#10b981;">(Promo -<?= montant($a['remise']) ?>)</small>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($a['statut'] === 'paye'): ?>
                        <span class="badge b-confirmee">Payé</span>
                    <?php elseif ($a['statut'] === 'en_attente'): ?>
                        <span class="badge b-en_attente">En attente</span>
                    <?php else: ?>
                        <span class="badge b-annulee">Annulé</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($a['mode_paiement']): ?>
                        <small>
                            <?= e(libelle($a['mode_paiement'])) ?><br>
                            <span class="texte-muet"><?= e($a['reference_paiement']) ?></span>
                        </small>
                    <?php else: ?>
                        <small class="texte-muet">Non réglé</small>
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <a class="lien" href="<?= e(url('achats', 'detail', ['id' => $a['id_achat']])) ?>">Détails</a>
                    
                    <?php if ($a['statut'] === 'paye'): ?>
                        <a class="lien" href="<?= e(url('achats', 'imprimer', ['id' => $a['id_achat']])) ?>" target="_blank">🖨️ Imprimer</a>
                    <?php endif; ?>

                    <?php if ($a['statut'] === 'en_attente'): ?>
                        <a class="btn btn-sm" href="<?= e(url('paiement', 'recapitulatif', ['type' => 'achat', 'id' => $a['id_achat']])) ?>">
                            💳 Payer
                        </a>

                        <?php if (in_array($role, ['admin', 'agent'], true)): ?>
                            <form method="post" action="<?= e(url('achats', 'statut')) ?>" style="display:inline" data-confirm="Encaisser cette commande au guichet ?">
                                <input type="hidden" name="id" value="<?= (int)$a['id_achat'] ?>">
                                <input type="hidden" name="statut" value="paye">
                                <select name="mode_paiement" style="padding:2px 6px; font-size:12px;">
                                    <option value="especes">Espèces</option>
                                    <option value="carte">Carte</option>
                                </select>
                                <button class="lien" type="submit">Encaisser</button>
                            </form>
                        <?php endif; ?>

                        <form method="post" action="<?= e(url('achats', 'statut')) ?>" style="display:inline" data-confirm="Annuler cette commande de billets ?">
                            <input type="hidden" name="id" value="<?= (int)$a['id_achat'] ?>">
                            <input type="hidden" name="statut" value="annule">
                            <button class="lien rouge" type="submit">Annuler</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$achats): ?>
                <tr><td colspan="<?= $role !== 'client' ? 9 : 8 ?>">Aucun achat de billets enregistré.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
