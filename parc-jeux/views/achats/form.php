<?php $titrePage = 'Vente à la billetterie'; ?>
<h1>Enregistrer une vente</h1>
<?php afficherErreurs($erreurs); ?>
<form method="post" class="formulaire validation">
    <label>Client * <a href="<?= e(url('clients', 'ajouter')) ?>">(nouveau client)</a></label>
    <select name="id_client" required><option value="">-- Choisir --</option>
        <?php foreach ($clients as $c): ?><option value="<?= $c['id_client'] ?>" <?= selectionne($form['id_client'], $c['id_client']) ?>><?= e($c['prenom'] . ' ' . $c['nom']) ?></option><?php endforeach; ?>
    </select>

    <label>Billets (quantité)</label>
    <table class="mini">
        <tr><th>Type</th><th>Prix unitaire</th><th>Quantité</th><th>Sous-total</th></tr>
        <?php foreach ($types as $t): ?>
        <tr>
            <td><?= e($t['nom']) ?></td>
            <td><?= montant($t['tarif']) ?></td>
            <td><input type="number" class="quantite" name="quantite[<?= $t['id_type_billet'] ?>]" min="0" max="50" data-prix="<?= e($t['tarif']) ?>"
                       value="<?= (int)($form['quantites'][$t['id_type_billet']] ?? 0) ?>"></td>
            <td class="sous-total">0,00 DH</td>
        </tr>
        <?php endforeach; ?>
        <tr><td colspan="3"><strong>Total</strong></td><td><strong id="total-achat">0,00 DH</strong></td></tr>
    </table>
    <small>Le montant définitif est recalculé par le serveur.</small>

    <label>Mode de paiement *</label>
    <select name="mode_paiement">
        <option value="especes" <?= selectionne($form['mode_paiement'], 'especes') ?>>Espèces</option>
        <option value="carte" <?= selectionne($form['mode_paiement'], 'carte') ?>>Carte</option>
    </select>
    <label>Statut du paiement</label>
    <select name="statut_paiement">
        <option value="paye" <?= selectionne($form['statut_paiement'], 'paye') ?>>Payé</option>
        <option value="en_attente" <?= selectionne($form['statut_paiement'], 'en_attente') ?>>En attente</option>
    </select>
    <button class="btn" type="submit">Enregistrer la vente</button>
    <a class="btn gris" href="<?= e(url('billets', 'achats')) ?>">Annuler</a>
</form>
