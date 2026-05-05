<?php
/**
 * Fichier : list.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Liste et recherche de patients
 */
/** @var int|null $id */
/** @var array|null $patients */
?>

<div class="container mt-4">

    <h1 class="h4 fw-bold mb-3">Liste des patients</h1>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <form method="GET" action="/patients">
                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label for="nom" class="form-label fw-semibold">Nom ou prénom</label>
                        <input type="text" id="nom" name="nom"
                               class="form-control"
                               placeholder="Nom ou prénom"
                               onchange="this.form.submit()"
                               value="<?= htmlspecialchars($_GET['nom'] ?? '') ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="numeroDossier" class="form-label fw-semibold">Numéro de dossier</label>
                        <input type="text" id="numeroDossier" name="numeroDossier"
                               class="form-control"
                               placeholder="Numéro de dossier"
                               onchange="this.form.submit()"
                               value="<?= htmlspecialchars($_GET['numeroDossier'] ?? '') ?>">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Rechercher</button>
                    </div>

                    <div class="col-md-2">
                        <a href="/patients" class="btn btn-secondary w-100">Réinitialiser</a>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <a href="/patient/form" class="btn btn-primary">Ajouter un patient</a>
    </div>

    <?php if ($patients): ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th>N° DOSSIER</th>
                                <th>NOM</th>
                                <th>PRÉNOM</th>
                                <th>NAISSANCE</th>
                                <th>GENRE</th>
                                <th>CHAMBRE</th>
                                <th>SERVICE</th>
                                <th>STATUT</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($patients as $patient): ?>
                                <tr data-id="<?= $patient['id'] ?>" style="cursor: pointer;">
                                    <td><?= htmlspecialchars($patient['numero_dossier']) ?></td>
                                    <td><?= htmlspecialchars($patient['nom']) ?></td>
                                    <td><?= htmlspecialchars($patient['prenom']) ?></td>
                                    <td><?= htmlspecialchars((new DateTime($patient['date_naissance']))->format('d-m-Y')) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($patient['genre'][0])) ?></td>
                                    <td><?= htmlspecialchars($patient['chambre'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($patient['service']) ?></td>
                                    <td><?= htmlspecialchars($patient['statut']) ?></td>
                                    <td>
                                        <?php if ($patient['statut'] === 'Hospitalisé'): ?>
                                            <a href="/patient/<?= $patient['id'] ?>" class="btn btn-sm btn-primary">Voir plus</a>
                                            <a href="/patient/form/<?= $patient['id'] ?>/edit" class="btn btn-sm btn-secondary">Modifier</a>
                                        <?php else: ?>
                                            <a href="/patient/<?= $patient['id'] ?>" class="btn btn-sm btn-secondary">Historique</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php else: ?>
        <p class="text-muted">Aucun patient trouvé.</p>
    <?php endif; ?>

</div>