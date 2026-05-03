<?php

/**
 * Fichier : detail.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Détail d'un rendez-vous (admin)
 */
/** @var array $rendezVous */

$badges = [
    'Planifié'   => 'primary',
    'Réalisé'    => 'success',
    'Annulé'     => 'danger',
    'Non honoré' => 'secondary',
];
$couleur = $badges[$rendezVous['statut']] ?? 'secondary';
?>

<div class="container mt-4" style="max-width: 780px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Rendez-vous #<?= $rendezVous['id'] ?></h1>
        <span class="badge bg-<?= $couleur ?> fs-6"><?= htmlspecialchars($rendezVous['statut']) ?></span>
    </div>

    <a href="/admin/rdv" class="btn btn-sm btn-outline-secondary mb-4">← Retour</a>

    <div class="card mb-3">
        <div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Date et lieu</h6>
            <p class="mb-1 small">
                <span class="text-muted">Date :</span>
                <?= htmlspecialchars(date('d.m.Y', strtotime($rendezVous['date_rdv']))) ?>
            </p>
            <p class="mb-1 small">
                <span class="text-muted">Heure :</span>
                <?= htmlspecialchars(substr($rendezVous['heure_rdv'], 0, 5)) ?>
            </p>
            <p class="mb-0 small">
                <span class="text-muted">Lieu :</span>
                <?= htmlspecialchars($rendezVous['lieu']) ?>
            </p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Patient</h6>
            <p class="mb-1 fw-semibold">
                <?= htmlspecialchars($rendezVous['patient_nom'] . ' ' . $rendezVous['patient_prenom']) ?>
            </p>
            <p class="mb-0 text-muted small">
                Dossier : <?= htmlspecialchars($rendezVous['numero_dossier']) ?>
                &nbsp;·&nbsp;
                Chambre : <?= htmlspecialchars($rendezVous['chambre']) ?>
                &nbsp;·&nbsp;
                <?= htmlspecialchars($rendezVous['patient_service']) ?>
            </p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Soignant</h6>
            <p class="mb-0 fw-semibold">
                <?= htmlspecialchars($rendezVous['soignant_nom'] . ' ' . $rendezVous['soignant_prenom']) ?>
            </p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Réservation associée</h6>
            <p class="mb-1 small">
                <span class="text-muted">N° :</span>
                #<?= htmlspecialchars($rendezVous['id_reservation']) ?>
            </p>
            <p class="mb-1 small">
                <span class="text-muted">Statut :</span>
                <?= htmlspecialchars($rendezVous['statut_reservation']) ?>
            </p>
            <p class="mb-2 small">
                <span class="text-muted">Créée le :</span>
                <?= htmlspecialchars($rendezVous['date_reservation'] ?? '—') ?>
            </p>
            <a href="/admin/reservations/<?= $rendezVous['id_reservation'] ?>" class="btn btn-outline-primary btn-sm">
                Voir la réservation
            </a>
        </div>
    </div>

    <?php if (!empty($rendezVous['commentaire'])) : ?>
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Commentaire du soignant</h6>
                <p class="mb-0 small"><?= htmlspecialchars($rendezVous['commentaire']) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($rendezVous['statut'] === 'Planifié') : ?>
        <div class="d-flex gap-2 mb-4 flex-wrap">
            <a href="/admin/rdv/<?= $rendezVous['id'] ?>/edit" class="btn btn-outline-secondary btn-sm">
                Modifier
            </a>
            <a href="/admin/rdv/<?= $rendezVous['id'] ?>/realise"
                class="btn btn-outline-success btn-sm"
                onclick="return confirm('Marquer ce rendez-vous comme réalisé ?')">
                Marquer réalisé
            </a>
            <a href="/admin/rdv/<?= $rendezVous['id'] ?>/non-honore"
                class="btn btn-outline-warning btn-sm"
                onclick="return confirm('Marquer ce rendez-vous comme non honoré ?')">
                Marquer non honoré
            </a>
            <a href="/admin/rdv/<?= $rendezVous['id'] ?>/annuler"
                class="btn btn-outline-danger btn-sm"
                onclick="return confirm('Annuler ce rendez-vous ?')">
                Annuler
            </a>
        </div>
    <?php endif; ?>

</div>