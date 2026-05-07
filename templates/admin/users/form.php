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

<div class="container mt-4">

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <h1 class="h4 mb-4 fw-bold">
              
                <?= $id ? 'Modifier le soignant' : 'Ajouter un soignant' ?>
            </h1>

            <form action="<?= $id ? "/admin/soignants/$id/edit" : "/admin/soignants/create" ?>" method="post" novalidate>

                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" id="nom" name="nom" class="form-control"
                            value="<?= htmlspecialchars($soignant['nom'] ?? '') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" id="prenom" name="prenom" class="form-control"
                            value="<?= htmlspecialchars($soignant['prenom'] ?? '') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control"
                            value="<?= htmlspecialchars($soignant['email'] ?? '') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="text" id="telephone" name="telephone" class="form-control"
                            value="<?= htmlspecialchars($soignant['telephone'] ?? '') ?>">
                    </div>

                    <?php if (!$id) : ?>
                        <div class="col-12 mb-3">
                            <label for="mot_de_passe" class="form-label">Mot de passe</label>
                            <input type="password" id="mot_de_passe" name="mot_de_passe"
                                class="form-control" minlength="8">
                        </div>
                    <?php endif; ?>

                    <div class="col-md-6 mb-3">
                        <label for="service" class="form-label">Service</label>
                        <select id="service" name="service" class="form-select">
                            <option value="">Sélectionnez un service</option>
                            <option value="Urgences" <?= (isset($soignant['service']) && $soignant['service'] === 'Urgences') ? 'selected' : '' ?>>Urgences</option>
                            <option value="Chirurgie" <?= (isset($soignant['service']) && $soignant['service'] === 'Chirurgie') ? 'selected' : '' ?>>Chirurgie</option>
                            <option value="Médecine interne" <?= (isset($soignant['service']) && $soignant['service'] === 'Médecine interne') ? 'selected' : '' ?>>Médecine interne</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="role" class="form-label">Rôle</label>
                        <select id="role" name="role" class="form-select">
                            <option value="">Sélectionnez un rôle</option>
                            <option value="Administrateur" <?= (isset($soignant['role']) && $soignant['role'] === 'Administrateur') ? 'selected' : '' ?>>Administrateur</option>
                            <option value="Soignant" <?= (isset($soignant['role']) && $soignant['role'] === 'Soignant') ? 'selected' : '' ?>>Soignant</option>
                        </select>
                    </div>

                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        
                        <?= $id ? 'Mettre à jour' : 'Ajouter' ?>
                    </button>

                    <a href="/admin/soignants" class="btn btn-outline-secondary">
                        Annuler
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>