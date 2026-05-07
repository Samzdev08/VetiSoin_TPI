<?php

/**
 * Fichier : edit.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Modification d'une réservation
 */
/** @var array $reservations */
/** @var array $patients */

$row = $reservations[0];

$dateActuelle  = date('Y-m-d', strtotime($row['date_retrait_previsionelle']));
$heureActuelle = date('H', strtotime($row['date_retrait_previsionelle']))
    . 'h'
    . date('i', strtotime($row['date_retrait_previsionelle']));
?>
<style>
    .patient-list {
        max-height: 250px;
        overflow-y: auto;
    }
</style>
<div class="container mt-4">

    <h1 class="h4 fw-bold mb-3">
        Modifier la réservation #<?= $row['id'] ?>
    </h1>

    <a href="/reservations/<?= $row['id'] ?>" class="btn btn-link mb-3 px-0">
        ← Retour
    </a>

    <form action="/reservations/<?= $row['id'] ?>/updateForm" method="get" id="filter-form">
        <input type="hidden" name="service"
            value="<?= htmlspecialchars($_GET['service'] ?? '') ?>"
            id="service_patient">
    </form>

    <form action="/reservations/<?= $row['id'] ?>/edit" method="post" id="form-edit">

        <input type="hidden" name="patient_id" id="input-patient-id" value="<?= $row['id_patient'] ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="row g-4">

            <div class="col-lg-8">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">

                        <h5 class="fw-bold mb-3">Patient</h5>


                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="submit" form="filter-form" class="btn btn-sm btn-outline-primary" onclick="setValue('service_patient', '')">Tous</button>
                            <button type="submit" form="filter-form" class="btn btn-sm btn-outline-primary" onclick="setValue('service_patient', 'Urgences')">Urgences</button>
                            <button type="submit" form="filter-form" class="btn btn-sm btn-outline-primary" onclick="setValue('service_patient', 'Chirurgie')">Chirurgie</button>
                            <button type="submit" form="filter-form" class="btn btn-sm btn-outline-primary" onclick="setValue('service_patient', 'Médecine interne')">Médecine interne</button>
                        </div>

                        <input type="text"
                            name="recherche"
                            form="filter-form"
                            class="form-control form-control-sm mb-3 w-50"
                            placeholder="Rechercher un patient..."
                            onchange="this.form.submit()"
                            value="<?= htmlspecialchars($_GET['recherche'] ?? '') ?>">

                        <ul class="list-group mt-3 patient-list">
                            <?php if (empty($patients)): ?>
                                <li class="list-group-item text-muted">Aucun patient trouvé.</li>
                            <?php else: ?>
                                <?php foreach ($patients as $patient): ?>
                                    <li <?php if ($row['id_patient'] == $patient['id']): ?>style="background-color: #cfe9ff;" <?php endif; ?>
                                        onclick="patientSelected(this, <?= $patient['id'] ?>, '<?= htmlspecialchars($patient['nom']) ?>', '<?= htmlspecialchars($patient['chambre']) ?>', '<?= htmlspecialchars($patient['service']) ?>', '<?= htmlspecialchars($patient['prenom']) ?>', '<?= htmlspecialchars($patient['numero_dossier']) ?>')">
                                        <div class="fw-semibold">
                                            <?= htmlspecialchars($patient['nom'] . ' ' . $patient['prenom']) ?>
                                        </div>
                                        <small class="text-muted">
                                            Chambre <?= htmlspecialchars($patient['chambre']) ?> —
                                            <?= htmlspecialchars($patient['service']) ?> —
                                            <?= htmlspecialchars($patient['numero_dossier']) ?>
                                        </small>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>

                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">

                        <h5 class="fw-bold mb-3">Choisir la date de retrait</h5>

                        <div id="calendarContainer" class="mb-3"></div>

                        <h6 class="text-muted mb-2" id="horraireLabel">Créneaux disponibles</h6>

                        <div id="horraireGrid" class="d-flex flex-wrap gap-2 mb-3"></div>

                        <input type="hidden" name="date_retrait" id="input-date-retrait"
                            value="<?= htmlspecialchars($row['date_retrait_previsionelle']) ?>">

                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        <h5 class="fw-bold mb-3">Quantités des articles</h5>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Article</th>
                                        <th>Taille</th>
                                        <th>Couleur</th>
                                        <th>Quantité</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservations as $ligne): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">
                                                    <?= htmlspecialchars($ligne['article_nom']) ?>
                                                </div>
                                                <?php if (!empty($ligne['marque'])): ?>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars($ligne['marque']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($ligne['taille']) ?></td>
                                            <td><?= htmlspecialchars($ligne['couleur']) ?></td>
                                            <td>
                                                <input type="number"
                                                    class="form-control form-control-sm"
                                                    name="quantite[<?= $ligne['article_reserve_id'] ?>]"
                                                    value="<?= $ligne['quantite'] ?>"
                                                    min="1"
                                                    max="<?= $ligne['stock'] ?>">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>

            <div class="col-lg-4">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">

                        <h5 class="fw-bold mb-3">Récapitulatif</h5>

                        <div class="mb-3">
                            <div class="fw-semibold">Patient</div>
                            <div class="text-muted" id="recapPatientNom">
                                <?= htmlspecialchars($row['patient_nom'] ?? '') ?>
                                <?= htmlspecialchars($row['patient_prenom'] ?? '') ?>
                            </div>
                            <small class="text-muted" id="recapPatientInfo">
                                Dossier <?= htmlspecialchars($row['numero_dossier'] ?? '') ?> —
                                Chambre <?= htmlspecialchars($row['chambre'] ?? '') ?> —
                                <?= htmlspecialchars($row['patient_service'] ?? '') ?>
                            </small>
                        </div>

                        <div class="mb-3">
                            <div class="fw-semibold">Rendez-vous</div>
                            <div id="recapRdv" class="text-muted">
                                <?= htmlspecialchars($row['date_retrait_previsionelle']) ?>
                            </div>
                        </div>

                        <div>
                            <div class="fw-semibold">Statut</div>
                            <div class="text-muted">En attente</div>
                        </div>

                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">

                        <h5 class="fw-bold mb-3">Commentaire</h5>

                        <textarea class="form-control"
                            name="commentaire"
                            rows="4"
                            maxlength="250"
                            placeholder="Optionnel…"><?= htmlspecialchars($row['commentaire'] ?? '') ?></textarea>

                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            Enregistrer les modifications
                        </button>

                        <a href="/reservations/<?= $row['id'] ?>" class="btn btn-secondary w-100">
                            Annuler
                        </a>

                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

<script>
    const gridHorraire = document.getElementById('horraireGrid');
    const labelHorraire = document.getElementById('horraireLabel');
    const HORAIRES = ['08h00', '10h00', '11h30', '14h30', '16h00'];

    let dateActuelle = '<?= $dateActuelle ?>';
    let heureActuelle = '<?= $heureActuelle ?>';

    function setValue(input, value) {
        document.getElementById(input).value = value;
        document.getElementById('filter-form').submit();
    }

    const today = new Date();
    const maxDate = new Date(today);
    maxDate.setDate(maxDate.getDate() + 7);
    const fmt = d => d.toISOString().split('T')[0];

    flatpickr('#calendarContainer', {
        locale: 'fr',
        inline: true,
        dateFormat: 'Y-m-d',
        minDate: fmt(today),
        maxDate: fmt(maxDate),
        defaultDate: dateActuelle,
        onChange: function(selectedDates, dateStr) {
            AffichageHorraire(dateStr);
        }
    });

    AffichageHorraire(dateActuelle);

    function AffichageHorraire(date) {
        gridHorraire.innerHTML = '';
        labelHorraire.innerHTML = `Créneaux disponibles pour le <strong>${date}</strong>`;

        HORAIRES.forEach(h => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = h;

            const isActive = (date === dateActuelle && h === heureActuelle);
            btn.className = isActive ? 'btn btn-primary' : 'btn btn-secondary';

            btn.addEventListener('click', () => {
                gridHorraire.querySelectorAll('button').forEach(b => {
                    b.className = 'btn btn-secondary';
                });
                btn.className = 'btn btn-primary';
                reservationSelected(h, date);
            });

            gridHorraire.appendChild(btn);
        });
    }

    function reservationSelected(horaire, date) {
        document.getElementById('recapRdv').textContent = `${date} à ${horaire}`;
        document.getElementById('input-date-retrait').value = `${date} ${horaire.replace('h', ':')}:00`;
    }

    function patientSelected(li, patientId, nom, chambre, service, prenom, numeroDossier) {
        document.querySelectorAll('.patient-list li').forEach(l => l.style.backgroundColor = '');
        li.style.backgroundColor = '#cfe9ff';
        document.getElementById('input-patient-id').value = patientId;

        document.getElementById('recapPatientNom').textContent = nom + ' ' + prenom;
        document.getElementById('recapPatientInfo').textContent =
            `Dossier ${numeroDossier} — Chambre ${chambre} — ${service}`;
    }
</script>