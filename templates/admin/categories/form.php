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

<h1><?= $id ? 'Modifier la catégorie' : 'Ajouter une catégorie' ?></h1>

<form action="<?= $id ? "/admin/categories/$id/edit" : "/admin/categories/create" ?>" method="post" novalidate>
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" class="form-control" required
               value="<?= htmlspecialchars($categorie['nom'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" class="form-control" rows="3"><?= htmlspecialchars($categorie['description'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="type_taille">Type de taille</label>
        <select id="type_taille" name="type_taille" class="form-control" required>
            <option value="">Sélectionnez un type</option>
            <?php foreach ($typesTailles as $t) : ?>
                <option value="<?= $t ?>" <?= (isset($categorie['type_taille']) && $categorie['type_taille'] === $t) ? 'selected' : '' ?>>
                    <?= ucfirst($t) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small class="text-muted">
            <strong>Habit</strong> : XS, S, M, L, XL, XXL — 
            <strong>Chaussure</strong> : 36 à 46 — 
            <strong>Unique</strong> : taille unique
        </small>
    </div>

    <button type="submit" class="btn btn-primary"><?= $id ? 'Mettre à jour' : 'Ajouter' ?></button>
    <a href="/admin/categories" class="btn btn-secondary">Annuler</a>
</form>