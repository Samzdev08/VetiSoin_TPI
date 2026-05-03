<?php
/**
 * Fichier : form.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Formulaire creation/edition d'un patient
 */

/** @var int|null $id */
/** @var array|null $patient */
?>

<div class="container mt-4" style="max-width: 720px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0"><?= $id ? 'Modifier le patient' : 'Ajouter un patient' ?></h1>
        <a href="/patients" class="btn btn-sm btn-outline-secondary">← Retour</a>
    </div>

    <form action="<?= $id ? "/patient/$id/update" : '/patient/add' ?>" method="post" novalidate>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="row g-3">

            <div class="col-md-6">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" id="nom" name="nom" class="form-control" required maxlength="80"
                       value="<?= htmlspecialchars($patient['nom'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" id="prenom" name="prenom" class="form-control" required maxlength="80"
                       value="<?= htmlspecialchars($patient['prenom'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label for="date_naissance" class="form-label">Date de naissance</label>
                <input type="date" id="date_naissance" name="date_naissance" class="form-control" required
                       max="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($patient['date_naissance'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label d-block">Genre</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="genre_homme" name="genre" value="Homme" required
                        <?= (isset($patient['genre']) && $patient['genre'] === 'Homme') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="genre_homme">Homme</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="genre_femme" name="genre" value="Femme" required
                        <?= (isset($patient['genre']) && $patient['genre'] === 'Femme') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="genre_femme">Femme</label>
                </div>
            </div>

            <div class="col-md-6">
                <label for="numeroDossier" class="form-label">Numéro de dossier</label>
                <input type="text" id="numeroDossier" name="numeroDossier" class="form-control" required
                       pattern="^DOS-2026-\d+$"
                       placeholder="DOS-2026-001"
                       value="<?= htmlspecialchars($patient['numero_dossier'] ?? '') ?>">
                <small class="text-muted">Format attendu : <code>DOS-2026-XXX</code></small>
            </div>

            <div class="col-md-6">
                <label for="chambre" class="form-label">Chambre</label>
                <input type="text" id="chambre" name="chambre" class="form-control" required maxlength="10"
                       value="<?= htmlspecialchars($patient['chambre'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label for="service" class="form-label">Service</label>
                <select id="service" name="service" class="form-select" required>
                    <option value="" disabled <?= empty($patient['service']) ? 'selected' : '' ?>>Sélectionner…</option>
                    <option value="Urgences"         <?= (isset($patient['service']) && $patient['service'] === 'Urgences')         ? 'selected' : '' ?>>Urgences</option>
                    <option value="Chirurgie"        <?= (isset($patient['service']) && $patient['service'] === 'Chirurgie')        ? 'selected' : '' ?>>Chirurgie</option>
                    <option value="Médecine interne" <?= (isset($patient['service']) && $patient['service'] === 'Médecine interne') ? 'selected' : '' ?>>Médecine interne</option>
                </select>
            </div>

            <div class="col-md-6">
                <label for="statut" class="form-label">Statut</label>
                <select id="statut" name="statut" class="form-select" required>
                    <option value="Hospitalisé" <?= (!isset($patient['statut']) || $patient['statut'] === 'Hospitalisé') ? 'selected' : '' ?>>Hospitalisé</option>
                    <option value="Sorti"       <?= (isset($patient['statut']) && $patient['statut'] === 'Sorti')        ? 'selected' : '' ?>>Sorti</option>
                </select>
            </div>

        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="/patients" class="btn btn-outline-secondary">Annuler</a>
            <button type="submit" class="btn btn-dark">
                <?= $id ? 'Mettre à jour' : 'Ajouter le patient' ?>
            </button>
        </div>

    </form>
</div>