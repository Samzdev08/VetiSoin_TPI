<?php
/**
 * Fichier : list.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Liste des catégories (admin)
 */
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Catégories</h1>
        <a href="/admin/categories/create" class="btn btn-sm btn-dark">+ Ajouter</a>
    </div>

    <?php if (empty($categories)) : ?>
        <div class="alert alert-info">Aucune catégorie trouvée.</div>
    <?php else : ?>
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Type de taille</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $categorie) : ?>
                    <tr>
                        <td><?= htmlspecialchars($categorie['nom']) ?></td>
                        <td><?= htmlspecialchars($categorie['description'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($categorie['type_taille']) ?></td>
                        <td class="text-center">
                            <a href="/admin/categories/<?= $categorie['id'] ?>/edit" class="btn btn-outline-secondary btn-sm">Modifier</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>