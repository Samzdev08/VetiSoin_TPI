<?php

/**
 * Fichier : detail.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Détail d'une réservation (admin)
 */
/** @var array $reservations */

$row = $reservations[0];

$badges = [
    'En attente' => 'warning',
    'Confirmée'  => 'success',
    'Clôturée'   => 'secondary',
    'Annulée'    => 'danger',
];
$couleur = $badges[$row['statut']] ?? 'secondary';
?>

<div class="container mt-4" style="max-width: 880px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Réservation #<?= $row['id'] ?></h1>
        <span class="badge bg-<?= $couleur ?> fs-6"><?= htmlspecialchars($row['statut']) ?></span>
    </div>

    <a href="/admin/reservations" class="btn btn-sm btn-outline-secondary mb-4">← Retour</a>

    
    <div class="card mb-3">
        <div class="card-body">
            <h6 class="card-subtitle text-muted mb-2">Patient</h6>
            <p class="mb-1 fw-semibold">
                <?= htmlspecialchars($row['patient_nom'] . ' ' . $row['patient_prenom']) ?>
            </p>
            <p class="mb-0 text-muted small">
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
            <table class="table table-sm table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Article</th>
                        <th>Taille</th>
                        <th>Couleur</th>
                        <th>Qté</th>
                        <th>Retour</th>
                        <?php if ($row['statut'] === 'Clôturée') : ?>
                            <th class="text-center">Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $ligne) : ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($ligne['article_nom']) ?>
                                <span class="text-muted small d-block">
                                    <?= htmlspecialchars($ligne['marque'] ?? '') ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($ligne['taille']) ?></td>
                            <td><?= htmlspecialchars($ligne['couleur']) ?></td>
                            <td><?= htmlspecialchars($ligne['quantite']) ?></td>

                            
                            <td>
                                <?php if ($ligne['est_retourne']) : ?>
                                    <span class="badge bg-success">
                                        Retourné
                                        <?= $ligne['date_retour'] ? ' ' . htmlspecialchars(date('d.m.Y', strtotime($ligne['date_retour']))) : '' ?>
                                    </span>
                                <?php elseif ($ligne['retour_demande']) : ?>
                                    <span class="badge bg-warning text-dark">Retour demandé</span>
                                <?php else : ?>
                                    <span class="badge bg-secondary">Non retourné</span>
                                <?php endif; ?>
                            </td>

                           
                            <?php if ($row['statut'] === 'Clôturée') : ?>
                                <td class="text-center">
                                    <?php if ($ligne['retour_demande'] && !$ligne['est_retourne']) : ?>
                                        <a href="/admin/reservations/<?= $row['id'] ?>/items/<?= $ligne['article_reserve_id'] ?>/retour"
                                            class="btn btn-outline-success btn-sm"
                                            onclick="return confirm('Valider le retour de cet article ?')">
                                            Valider retour
                                        </a>
                                    <?php else : ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <?php if (!empty($row['commentaire'])) : ?>
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Commentaire du soignant</h6>
                <p class="mb-0 small"><?= htmlspecialchars($row['commentaire']) ?></p>
            </div>
        </div>
    <?php endif; ?>

    
    <div class="d-flex gap-2 mb-4">

        <?php if ($row['statut'] !== 'Annulée' && $row['statut'] !== 'Clôturée') : ?>
            <a href="/admin/reservations/<?= $row['id'] ?>/annuler"
                class="btn btn-outline-danger btn-sm"
                onclick="return confirm('Annuler cette réservation ? Le stock sera restauré.')">
                Annuler la réservation
            </a>
        <?php endif; ?>
    </div>

</div>