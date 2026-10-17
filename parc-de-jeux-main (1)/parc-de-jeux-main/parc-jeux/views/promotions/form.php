<?php $titrePage = $id ? 'Modifier la promotion' : 'Ajouter une promotion'; ?>
<div class="entete">
    <h1><?= $id ? 'Modifier la promotion' : 'Créer une nouvelle promotion' ?></h1>
    <a class="btn gris" href="<?= e(url('promotions')) ?>">← Retour aux promotions</a>
</div>

<?php afficherErreurs($erreurs); ?>

<form method="post" class="formulaire validation">
    <div class="form-groupe">
        <label>Code promotionnel (ex: PROMO20, ETE2026) *</label>
        <input type="text" name="code" value="<?= e($promo['code']) ?>" required style="text-transform:uppercase; font-weight:700; letter-spacing:1px;">
        <small class="texte-muet">Les clients saisiront ce code dans leur panier ou lors de la réservation.</small>
    </div>

    <div class="form-groupe">
        <label>Titre public de l'offre *</label>
        <input type="text" name="titre" value="<?= e($promo['titre']) ?>" placeholder="Ex: Réduction Vacances d'Été" required>
    </div>

    <div class="form-groupe">
        <label>Description détaillée *</label>
        <textarea name="description" rows="2" required placeholder="Ex: 20% de remise immédiate dès 150 DH d'achat"><?= e($promo['description']) ?></textarea>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
        <div class="form-groupe">
            <label>Type de réduction *</label>
            <select name="type_reduction" id="type-reduction" required>
                <option value="pourcentage" <?= selectionne($promo['type_reduction'], 'pourcentage') ?>>Pourcentage (%)</option>
                <option value="montant_fixe" <?= selectionne($promo['type_reduction'], 'montant_fixe') ?>>Montant fixe (DH)</option>
            </select>
        </div>

        <div class="form-groupe">
            <label>Valeur de la réduction *</label>
            <input type="number" step="0.5" min="0.1" name="valeur" value="<?= e($promo['valeur']) ?>" required placeholder="Ex: 20">
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
        <div class="form-groupe">
            <label>Montant minimum d'achat (DH)</label>
            <input type="number" step="1" min="0" name="montant_minimum" value="<?= e($promo['montant_minimum']) ?>" placeholder="0 pour aucun minimum">
        </div>

        <div class="form-groupe">
            <label>Nombre maximum d'utilisations (quota)</label>
            <input type="number" step="1" min="1" name="nb_utilisations_max" value="<?= e($promo['nb_utilisations_max']) ?>" placeholder="Laisser vide pour illimité">
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
        <div class="form-groupe">
            <label>Date de début (optionnelle)</label>
            <input type="date" name="date_debut" value="<?= e($promo['date_debut']) ?>">
        </div>

        <div class="form-groupe">
            <label>Date d'expiration (optionnelle)</label>
            <input type="date" name="date_fin" value="<?= e($promo['date_fin']) ?>">
        </div>
    </div>

    <div class="form-groupe" style="margin-top:12px;">
        <label class="case">
            <input type="checkbox" name="actif" value="1" <?= !empty($promo['actif']) ? 'checked' : '' ?>>
            <span><strong>Activer immédiatement cette promotion</strong></span>
        </label>
    </div>

    <div style="margin-top:20px; display:flex; gap:12px;">
        <button class="btn" type="submit">💾 Enregistrer la promotion</button>
        <a class="btn gris" href="<?= e(url('promotions')) ?>">Annuler</a>
    </div>
</form>
