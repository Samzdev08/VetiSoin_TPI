<?php

/**
 * Fichier : detail.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Détail d'une réservation
 */
/** @var array $reservations */



$row = $reservations[0];

$badges  = [
    'En attente' => 'warning',
    'Confirmée'  => 'success',
    'Clôturée'   => 'secondary',
    'Annulée'    => 'danger',
];
$couleur = $badges[$row['statut']];
?>

<div class="container mt-4" style="max-width: 780px;">


    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Réservation #<?= $row['id'] ?></h1>
        <span class="badge bg-<?= $couleur ?> fs-6"><?= htmlspecialchars($row['statut']) ?></span>
    </div>

    <a href="/reservations" class="btn btn-sm btn-outline-secondary mb-4">← Retour</a>
    <?php if($row['statut'] === 'En attente') : ?>
        <a href="/rdv/<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary mb-4">Prendre rdv</a>
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Patient</h6>
            <p class="mb-1 fw-semibold">
                <?= htmlspecialchars($row['patient_nom'] . ' ' . $row['patient_prenom']) ?>
            </p>
            <p class="mb-1 text-muted small">
                Dossier : <?= htmlspecialchars($row['numero_dossier']) ?>
                &nbsp;·&nbsp;
                Chambre : <?= htmlspecialchars($row['chambre']) ?>
                &nbsp;·&nbsp;
                <?= htmlspecialchars($row['patient_service']) ?>
            </p>
        </div>
    </div>


    <div class="card mb-3">
        <div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Dates</h6>
            <p class="mb-1 small">
                <span class="text-muted">Réservation créée le :</span>
                <?= htmlspecialchars($row['date_reservation'] ?? '—') ?>
            </p>
            <p class="mb-0 small">
                <span class="text-muted">Retrait prévu le :</span>
                <?= htmlspecialchars($row['date_retrait_effective'] ?? '—') ?>
            </p>
        </div>
    </div>


    <div class="card mb-3">
        <div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Articles réservés</h6>
            <table class="table table-sm table-borderless mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Article</th>
                        <th>Taille</th>
                        <th>Couleur</th>
                        <th>Qté</th>
                        <?php if ($row['statut'] === 'Cloturée') : ?>
                            <th>Actions</th>
                        <?php endif ?>
                        <th>Retourné</th>


                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $ligne) : ?>
                        <tr>
                            <td><?= htmlspecialchars($ligne['article_nom']) ?>
                                <span class="text-muted small">
                                    <?= htmlspecialchars($ligne['marque'] ?? '') ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($ligne['taille']) ?></td>
                            <td><?= htmlspecialchars($ligne['couleur']) ?></td>
                            <td><?= htmlspecialchars($ligne['quantite']) ?></td>
                            <?php if ($row['statut'] === 'Cloturée') : ?>
                                <td><button class="badge bg-danger">retourner</button></td>
                            <?php endif; ?>
                            <td>
                                <?php if ($ligne['est_retourne']) : ?>

                                    <span class="badge bg-success">Oui</span>
                                <?php else : ?>
                                    <span class="badge bg-secondary">Non</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!empty($row['commentaire'])) : ?>
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Commentaire</h6>
                <p class="mb-0 small"><?= htmlspecialchars($row['commentaire']) ?></p>
            </div>
        </div>
    <?php endif; ?>


    <div class="d-flex gap-2">
        <?php if ($row['statut'] === 'En attente') : ?>
            <a href="/reservations/<?= $row['id'] ?>/updateForm" class="btn btn-outline-secondary btn-sm">Modifier</a>
            <a href="/reservations/<?= $row['id'] ?>/annuler"
                class="btn btn-outline-danger btn-sm"
                onclick="return confirm('Annuler cette réservation ?')">
                Annuler
            </a>
        <?php endif; ?>
    </div>

</div>