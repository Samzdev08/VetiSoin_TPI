<?php
/**
 * Fichier : show.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Détail d'un article (admin)
 */

/** @var array $article */
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0"><?= htmlspecialchars($article['nom']) ?></h1>
        <div class="d-flex gap-2">
            <a href="/admin/articles/<?= $article['id'] ?>/edit" class="btn btn-sm btn-outline-secondary">Modifier</a>
            <a href="/admin/articles" class="btn btn-sm btn-outline-dark">Retour</a>
        </div>
    </div>

    <table class="table table-bordered mb-4" style="max-width: 400px;">
        <tr><th>Marque</th><td><?= htmlspecialchars($article['marque']) ?></td></tr>
        <tr><th>Genre</th><td><?= htmlspecialchars($article['genre']) ?></td></tr>
        <tr><th>Matière</th><td><?= htmlspecialchars($article['matiere']) ?></td></tr>
        <tr><th>Catégorie</th><td><?= htmlspecialchars($article['categorie']) ?></td></tr>
    </table>

    <h2 class="h5 mb-3">Variantes</h2>

    <?php if (empty($article['variantes'])) : ?>
        <div class="alert alert-info">Aucune variante disponible.</div>
    <?php else : ?>
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Photo</th>
                    <th>Taille</th>
                    <th>Couleur</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($article['variantes'] as $variante) : ?>
                    <tr>
                        <td>
                            <?php if (!empty($variante['photo'])) : ?>
                                <img src="<?= htmlspecialchars($variante['photo']) ?>"
                                     alt="photo"
                                     width="50" height="50"
                                     style="object-fit: cover; border-radius: 4px;">
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($variante['taille']) ?></td>
                        <td><?= htmlspecialchars($variante['couleur']) ?></td>
                        <td><?= htmlspecialchars($variante['stock']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>