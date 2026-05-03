<?php

/**
 * Fichier : list.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Liste des rendez-vous du soignant
 */
/** @var array $rendezVous */
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Mes Rendez-vous</h1>
        <form action="/rdv" method="get" class="mb-3">
            <?php
            $statuts = ['Planifié', 'Réalisé', 'Annulé', 'Non honoré'];
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

    <?php if (empty($rendezVous)) : ?>
        <div class="alert alert-info">Aucun rendez-vous trouvé.</div>
    <?php else : ?>
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Lieu</th>
                    <th>Statut</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rendezVous as $rdv) : ?>
                    <tr>
                        <td><?= htmlspecialchars($rdv['patient_nom'] . ' ' . $rdv['patient_prenom']) ?></td>
                        <td><?= htmlspecialchars(date('d.m.Y', strtotime($rdv['date_rdv']))) ?></td>
                        <td><?= htmlspecialchars(substr($rdv['heure_rdv'], 0, 5)) ?></td>
                        <td><?= htmlspecialchars($rdv['lieu']) ?></td>
                        <td>
                            <?php
                            $badges = [
                                'Planifié'   => 'primary',
                                'Réalisé'    => 'success',
                                'Annulé'     => 'danger',
                                'Non honoré' => 'secondary',
                            ];
                            $couleur = $badges[$rdv['statut']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $couleur ?>">
                                <?= htmlspecialchars($rdv['statut']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="/rdv/<?= $rdv['id'] ?>/detail" class="btn btn-outline-primary btn-sm">Voir</a>
                            <a href="/reservations/<?= $rdv['id_reservation'] ?>" class="btn btn-outline-secondary btn-sm">Réservation</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>