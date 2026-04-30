<?php

/**
 * Fichier : edit.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Modification d'un article et de ses variantes (admin)
 */

/** @var array $article */
/** @var array $categories*/

$genres = ['Femme', 'Homme', 'Mixte'];

?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Modifier <?= htmlspecialchars($article['nom']) ?></h1>
        <a href="/admin/articles/<?= $article['id'] ?>" class="btn btn-sm btn-outline-dark">Retour</a>
    </div>

    <h2 class="h5 mb-3">Informations de l'article</h2>
    <form action="/admin/articles/<?= $article['id'] ?>/edit" method="post" class="mb-5">

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <table class="table table-bordered" style="max-width: 500px;">
            <tr>
                <th>Nom</th>
                <td><input type="text" name="nom" value="<?= htmlspecialchars($article['nom']) ?>" class="form-control" required></td>
            </tr>
            <tr>
                <th>Marque</th>
                <td><input type="text" name="marque" value="<?= htmlspecialchars($article['marque']) ?>" class="form-control" required></td>
            </tr>
            <tr>
                <th>Matière</th>
                <td><input type="text" name="matiere" value="<?= htmlspecialchars($article['matiere']) ?>" class="form-control" required></td>
            </tr>
            <tr>
                <th>Genre</th>
                <td>
                    <select name="genre" class="form-select">
                        <?php foreach ($genres as $g) : ?>
                            <option value="<?= $g ?>" <?= $article['genre'] === $g ? 'selected' : '' ?>><?= $g ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Catégorie</th>
                <td>
                    <select name="id_categorie" id="select-categorie" class="form-select" onchange="setTaille()">
                        <option value="">— Choisir —</option>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?= $cat['id'] ?>"
                                data-nom="<?= htmlspecialchars($cat['type_taille']) ?>"
                                <?= $article['id_categorie'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
        <button type="submit" class="btn btn-dark">Enregistrer</button>
    </form>

    <h2 class="h5 mb-3">Variantes</h2>
    <?php if (empty($article['variantes'])) : ?>
        <div class="alert alert-info">Aucune variante.</div>
    <?php else : ?>
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Photo</th>
                    <th>Taille</th>
                    <th>Couleur</th>
                    <th>Stock</th>
                    <th>Nouvelle photo</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($article['variantes'] as $variante) : ?>
                    <tr>
                        <form action="/admin/variantes/<?= $variante['id'] ?>/edit" method="post" enctype="multipart/form-data">

                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                            <td>
                                <?php if (!empty($variante['photo'])) : ?>
                                    <img src="<?= htmlspecialchars($variante['photo']) ?>"
                                        alt="photo" width="50" height="50"
                                        style="object-fit: cover; border-radius: 4px;">
                                <?php else : ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td>
                                <select name="taille" class="form-select select-taille" data-taille="<?= htmlspecialchars($variante['taille']) ?>">
                                    <option value="">—</option>
                                </select>
                            </td>
                            <td><?= htmlspecialchars($variante['couleur']) ?></td>
                            <td>
                                <input type="number" name="stock" value="<?= htmlspecialchars($variante['stock']) ?>"
                                    min="0" class="form-control" style="max-width: 100px;">
                            </td>
                            <td>
                                <input type="file" name="photo" accept="image/*" class="form-control">
                            </td>
                            <td class="text-center">
                                <button type="submit" class="btn btn-outline-primary btn-sm">Enregistrer</button>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<script>
    const tailles = {
        habit: ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
        chaussure: ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46'],
        unique: ['Unique']
    };

    function setTaille() {
        const selectCat = document.getElementById('select-categorie');
        const type = selectCat.options[selectCat.selectedIndex].dataset.nom;
        if (!type || !tailles[type]) return;

        document.querySelectorAll('.select-taille').forEach(select => {
            const tailleActuelle = select.dataset.taille;
            select.innerHTML = '<option value="">—</option>';
            tailles[type].forEach(taille => {
                const option = document.createElement('option');
                option.value = taille;
                option.textContent = taille;
                if (taille === tailleActuelle) option.selected = true;
                select.appendChild(option);
            });
        });
    }

    window.addEventListener('DOMContentLoaded', setTaille);
</script>