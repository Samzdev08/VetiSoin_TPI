<?php

/**
 * Fichier : detail.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Fiche detaillee d'un patient
 */
/** @var array|null $patient */
?>

<div class="container mt-4">

    <h1 class="h4 fw-bold mb-3">Détails du patient</h1>

    <a href="/patients" class="btn btn-link mb-3 px-0">← Retour à la liste</a>

    <div class="row g-4">

        <div class="col-lg-8">

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Informations</h5>

                    <div class="row g-2">
                        <div class="col-sm-6">
                            <div class="fw-semibold">Nom</div>
                            <div class="text-muted"><?= htmlspecialchars($patient['nom']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="fw-semibold">Prénom</div>
                            <div class="text-muted"><?= htmlspecialchars($patient['prenom']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="fw-semibold">Date de naissance</div>
                            <div class="text-muted">
                                <?= htmlspecialchars((new DateTime($patient['date_naissance']))->format('d-m-Y')) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="fw-semibold">Genre</div>
                            <div class="text-muted"><?= htmlspecialchars(ucfirst($patient['genre'][0])) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="fw-semibold">Numéro de dossier</div>
                            <div class="text-muted"><?= htmlspecialchars($patient['numero_dossier']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="fw-semibold">Statut</div>
                            <div class="text-muted"><?= htmlspecialchars($patient['statut']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="fw-semibold">Service</div>
                            <div class="text-muted"><?= htmlspecialchars($patient['service']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="fw-semibold">Chambre</div>
                            <div class="text-muted"><?= htmlspecialchars($patient['chambre'] ?? '-') ?></div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Réservations</h5>

                    <?php if (!empty($patient['reservations'])): ?>
                        <?php foreach ($patient['reservations'] as $reservation): ?>
                            <div class="card border mb-3">
                                <div class="card-body">
                                    <p class="mb-1">
                                        <span class="fw-semibold">Soignant :</span>
                                        <span class="text-muted">
                                            <?= htmlspecialchars($reservation['soignant_nom'] ?? '') ?>
                                            <?= htmlspecialchars($reservation['soignant_prenom'] ?? '') ?>
                                        </span>
                                    </p>
                                    <p class="mb-1">
                                        <span class="fw-semibold">Date de réservation :</span>
                                        <span class="text-muted">
                                            <?= htmlspecialchars((new DateTime($reservation['date_reservation']))->format('d-m-Y')) ?>
                                        </span>
                                    </p>
                                    <p class="mb-1">
                                        <span class="fw-semibold">Statut :</span>
                                        <span class="text-muted"><?= htmlspecialchars($reservation['reservation_statut'] ?? '') ?></span>
                                    </p>
                                    <p class="mb-0">
                                        <span class="fw-semibold">Commentaire :</span>
                                        <span class="text-muted"><?= htmlspecialchars($reservation['reservation_commentaire'] ?? '-') ?></span>
                                    </p>

                                    
                                    <a href="/reservations/<?= $reservation['id_reservation'] ?>" class="btn btn-outline-primary btn-sm mt-2">
                                        Voir la réservation
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Aucune réservation</p>
                    <?php endif; ?>

                </div>
            </div>

        </div>

    </div>

</div>