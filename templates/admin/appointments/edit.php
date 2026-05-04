<?php

/**
 * Fichier : edit.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Modification d'un rendez-vous (admin)
 */
/** @var array $rendezVous */

$horaires = [
    '08:00:00' => '08:00',
    '10:00:00' => '10:00',
    '11:30:00' => '11:30',
    '14:30:00' => '14:30',
    '16:00:00' => '16:00',
];
$lieux = ['Vestiaire principal', 'Secrétariat'];
$dateActuelle  = $rendezVous['date_rdv'];
$heureActuelle = $rendezVous['heure_rdv'];
$lieuActuel    = $rendezVous['lieu'];
$dateMin = date('Y-m-d');
$dateMax = date('Y-m-d', strtotime('+7 days'));
?>
<div class="container mt-4" style="max-width: 600px;">
    <h1 class="h4 mb-3">Modifier le rendez-vous #<?= $rendezVous['id'] ?></h1>
    <a href="/admin/rdv" class="btn btn-sm btn-outline-secondary mb-4">← Retour</a>
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
            <p class="mb-0 text-muted small mt-1">
                Soignant : <?= htmlspecialchars($rendezVous['soignant_nom'] . ' ' . $rendezVous['soignant_prenom']) ?>
            </p>
        </div>
    </div>
    <form action="/admin/rdv/<?= $rendezVous['id'] ?>/edit" method="post" class="card card-body">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="mb-3">
            <label for="date_rdv" class="form-label">Date du rendez-vous</label>
            <input type="date"
                id="date_rdv"
                name="date_rdv"
                value="<?= htmlspecialchars($dateActuelle) ?>"
                min="<?= $dateMin ?>"
                max="<?= $dateMax ?>"
                class="form-control">
            <small class="text-muted">Date limite : <?= date('d.m.Y', strtotime($dateMax)) ?></small>
        </div>
        <div class="mb-3">
            <label for="heure_rdv" class="form-label">Heure du rendez-vous</label>
            <select id="heure_rdv" name="heure_rdv" class="form-select" required>
                <?php foreach ($horaires as $valeur => $affichage) : ?>
                    <option value="<?= $valeur ?>" <?= $heureActuelle === $valeur ? 'selected' : '' ?>>
                        <?= $affichage ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="lieu" class="form-label">Lieu de retrait</label>
            <select id="lieu" name="lieu" class="form-select" required>
                <?php foreach ($lieux as $l) : ?>
                    <option value="<?= $l ?>" <?= $lieuActuel === $l ? 'selected' : '' ?>>
                        <?= $l ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-dark">Enregistrer</button>
            <a href="/admin/rdv" class="btn btn-outline-secondary">Annuler</a>
        </div>
    </form>
</div>