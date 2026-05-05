<?php

/**
 * Fichier : new.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Prise de rendez-vous
 */
/** @var array $infos */

$row = $infos[0];



$dateActuelle  = date('Y-m-d', strtotime($row['date_retrait_effective']));
$heureActuelle = date('H', strtotime($row['date_retrait_effective']))
    . 'h'
    . date('i', strtotime($row['date_retrait_effective']));
?>

<div class="container mt-4">

    <h1 class="h4 fw-bold mb-3">
        Confirmation de réservation #<?= $row['id'] ?>
    </h1>

    <a href="/reservations/<?= $row['id'] ?>" class="btn btn-link mb-3 px-0">
        ← Retour
    </a>

    <form action="/rdv/<?= $row['id'] ?>/post" method="post" id="form-rdv">

        <div class="row g-4">

            <div class="col-lg-8">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">

                        <h5 class="fw-bold mb-3">Choisir la date de retrait</h5>

                        <div id="calendarContainer" class="mb-3"></div>

                        <h6 class="text-muted mb-2" id="horraireLabel">
                            Créneaux disponibles
                        </h6>

                        <div id="horraireGrid" class="d-flex flex-wrap gap-2 mb-3"></div>

                        <label for="lieu" class="form-label">Lieu de retrait</label>
                        <select id="lieu" name="lieu" class="form-select">
                            <option value="Vestiaire principal">Vestiaire principal</option>
                            <option value="Secrétariat">Secrétariat</option>
                        </select>

                        <input type="hidden" name="date_rdv" id="input-date-rdv"
                            value="<?= htmlspecialchars($row['date_retrait_effective']) ?>">

                    </div>
                </div>

            </div>


            <div class="col-lg-4">

                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        <h5 class="fw-bold mb-3">Récapitulatif</h5>

                        <div class="mb-3">
                            <div class="fw-semibold">Patient</div>
                            <div class="text-muted">
                                <?= htmlspecialchars($row['patient_nom'] . ' ' . $row['patient_prenom']) ?>
                            </div>
                            <small class="text-muted">
                                Dossier <?= htmlspecialchars($row['numero_dossier']) ?> —
                                Chambre <?= htmlspecialchars($row['chambre']) ?> —
                                <?= htmlspecialchars($row['patient_service']) ?>
                            </small>
                        </div>

                        <div class="mb-3">
                            <div class="fw-semibold">Rendez-vous</div>
                            <div id="recapRdv" class="text-muted">
                                <?= htmlspecialchars($row['date_retrait_effective']) ?>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Confirmer la réservation 
                        </button>

                    </div>
                </div>

            </div>

        </div>

    </form>

</div>
<script>
       const gridHorraire  = document.getElementById('horraireGrid');
    const labelHorraire = document.getElementById('horraireLabel');
    const HORAIRES      = ['08h00', '10h00', '11h30', '14h30', '16h00'];

    const dateActuelle  = '<?= $dateActuelle ?>';
    const heureActuelle = '<?= $heureActuelle ?>';

    const today   = new Date();
    const maxDate = new Date(today);
    maxDate.setDate(maxDate.getDate() + 7);
    const fmt = d => d.toISOString().split('T')[0];

    flatpickr('#calendarContainer', {
        locale     : 'fr',
        inline     : true,
        dateFormat : 'Y-m-d',
        minDate    : fmt(today),
        maxDate    : fmt(maxDate),
        defaultDate: dateActuelle,
        onChange   : function(selectedDates, dateStr) {
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
                rdvSelected(h, date);
            });

            gridHorraire.appendChild(btn);
        });
    }

    function rdvSelected(horaire, date) {
        document.getElementById('recapRdv').textContent = `${date} à ${horaire}`;
        document.getElementById('input-date-rdv').value = `${date} ${horaire.replace('h', ':')}:00`;
    }
</script>
