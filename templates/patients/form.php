<?php
/**
 * Fichier : form.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Formulaire creation/edition patient
 */
?>
<h1><?= $id ? 'Modifier le patient' : 'Ajouter un patient' ?></h1>
<form action="<?= $id ? "/patient/$id/update" : "/patient/add" ?>" method="post" novalidate>
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" class="form-control" required
               value="<?= htmlspecialchars($patient['nom'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" class="form-control" required
               value="<?= htmlspecialchars($patient['prenom'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="date_naissance">Date de naissance</label>
        <input type="date" id="date_naissance" name="date_naissance" class="form-control" required
               value="<?= htmlspecialchars($patient['date_naissance'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="genre">Genre</label>
        <select id="genre" name="genre" class="form-control" required>
            <option value="">Sélectionnez un genre</option>
            <option value="Homme" <?= (isset($patient['genre']) && $patient['genre'] === 'Homme') ? 'selected' : '' ?>>Masculin</option>
            <option value="Femme" <?= (isset($patient['genre']) && $patient['genre'] === 'Femme') ? 'selected' : '' ?>>Féminin</option>
        </select>
    </div>

    <div class="form-group">
        <label for="numero_dossier">Numéro de dossier (DOS-2026)</label>
        <input type="text" id="numero_dossier" name="numeroDossier" class="form-control" required
               value="<?= htmlspecialchars($patient['numero_dossier'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="service">Service</label>
        <select id="service" name="service" class="form-control" required>
            <option value="">Sélectionnez un service</option>
            <option value="Urgences" <?= (isset($patient['service']) && $patient['service'] === 'Urgences') ? 'selected' : '' ?>>Urgences</option>
            <option value="Chirurgie" <?= (isset($patient['service']) && $patient['service'] === 'Chirurgie') ? 'selected' : '' ?>>Chirurgie</option>
            <option value="Médecine interne" <?= (isset($patient['service']) && $patient['service'] === 'Médecine interne') ? 'selected' : '' ?>>Médecine interne</option>
        </select>
    </div>

    <div class="form-group">
        <label for="chambre">Chambre</label>
        <input type="text" id="chambre" name="chambre" class="form-control"
               value="<?= htmlspecialchars($patient['chambre'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="statut">Statut</label>
        <select id="statut" name="statut" class="form-control" required
                <?= (isset($patient['statut']) && $patient['statut'] === 'Sorti') ? 'disabled' : '' ?>>
            <option value="">Sélectionnez un statut</option>
            <option value="Hospitalisé" <?= (isset($patient['statut']) && $patient['statut'] === 'Hospitalisé') ? 'selected' : '' ?>>Hospitalisé</option>
            <option value="Sorti" <?= (isset($patient['statut']) && $patient['statut'] === 'Sorti') ? 'selected' : '' ?>>Sorti</option>
        </select>
        <?php if (isset($patient['statut']) && $patient['statut'] === 'Sorti'): ?>
            <input type="hidden" name="statut" value="Sorti">
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary"><?= $id ? 'Mettre à jour' : 'Ajouter' ?></button>
    <a href="/patients" class="btn btn-secondary">Annuler</a>
</form>