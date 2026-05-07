<?php

/**
 * Fichier : list.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Liste des réservations (admin) avec filtres
 */
/** @var array $reservations */
/** @var array $soignants */

$badges = [
    'En attente' => 'warning',
    'Confirmée'  => 'success',
    'Clôturée'   => 'secondary',
    'Annulée'    => 'danger',
];

$statuts  = ['En attente', 'Confirmée', 'Clôturée', 'Annulée'];
$services = ['Urgences', 'Chirurgie', 'Médecine interne'];

$statutActif   = $_GET['statut']   ?? '';
$soignantActif = $_GET['soignant'] ?? '';
$serviceActif  = $_GET['service']  ?? '';
$dateActive    = $_GET['date']     ?? '';
?>

<div class="container mt-4">
    <h1 class="h3 mb-3">Toutes les réservations</h1>

    <form action="/admin/reservations" method="get" class="card card-body mb-3">
        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Statut</label>
                <select name="statut" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <?php foreach ($statuts as $s) : ?>
                        <option value="<?= $s ?>" <?= $statutActif === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Soignant</label>
                <select name="soignant" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <?php foreach ($soignants as $s) : ?>
                        <option value="<?= $s['id'] ?>" <?= (int)$soignantActif === $s['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nom'] . ' ' . $s['prenom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Service patient</label>
                <select name="service" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <?php foreach ($services as $sv) : ?>
                        <option value="<?= $sv ?>" <?= $serviceActif === $sv ? 'selected' : '' ?>><?= $sv ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Date de retrait</label>
                <input type="date" name="date" value="<?= htmlspecialchars($dateActive) ?>" class="form-control form-control-sm">
            </div>
        </div>

        <div class="mt-2 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-dark">Filtrer</button>
            <a href="/admin/reservations" class="btn btn-sm btn-outline-secondary">Réinitialiser</a>
        </div>
    </form>

    <?php if (empty($reservations)) : ?>
        <div class="alert alert-info">Aucune réservation trouvée.</div>
    <?php else : ?>
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Patient</th>
                    <th>Service</th>
                    <th>Soignant</th>
                    <th>Date retrait</th>
                    <th>Statut</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $r) : ?>
                    <tr>
                        <td><?= htmlspecialchars($r['patient_nom'] . ' ' . $r['patient_prenom']) ?></td>
                        <td><?= htmlspecialchars($r['patient_service']) ?></td>
                        <td><?= htmlspecialchars($r['soignant_nom'] . ' ' . $r['soignant_prenom']) ?></td>
                        <td><?= htmlspecialchars($r['date_retrait_previsionelle'] ?? '—') ?></td>
                        <td>
                            <span class="badge bg-<?= $badges[$r['statut']] ?? 'secondary' ?>">
                                <?= htmlspecialchars($r['statut']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="/admin/reservations/<?= $r['id'] ?>" class="btn btn-outline-primary btn-sm">Voir</a>
                            <a href="/admin/reservations/<?= $r['id'] ?>/edit" class="btn btn-outline-secondary btn-sm">Modifier</a>

                            <?php if ($r['statut'] !== 'Annulée' && $r['statut'] !== 'Clôturée') : ?>
                                <form action="/admin/reservations/<?= $r['id'] ?>/annuler" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Annuler cette réservation ?')">
                                        Annuler
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>