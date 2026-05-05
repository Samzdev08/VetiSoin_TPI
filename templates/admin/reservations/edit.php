<?php
/**
 * Fichier : edit.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Modification d'une réservation (admin)
 */
/** @var array $reservation */

$row = $reservation[0];

$statuts = ['En attente', 'Confirmée', 'Clôturée', 'Annulée'];
?>

<div class="container mt-4" style="max-width: 600px;">
    <h1 class="h4 mb-3">Modifier la réservation #<?= $row['id'] ?></h1>
    <a href="/admin/reservations/<?= $row['id'] ?>" class="btn btn-sm btn-outline-secondary mb-4">← Retour</a>

    <div class="card mb-3">
        <div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Patient</h6>
            <p class="mb-1 fw-semibold">
                <?= htmlspecialchars($row['patient_nom'] . ' ' . $row['patient_prenom']) ?>
            </p>
            <p class="mb-0 text-muted small">
                Dossier : <?= htmlspecialchars($row['numero_dossier']) ?>
                &nbsp;·&nbsp; Chambre : <?= htmlspecialchars($row['chambre']) ?>
                &nbsp;·&nbsp; <?= htmlspecialchars($row['patient_service']) ?>
            </p>
        </div>
    </div>

    <form action="/admin/reservations/<?= $row['id'] ?>/edit" method="post" class="card card-body">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="mb-3">
            <label for="statut" class="form-label">Statut</label>
            <select name="statut" id="statut" class="form-select" required>
                <?php foreach ($statuts as $s) : ?>
                    <option value="<?= $s ?>" <?= $row['statut'] === $s ? 'selected' : '' ?>>
                        <?= $s ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="date_retrait" class="form-label">Date de retrait prévue</label>
            <input type="date"
                id="date_retrait"
                name="date_retrait"
                value="<?= htmlspecialchars(date('Y-m-d', strtotime($row['date_retrait_effective'])) ?? '') ?>"
                class="form-control">
        </div>

        <div class="mb-3">
            <label for="commentaire" class="form-label">Commentaire</label>
            <textarea name="commentaire" id="commentaire" class="form-control" rows="3"
                placeholder="Commentaire admin..."><?= htmlspecialchars($row['commentaire'] ?? '') ?></textarea>
        </div>

        <div class="d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-dark">Enregistrer</button>
            <a href="/admin/reservations/<?= $row['id'] ?>" class="btn btn-outline-secondary">Annuler</a>
        </div>
    </form>
</div>