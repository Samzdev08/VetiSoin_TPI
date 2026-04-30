<?php

/**
 * Fichier : list.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Liste des reservations du soignant
 */
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Mes Réservations</h1>
        <form action="/reservations" method="get" class="mb-3">
            <?php
            $statuts = ['En attente', 'Confirmée', 'Clôturée', 'Annulée'];
            $statutActif = $_GET['statut'] ?? '';
            ?>
            <button type="submit" name="statut" value=""
                class="btn btn-sm <?= $statutActif === '' ? 'btn-dark' : 'btn-outline-dark' ?>">
                Tous
            </button>
            <?php foreach ($statuts as $s) : ?>
                <button type="submit" name="statut" value="<?= $s ?>"
                    class="btn btn-sm <?= $statutActif === $s ? 'btn-dark' : 'btn-outline-dark' ?>">
                    <?= $s ?>
                </button>
            <?php endforeach; ?>
        </form>
    </div>

    <?php if (empty($reservations)) : ?>
        <div class="alert alert-info">Aucune réservation trouvée.</div>
    <?php else : ?>
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Patient</th>
                    <th>Date de retrait prévue</th>
                    <th>Statut</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation) : ?>
                    <tr>
                        <td><?= htmlspecialchars($reservation['patient_nom'] . ' ' . $reservation['patient_prenom']) ?></td>
                        <td><?= htmlspecialchars($reservation['date_retrait_effective'] ?? '—') ?></td>
                        <td>
                            <?php
                            $badges = [
                                'En attente' => 'warning',
                                'Confirmée'  => 'success',
                                'Clôturée'   => 'secondary',
                                'Annulée'    => 'danger',
                            ];
                            $couleur = $badges[$reservation['statut']];
                            ?>
                            <span class="badge bg-<?= $couleur ?>">
                                <?= htmlspecialchars($reservation['statut']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="/reservations/<?= $reservation['id'] ?>" class="btn btn-outline-primary btn-sm">Voir</a>

                            <?php if ($reservation['statut'] === 'En attente') : ?>
                                <a href="/reservations/<?= $reservation['id'] ?>/updateForm" class="btn btn-outline-secondary btn-sm">Modifier</a>
                                <a href="/reservations/<?= $reservation['id'] ?>/annuler"
                                    class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                    Annuler
                                </a>
                                <?php elseif ($reservation['statut'] === 'Confirmée') : ?>

                                    <button class="btn btn-outline-secondary btn-sm" disabled>Modifier</button>
                                <?php elseif ($reservation['statut'] === 'Clôturée') : ?>
                                    <button class="btn btn-outline-success btn-sm" disabled>Retrait confirmer</button>
                                <?php elseif ($reservation['statut'] === 'Annulée') : ?>
                                    <button class="btn btn-outline-secondary btn-sm" disabled style="cursor: not-allowed;">Modifier</button>
                            <?php endif; ?>

                            <?php if ($reservation['is_archived']) : ?>
                                <button class="btn btn-outline-secondary btn-sm">Historique</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>