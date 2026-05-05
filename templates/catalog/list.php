<?php

/**
 * Fichier : list.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Catalogue d'articles avec filtres
 */
/** @var array $articles */
/** @var array $couleurs */
/** @var string | null $title */
$flash = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);
?>

<link href="../assets/css/catalogue.css" rel="stylesheet">

<div class="d-flex" style="min-width: 0;">

    <aside class="sidebar flex-shrink-0">
        <form action="/catalogue" method="GET" id="filtreForm">

            <input type="hidden" name="genre" id="genre-hidden" value="<?= htmlspecialchars($_GET['genre']     ?? '') ?>">
            <input type="hidden" name="taille" id="taille-hidden" value="<?= htmlspecialchars($_GET['taille']    ?? '') ?>">
            <input type="hidden" name="categorie" id="categorie-hidden" value="<?= htmlspecialchars($_GET['categorie'] ?? '') ?>">

            <div class="mb-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text" style="background-color: #E8F3FA; border-color: #E2E5EA; color: #1A5C8A;">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="recherche" class="form-control form-control-sm"
                        placeholder="Rechercher..."
                        value="<?= htmlspecialchars($_GET['recherche'] ?? '') ?>"
                        onchange="this.form.submit()">
                </div>
            </div>

            <p class="sidebar-title">Catégorie</p>
            <button type="submit" class="btn-filtre <?= empty($_GET['categorie'])                        ? 'actif' : '' ?>" onclick="setValue('categorie-hidden', '')">Toutes</button>
            <button type="submit" class="btn-filtre <?= ($_GET['categorie'] ?? '') === '1'  ? 'actif' : '' ?>" onclick="setValue('categorie-hidden', '1')">Blouses</button>
            <button type="submit" class="btn-filtre <?= ($_GET['categorie'] ?? '') === '2'  ? 'actif' : '' ?>" onclick="setValue('categorie-hidden', '2')">Pantalons</button>
            <button type="submit" class="btn-filtre <?= ($_GET['categorie'] ?? '') === '3'  ? 'actif' : '' ?>" onclick="setValue('categorie-hidden', '3')">Tuniques</button>
            <button type="submit" class="btn-filtre <?= ($_GET['categorie'] ?? '') === '4'  ? 'actif' : '' ?>" onclick="setValue('categorie-hidden', '4')">Casaques</button>
            <button type="submit" class="btn-filtre <?= ($_GET['categorie'] ?? '') === '5'  ? 'actif' : '' ?>" onclick="setValue('categorie-hidden', '5')">Chaussures</button>
            <button type="submit" class="btn-filtre <?= ($_GET['categorie'] ?? '') === '6'  ? 'actif' : '' ?>" onclick="setValue('categorie-hidden', '6')">Coiffes</button>
            <button type="submit" class="btn-filtre <?= ($_GET['categorie'] ?? '') === '7'  ? 'actif' : '' ?>" onclick="setValue('categorie-hidden', '7')">Vestes &amp; Polaires</button>

            <p class="sidebar-title">Genre</p>
            <button type="submit" class="btn-filtre <?= empty($_GET['genre'])                            ? 'actif' : '' ?>" onclick="setValue('genre-hidden', '')">Tous</button>
            <button type="submit" class="btn-filtre <?= ($_GET['genre'] ?? '') === 'Homme'  ? 'actif' : '' ?>" onclick="setValue('genre-hidden', 'Homme')">Homme</button>
            <button type="submit" class="btn-filtre <?= ($_GET['genre'] ?? '') === 'Femme'  ? 'actif' : '' ?>" onclick="setValue('genre-hidden', 'Femme')">Femme</button>
            <button type="submit" class="btn-filtre <?= ($_GET['genre'] ?? '') === 'Mixte'  ? 'actif' : '' ?>" onclick="setValue('genre-hidden', 'Mixte')">Mixte</button>

            <p class="sidebar-title">Taille</p>
            <button type="submit" class="btn-filtre <?= empty($_GET['taille'])                          ? 'actif' : '' ?>" onclick="setValue('taille-hidden', '')">Toutes</button>
            <button type="submit" class="btn-filtre <?= ($_GET['taille'] ?? '') === 'XS'    ? 'actif' : '' ?>" onclick="setValue('taille-hidden', 'XS')">XS</button>
            <button type="submit" class="btn-filtre <?= ($_GET['taille'] ?? '') === 'S'     ? 'actif' : '' ?>" onclick="setValue('taille-hidden', 'S')">S</button>
            <button type="submit" class="btn-filtre <?= ($_GET['taille'] ?? '') === 'M'     ? 'actif' : '' ?>" onclick="setValue('taille-hidden', 'M')">M</button>
            <button type="submit" class="btn-filtre <?= ($_GET['taille'] ?? '') === 'L'     ? 'actif' : '' ?>" onclick="setValue('taille-hidden', 'L')">L</button>
            <button type="submit" class="btn-filtre <?= ($_GET['taille'] ?? '') === 'XL'    ? 'actif' : '' ?>" onclick="setValue('taille-hidden', 'XL')">XL</button>
            <button type="submit" class="btn-filtre <?= ($_GET['taille'] ?? '') === 'XXL'   ? 'actif' : '' ?>" onclick="setValue('taille-hidden', 'XXL')">XXL</button>

            <p class="sidebar-title">Couleur</p>
            <select name="couleur" id="couleur" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Toutes</option>
                <?php foreach ($couleurs as $couleur): ?>
                    <option value="<?= htmlspecialchars($couleur) ?>"
                        <?= ($_GET['couleur'] ?? '') === $couleur ? 'selected' : '' ?>>
                        <?= htmlspecialchars($couleur) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <a href="/catalogue" class="lien-reset">
                <i class="bi bi-x-circle me-1"></i>Réinitialiser les filtres
            </a>

        </form>
    </aside>


    <div class="main-content" style="flex: 1 1 0; min-width: 0; overflow-x: clip;">
        <h1 class="page-titre">Catalogue</h1>

        <?php if ($articles): ?>
            <div class="articles-grid">
                <?php foreach ($articles as $article): ?>
                    <div class="article-card " data-id="<?= $article['id'] ?>">
                        <img src="../<?= htmlspecialchars($article['photo']) ?>"
                            alt="<?= htmlspecialchars($article['nom']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($article['nom']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($article['marque']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="pagination" class="mt-4 d-flex justify-content-center"></div>

        <?php else: ?>
            <div class="d-flex flex-column align-items-center justify-content-center h-75"
                style="min-height: 400px; color: #9aa0af;">
                <i class="bi bi-box-seam fs-1 mb-3"></i>
                <p class="mb-0">Aucun article trouvé pour ces filtres.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/catalogue.js"></script>