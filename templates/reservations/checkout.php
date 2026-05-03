<?php

/**
 * Fichier : checkout.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Page de checkout pour finaliser la reservation
 */

?>

<div class="container mt-4">

    <h1>Finaliser la réservation</h1>
    <p>Merci de vérifier les détails de votre réservation avant de confirmer.</p>

    <div class="card mb-4">
        <div class="step-indicator">
            <span>Étape 1 sur 3</span>
        </div>
        <h2 class="card-title-vs">Pour quel patient ?</h2>

        <div class="chips-filter">
            <form action="/reservations/checkout" method="get" id="filter-form">


                <input type="hidden" name="service"
                    value="<?= htmlspecialchars($_GET['service'] ?? '') ?>"
                    id="service_patient">

                <button type="button" onclick="setValue('service_patient', '')">Tous les services</button>
                <button type="button" onclick="setValue('service_patient', 'Urgences')">Urgences</button>
                <button type="button" onclick="setValue('service_patient', 'Chirurgie')">Chirurgie</button>
                <button type="button" onclick="setValue('service_patient', 'Médecine interne')">Médecine interne</button>


                <input type="text"
                    name="recherche"
                    id="recherche"
                    placeholder="Rechercher un patient..."
                    onchange="this.form.submit()"
                    value="<?= htmlspecialchars($_GET['recherche'] ?? '') ?>">
            </form>


            <ul>
                <?php if (empty($patients)): ?>
                    <li>Aucun patient trouvé.</li>
                <?php else: ?>
                    <?php foreach ($patients as $patient): ?>
                        <li onclick="patientSelected(<?= $patient['id'] ?>, '<?= htmlspecialchars($patient['nom']) ?>', '<?= htmlspecialchars($patient['chambre']) ?>', '<?= htmlspecialchars($patient['service']) ?>', '<?= htmlspecialchars($patient['prenom']) ?>')">
                            <?= htmlspecialchars($patient['nom']) ?>
                            <?= htmlspecialchars($patient['prenom']) ?>
                            <span>
                                <?= htmlspecialchars($patient['chambre']) ?> —
                                <?= htmlspecialchars($patient['service']) ?>
                            </span>
                            <span><?= htmlspecialchars($patient['numero_dossier']) ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>


    <div class="card-vs mb-4">
        <div class="step-indicator">
            <span>Étape 2 sur 3</span>
        </div>
        <h2 class="card-title-vs">Choisir la date de retrait prévisionnel</h2>

        <div id="calendarContainer"></div>

        <div class="horraire-label" id="horraireLabel">
            Créneaux — sélectionne d'abord une date
        </div>
        <div class="horraire-grid" id="horraireGrid"></div>
    </div>


    <aside class="recap-card">
        <div class="recap-header">
            <h2 class="recap-title">Récapitulatif de la réservation</h2>
        </div>


        <div class="infos">
            <div id="recapPatient" class="recap-patient-block empty">
                Aucun patient sélectionné

            </div>
            <p id="infos-sec-patient"></p>
        </div>

        <div class="recap-articles">
            <div class="recap-label">Articles réservés</div>

            <?php if (!empty($paniers)): ?>
                <?php foreach ($paniers as $panier): ?>
                    <div class="recap-article">
                        <img src="  <?= htmlspecialchars($panier['photo']) ?>" alt="">
                        <div class="recap-article-text">
                            <?= htmlspecialchars($panier['nom']) ?> - <?= htmlspecialchars($panier['couleur']) ?>
                            × <?= htmlspecialchars($panier['quantite']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>




        <div id="recapRdv" class="recap-rdv empty">
            Sélectionne un créneau de retrait...
        </div>

        <form action="/reservations/add" method="post">

            <textarea name="commentaire" id="" maxlength="250" placeholder="Optionnel — Ajouter un commentaire pour le personnel de l'hôpital (ex: besoins spécifiques, urgence, etc.)" class="form-control mb-3" style="height: 100px;"></textarea>

            </textarea>

            <input type="hidden" name="patient_id" id="input-patient-id">
            <input type="hidden" name="date_retrait" id="input-date-retrait">

            <button type="submit" class="" id="btnConfirm" disabled style="cursor:not-allowed;">
                Confirmer la réservation
            </button>
    </aside>

</div>


<script src="../assets/js/checkout.js"></script>