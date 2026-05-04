<?php

/**
 * Fichier : form.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Page de gestion du profil du soignant connecté
 */
/** @var array $soignant */

$services = ['Urgences', 'Chirurgie', 'Médecine interne'];
?>

<div class="container mt-4" style="max-width: 720px;">

    <h1 class="h4 mb-4">Mon profil</h1>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Informations personnelles</h5>

            <form action="/profil/infos" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" id="nom" name="nom"
                            value="<?= htmlspecialchars($soignant['nom']) ?>"
                            class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label for="prenom" class="form-label">Prénom *</label>
                        <input type="text" id="prenom" name="prenom"
                            value="<?= htmlspecialchars($soignant['prenom']) ?>"
                            class="form-control" required>
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label for="email" class="form-label">E-mail *</label>
                    <input type="email" id="email" name="email"
                        value="<?= htmlspecialchars($soignant['email']) ?>"
                        class="form-control" required>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="service" class="form-label">Service *</label>
                        <select id="service" name="service" class="form-select" required>
                            <?php foreach ($services as $sv) : ?>
                                <option value="<?= $sv ?>" <?= $soignant['service'] === $sv ? 'selected' : '' ?>>
                                    <?= $sv ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone"
                            value="<?= htmlspecialchars($soignant['telephone'] ?? '') ?>"
                            class="form-control">
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    <button type="submit" class="btn btn-dark">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>


    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Changer le mot de passe</h5>

            <form action="/profil/password" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <div class="mb-3">
                    <label for="current_password" class="form-label">Mot de passe actuel *</label>
                    <input type="password" id="current_password" name="current_password"
                        class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">Nouveau mot de passe *</label>
                    <input type="password" id="new_password" name="new_password"
                        class="form-control" required
                        minlength="8"
                        pattern="(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}"
                        title="Au moins 8 caractères, 1 majuscule, 1 chiffre et 1 caractère spécial">
                    <small class="text-muted">
                        Au moins 8 caractères, 1 majuscule, 1 chiffre et 1 caractère spécial.
                    </small>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmation du nouveau mot de passe *</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                        class="form-control" required>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    <button type="submit" class="btn btn-dark">Modifier le mot de passe</button>
                </div>
            </form>
        </div>
    </div>

</div>