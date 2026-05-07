<?php

/**
 * Fichier : list.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Liste des rendez-vous (admin) avec filtres
 */
/** @var array $rendezVous */
/** @var array $soignants */

$badges = [
    'Planifié'   => 'primary',
    'Réalisé'    => 'success',
    'Annulé'     => 'danger',
    'Non honoré' => 'secondary',
];

$statuts  = ['Planifié', 'Réalisé', 'Annulé', 'Non honoré'];
$services = ['Urgences', 'Chirurgie', 'Médecine interne'];

$statutActif   = $_GET['statut']   ?? '';
$soignantActif = $_GET['soignant'] ?? '';
$serviceActif  = $_GET['service']  ?? '';
$dateActive    = $_GET['date']     ?? '';
?>

<div class="container mt-4">
    <h1 class="h3 mb-3">Calendrier des rendez-vous</h1>

    <form action="/admin/rdv" method="get" class="card card-body mb-3">
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
                        <option value="<?= $s['id'] ?>" <?= (string)$soignantActif === (string)$s['id'] ? 'selected' : '' ?>>
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
                <label class="form-label small text-muted mb-1">Date</label>
                <input type="date" name="date" value="<?= htmlspecialchars($dateActive) ?>" class="form-control form-control-sm">
            </div>
        </div>

        <div class="mt-2 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-dark">Filtrer</button>
            <a href="/admin/rdv" class="btn btn-sm btn-outline-secondary">Réinitialiser</a>
        </div>
    </form>

    <?php if (empty($rendezVous)) : ?>
        <div class="alert alert-info">Aucun rendez-vous trouvé.</div>
    <?php else : ?>
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Lieu</th>
                    <th>Patient</th>
                    <th>Service</th>
                    <th>Soignant</th>
                    <th>Statut</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rendezVous as $rdv) : ?>
                    <tr>
                        <td><?= htmlspecialchars(date('d.m.Y', strtotime($rdv['date_rdv']))) ?></td>
                        <td><?= htmlspecialchars(substr($rdv['heure_rdv'], 0, 5)) ?></td>
                        <td><?= htmlspecialchars($rdv['lieu']) ?></td>
                        <td><?= htmlspecialchars($rdv['patient_nom'] . ' ' . $rdv['patient_prenom']) ?></td>
                        <td><?= htmlspecialchars($rdv['patient_service']) ?></td>
                        <td><?= htmlspecialchars($rdv['soignant_nom'] . ' ' . $rdv['soignant_prenom']) ?></td>
                        <td>
                            <span class="badge bg-<?= $badges[$rdv['statut']] ?? 'secondary' ?>">
                                <?= htmlspecialchars($rdv['statut']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="/admin/rdv/<?= $rdv['id'] ?>" class="btn btn-outline-primary btn-sm">Voir</a>

                            <?php if ($rdv['statut'] === 'Planifié') : ?>
                                <a href="/admin/rdv/<?= $rdv['id'] ?>/edit" class="btn btn-outline-secondary btn-sm">Modifier</a>
                                <form action="/admin/rdv/<?= $rdv['id'] ?>/realise" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <button type="submit" class="btn btn-outline-success btn-sm"
                                            onclick="return confirm('Marquer ce rendez-vous comme réalisé ?')">
                                        Réalisé
                                    </button>
                                </form>
                                <form action="/admin/rdv/<?= $rdv['id'] ?>/non-honore" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <button type="submit" class="btn btn-outline-warning btn-sm"
                                            onclick="return confirm('Marquer ce rendez-vous comme non honoré ?')">
                                        Non honoré
                                    </button>
                                </form>
                                <form action="/admin/rdv/<?= $rdv['id'] ?>/annuler" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Annuler ce rendez-vous ?')">
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