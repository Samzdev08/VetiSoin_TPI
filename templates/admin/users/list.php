<?php

/**
 * Fichier : list.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Liste des comptes soignants
 */
?>
<?php
/**
 * Fichier : list.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Liste des soignants (admin)
 */

$badges = [
    'Actif'    => 'success',
    'Inactif'  => 'secondary',
];

?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Soignants</h1>
        <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Soignants</h1>
    <a href="/admin/soignants/create" class="btn btn-sm btn-dark">+ Ajouter</a>
</div>
    </div>

    <?php if (empty($soignants)) : ?>
        <div class="alert alert-info">Aucun soignant trouvé.</div>
    <?php else : ?>
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Service</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($soignants as $soignant) : ?>
                    <tr>
                        <td><?= htmlspecialchars($soignant['nom']) ?></td>
                        <td><?= htmlspecialchars($soignant['prenom']) ?></td>
                        <td><?= htmlspecialchars($soignant['email']) ?></td>
                        <td><?= htmlspecialchars($soignant['service']) ?></td>
                        <td><?= htmlspecialchars($soignant['role']) ?></td>
                        <td>
                            <span class="badge bg-<?= $badges[$soignant['statut']] ?? 'secondary' ?>">
                                <?= htmlspecialchars($soignant['statut']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="/admin/soignants/<?= $soignant['id'] ?>/edit" class="btn btn-outline-secondary btn-sm">Modifier</a>

                            <?php if ($soignant['role'] === 'Soignant') : ?>
                                <a href="/admin/soignants/<?= $soignant['id'] ?>/reset-password"
                                    class="btn btn-outline-warning btn-sm"
                                    onclick="return confirm('Réinitialiser le mot de passe de ce soignant ?')">
                                    Reset MDP
                                </a>
                            <?php endif; ?>

                            <?php if ($_SESSION['user_id'] == $soignant['id'] ): ?>
                                <span class="text-muted">—</span>
                            <?php else : ?>
                                <?php if ($soignant['statut'] === 'Actif') : ?>
                                    <a href="/admin/soignants/<?= $soignant['id'] ?>/toggle"
                                        class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Rendre ce soignant inactif ?')">
                                        Désactiver
                                    </a>
                                <?php else : ?>
                                    <a href="/admin/soignants/<?= $soignant['id'] ?>/toggle"
                                        class="btn btn-outline-success btn-sm"
                                        onclick="return confirm('Réactiver ce soignant ?')">
                                        Activer
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>