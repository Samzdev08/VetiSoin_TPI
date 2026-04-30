<?php
/**
 * Fichier : form.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Formulaire ajout/edition soignant
 */
/** @var int|null $id */

?>

<h1><?= $id ? 'Modifier le soignant' : 'Ajouter un soignant' ?></h1>

<form action="<?= $id ? "/admin/soignants/$id/edit" : "/admin/soignants/create" ?>" method="post" novalidate>
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" class="form-control" required
               value="<?= htmlspecialchars($soignant['nom'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" class="form-control" required
               value="<?= htmlspecialchars($soignant['prenom'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control" required
               value="<?= htmlspecialchars($soignant['email'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="telephone">Téléphone</label>
        <input type="text" id="telephone" name="telephone" class="form-control"
               value="<?= htmlspecialchars($soignant['telephone'] ?? '') ?>">
    </div>

    <?php if (!$id) : ?>
        <div class="form-group">
            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required minlength="8">
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="service">Service</label>
        <select id="service" name="service" class="form-control" required>
            <option value="">Sélectionnez un service</option>
            <option value="Urgences" <?= (isset($soignant['service']) && $soignant['service'] === 'Urgences')          ? 'selected' : '' ?>>Urgences</option>
            <option value="Chirurgie" <?= (isset($soignant['service']) && $soignant['service'] === 'Chirurgie')         ? 'selected' : '' ?>>Chirurgie</option>
            <option value="Médecine interne" <?= (isset($soignant['service']) && $soignant['service'] === 'Médecine interne')  ? 'selected' : '' ?>>Médecine interne</option>
        </select>
    </div>


    <button type="submit" class="btn btn-primary"><?= $id ? 'Mettre à jour' : 'Ajouter' ?></button>
    <a href="/admin/soignants" class="btn btn-secondary">Annuler</a>
</form>