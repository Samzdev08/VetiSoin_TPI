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

<div>
    <h1>Confirmation de réservation #<?= $row['id'] ?></h1>

    <a href="/reservations/<?= $row['id'] ?>">← Retour</a>

    <form action="/reservations/<?= $row['id'] ?>/rdv/post" method="post" id="form-rdv">

        <div>
            <h2>Choisir la date de retrait</h2>

            <div id="calendarContainer"></div>

            <h3 id="horraireLabel">Créneaux disponibles</h3>
            <div id="horraireGrid"></div>

            <label for="lieu">Lieu de retrait</label>
            <select id="lieu" name="lieu">
                <option value="Vestiaire principal">Vestiaire principal</option>
                <option value="Secrétariat">Secrétariat</option>
            </select>

            <input type="hidden" name="date_rdv" id="input-date-rdv"
                value="<?= htmlspecialchars($row['date_retrait_effective']) ?>">
        </div>

        <aside>
            <h2>Récapitulatif de la réservation</h2>

            <div>
                <p><strong>Patient :</strong>
                    <?= htmlspecialchars($row['patient_nom'] . ' ' . $row['patient_prenom']) ?>
                </p>
                <p>
                    Dossier : <?= htmlspecialchars($row['numero_dossier']) ?>
                    — Chambre <?= htmlspecialchars($row['chambre']) ?>
                    — <?= htmlspecialchars($row['patient_service']) ?>
                </p>
            </div>
            <div>
                <p>Rendez-vous sélectionné</p>
                <p id="recapRdv">
                    <?= htmlspecialchars($row['date_retrait_effective']) ?>
                </p>
            </div>

            <button type="submit">Confirmer la réservation ✓</button>
        </aside>

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