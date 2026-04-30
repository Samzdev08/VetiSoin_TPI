<?php

/**
 * Fichier : create.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Création d'un article et de ses variantes (admin)
 */

/** @var array $article */
/** @var array $categories*/
/** @var string|null $categorySelect*/

$genres = ['Femme', 'Homme', 'Mixte'];

$tailles = [
    'habit'    => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
    'chaussure' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46'],
];

$categorySelect = 'salut';

?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Nouvel article</h1>
        <a href="/admin/articles" class="btn btn-sm btn-outline-dark">Retour</a>
    </div>

    <form action="/admin/articles/create" method="post" enctype="multipart/form-data">

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <h2 class="h5 mb-3">Informations de l'article</h2>
        <table class="table table-bordered" style="max-width: 500px;">
            <tr>
                <th>Nom</th>
                <td><input type="text" name="nom" class="form-control" required></td>
            </tr>
            <tr>
                <th>Marque</th>
                <td><input type="text" name="marque" class="form-control" required></td>
            </tr>
            <tr>
                <th>Matière</th>
                <td><input type="text" name="matiere" class="form-control" required></td>
            </tr>
            <tr>
                <th>Genre</th>
                <td>
                    <select name="genre" class="form-select" required>
                        <option value="">— Choisir —</option>
                        <?php foreach ($genres as $g) : ?>
                            <option value="<?= $g ?>"><?= $g ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Catégorie</th>
                <td>
                    <select name="id_categorie" id="select-categorie" class="form-select" onchange="setTaille(this.value)" required>
                        <option value="">— Choisir —</option>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?= $cat['id'] ?>" data-nom="<?= htmlspecialchars($cat['type_taille']) ?>">
                                <?= htmlspecialchars($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

        <h2 class="h5 mb-3">Variantes</h2>
        <table class="table table-bordered align-middle" id="variantes-table">
            <thead class="table-light">
                <tr>
                    <th>Taille</th>
                    <th>Couleur</th>
                    <th>Stock</th>
                    <th>Photo</th>
                </tr>
            </thead>
            <tbody id="variantes-body">
                <tr>
                    <td>
                        <select name="variantes[0][taille]" class="form-select select-taille" required>
                            <option value="">—</option>
                        </select>
                    </td>
                    <td><input type="text" name="variantes[0][couleur]" class="form-control" required></td>
                    <td><input type="number" name="variantes[0][stock]" min="0" value="0" class="form-control" style="max-width: 100px;" required></td>
                    <td><input type="file" name="variantes[0][photo]" accept="image/*" class="form-control"></td>
                </tr>
            </tbody>
        </table>

        <button type="button" onclick="ajouterVariante()" class="btn btn-outline-dark btn-sm mb-3">+ Ajouter une variante</button>


        <button type="submit" class="btn btn-dark">Créer l'article</button>
    </form>
</div>

<script>
    const tailles = {

        habit: ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
        chaussure: ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46'],
        unique: ['Unique']

    };

    function setTaille(idCategorie) {
        const selectCat = document.getElementById('select-categorie');
        const type = selectCat.options[selectCat.selectedIndex].dataset.nom;


        document.querySelectorAll('.select-taille').forEach(select => {
            select.innerHTML = '';
            tailles[type].forEach(taille => {
                const option = document.createElement('option');
                option.value = taille;
                option.textContent = taille;
                select.appendChild(option);
            });
        });
    }

    let i = 1;

    function ajouterVariante() {

        i++;

        const tbody = document.getElementById('variantes-body');
        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td>
                <select name="variantes[${i}][taille]" class="form-select select-taille" required>
                    <option value="">—</option>
                </select>
            </td>
            <td><input type="text" name="variantes[${i}][couleur]" class="form-control" required></td>
            <td><input type="number" name="variantes[${i}][stock]" min="0" value="0" class="form-control" style="max-width: 100px;" required></td>
            <td><input type="file" name="variantes[${i}][photo]" accept="image/*" class="form-control"></td>
            <td><button type="button" onclick="this.closest('tr').remove()" class="btn btn-outline-danger btn-sm">×</button></td>
        `;
        tbody.appendChild(tr);

        
        setTaille();

    }
</script>