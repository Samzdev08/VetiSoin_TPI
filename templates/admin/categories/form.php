<?php
/**
 * Fichier : form.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Formulaire ajout/edition catégorie (admin)
 */

/** @var int|null $id */

$typesTailles = ['habit', 'chaussure', 'unique'];
?>

<div class="container mt-4">

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <h1 class="h4 mb-4 fw-bold">
                
                <?= $id ? 'Modifier la catégorie' : 'Ajouter une catégorie' ?>
            </h1>

            <form action="<?= $id ? "/admin/categories/$id/edit" : "/admin/categories/create" ?>" method="post" novalidate >
                
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" id="nom" name="nom" 
                           class="form-control"
                           value="<?= htmlspecialchars($categorie['nom'] ?? '') ?>" required>
                </div>

               
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" 
                              class="form-control" rows="3"><?= htmlspecialchars($categorie['description'] ?? '') ?></textarea>
                </div>

                
                <div class="mb-4">
                    <label for="type_taille" class="form-label">Type de taille</label>
                    <select id="type_taille" name="type_taille" class="form-select" required>
                        <option value="">Sélectionnez un type</option>

                        <?php foreach ($typesTailles as $t) : ?>
                            <option value="<?= $t ?>" 
                                <?= (isset($categorie['type_taille']) && $categorie['type_taille'] === $t) ? 'selected' : '' ?>>
                                <?= ucfirst($t) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>

                    <div class="form-text mt-2">
                        <strong>Habit</strong> : XS, S, M, L, XL, XXL — 
                        <strong>Chaussure</strong> : 36 à 46 — 
                        <strong>Unique</strong> : taille unique
                    </div>
                </div>

               
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <?= $id ? 'Mettre à jour' : 'Ajouter' ?>
                    </button>

                    <a href="/admin/categories" class="btn btn-outline-secondary">
                        Annuler
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>