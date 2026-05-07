<?php

/**
 * Fichier : list.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Liste des articles (admin)
 */

$genres = [
    'Femme' => 'success',
    'Homme' => 'info',
    'Mixte' => 'secondary',
];
$genreActif = $_GET['genre'] ?? '';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Articles</h1>
        <div class="d-flex gap-2">
            <form action="/admin/articles" method="get">
                <button type="submit" name="genre" value=""
                    class="btn btn-sm <?= $genreActif === '' ? 'btn-dark' : 'btn-outline-dark' ?>">
                    Tous
                </button>
                <?php foreach ($genres as $g => $couleur) : ?>
                    <button type="submit" name="genre" value="<?= $g ?>"
                        class="btn btn-sm <?= $genreActif === $g ? 'btn-dark' : 'btn-outline-dark' ?>">
                        <?= $g ?>
                    </button>
                <?php endforeach; ?>

                <button type="submit" name="stock_bas" value="1"
                    class="btn btn-sm <?= isset($_GET['stock_bas']) ? 'btn-warning' : 'btn-outline-warning' ?>">
                    Stock bas
                </button>
            </form>


            <a href="/admin/articles/create" class="btn btn-sm btn-dark">+ Ajouter</a>
        </div>
    </div>

    <?php if (empty($articles)) : ?>
        <div class="alert alert-info">Aucun article trouvé.</div>
    <?php else : ?>
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Photo</th>
                    <th>Nom</th>
                    <th>Marque</th>
                    <th>Genre</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article) : ?>
                    <tr>
                        <td>
                            <?php if (!empty($article['photo'])) : ?>
                                <img src="<?= htmlspecialchars($article['photo']) ?>"
                                    alt="<?= htmlspecialchars($article['nom']) ?>"
                                    width="50" height="50"
                                    style="object-fit: cover; border-radius: 4px;">
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($article['nom']) ?></td>
                        <td><?= htmlspecialchars($article['marque']) ?></td>
                        <td>
                            <span class="badge bg-<?= $genres[$article['genre']] ?? 'secondary' ?>">
                                <?= htmlspecialchars($article['genre']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="/admin/articles/<?= $article['id'] ?>" class="btn btn-outline-primary btn-sm">Voir</a>
                            <a href="/admin/articles/<?= $article['id'] ?>/edit" class="btn btn-outline-secondary btn-sm">Modifier</a>
                            <form action="/admin/articles/<?= $article['id'] ?>/delete" method="POST" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Supprimer cet article ?')">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>