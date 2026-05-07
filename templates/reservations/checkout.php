<style>
    .patient-list {
    max-height: 250px;
    overflow-y: auto;
}
</style>
<div class="container mt-4">

    <h1 class="h4 fw-bold mb-3">
       Finaliser la réservation
    </h1>
    <p class="text-muted mb-4">Merci de vérifier les détails de votre réservation avant de confirmer.</p>

    <div class="row g-4">

        <div class="col-lg-8">

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Pour quel patient ?</h5>

                    <form action="/reservations/checkout" method="get" class="mb-3 d-flex flex-wrap gap-2 " id="filter-form">

                        <input type="hidden" name="service"
                            value="<?= htmlspecialchars($_GET['service'] ?? '') ?>"
                            id="service_patient">

                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setValue('service_patient', '')">Tous</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setValue('service_patient', 'Urgences')">Urgences</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setValue('service_patient', 'Chirurgie')">Chirurgie</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setValue('service_patient', 'Médecine interne')">Médecine interne</button>

                        <input type="text"
                            name="recherche"
                            class="form-control form-control-sm ms-auto"
                            style="max-width: 250px;"
                            placeholder="Rechercher..."
                            onchange="this.form.submit()"
                            value="<?= htmlspecialchars($_GET['recherche'] ?? '') ?>">
                    </form>

                    <ul class="list-group patient-list">
                        <?php if (empty($patients)): ?>
                            <li class="list-group-item text-muted">Aucun patient trouvé.</li>
                        <?php else: ?>
                            <?php foreach ($patients as $patient): ?>
                                <li class="list-group-item list-group-item-action"
                                    onclick="patientSelected(<?= $patient['id'] ?>, '<?= htmlspecialchars($patient['nom']) ?>', '<?= htmlspecialchars($patient['chambre']) ?>', '<?= htmlspecialchars($patient['service']) ?>', '<?= htmlspecialchars($patient['prenom']) ?>')">

                                    <div class="fw-bold">
                                        <?= htmlspecialchars($patient['nom']) ?> <?= htmlspecialchars($patient['prenom']) ?>
                                    </div>

                                    <small class="text-muted">
                                        <?= htmlspecialchars($patient['chambre']) ?> — <?= htmlspecialchars($patient['service']) ?>
                                    </small>

                                    <div class="text-muted small">
                                        <?= htmlspecialchars($patient['numero_dossier']) ?>
                                    </div>

                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>

                </div>
            </div>


            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Choisir la date</h5>

                    <div id="calendarContainer" class="mb-3"></div>

                    <div class="text-muted small mb-2" id="horraireLabel">
                        Créneaux — sélectionne d'abord une date
                    </div>

                    <div class="d-flex flex-wrap gap-2" id="gridHorraire"></div>

                </div>
            </div>

        </div>


        <div class="col-lg-4">

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Récapitulatif</h5>

                    <div id="recapPatient" class="text-muted mb-3">
                        Aucun patient sélectionné
                    </div>

                    <div id="infos-sec-patient" class="small text-muted mb-3"></div>

                    <div class="mb-3">
                        <div class="fw-semibold mb-2">Articles</div>

                        <?php if (!empty($paniers)): ?>
                            <?php foreach ($paniers as $panier): ?>
                                <div class="d-flex align-items-center mb-2">
                                    <img src="../<?= htmlspecialchars($panier['photo']) ?>"
                                        style="width:40px;height:40px;object-fit:cover;border-radius:6px;margin-right:10px;">
                                    <div class="small">
                                        <?= htmlspecialchars($panier['nom']) ?> - <?= htmlspecialchars($panier['couleur']) ?>
                                        × <?= htmlspecialchars($panier['quantite']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div id="recapRdv" class="text-muted mb-3">
                        Sélectionne un créneau...
                    </div>

                    <form action="/reservations/add" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                        <textarea name="commentaire" maxlength="250"
                            class="form-control mb-3"
                            placeholder="Commentaire (optionnel)"
                            style="height:100px;"></textarea>

                        <input type="hidden" name="patient_id" id="input-patient-id">
                        <input type="hidden" name="date_retrait" id="input-date-retrait">

                        <button type="submit" class="btn btn-primary w-100"
                            id="btnConfirm" disabled>
                            Confirmer la réservation
                        </button>

                    </form>

                </div>
            </div>

        </div>

    </div>
<script src="../assets/js/checkout.js"></script>
</div>
