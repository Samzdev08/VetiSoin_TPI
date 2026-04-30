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

$dateActuelle  = date('Y-m-d', strtotime($row['date_retrait_effective']));
$heureActuelle = date('H', strtotime($row['date_retrait_effective']))
    . 'h'
    . date('i', strtotime($row['date_retrait_effective']));

?>

<div>

    <h1>Modifier la réservation #<?= $row['id'] ?></h1>
    <p>Statut : En attente</p>

    <a href="/reservations/<?= $row['id'] ?>">← Retour</a>

    <div>
        <h2>Patient</h2>

        <form action="/reservations/<?= $row['id'] ?>/updateForm" method="get" id="filter-form">
            <input type="hidden" name="service"
                value="<?= htmlspecialchars($_GET['service'] ?? '') ?>"
                id="service_patient">

            <div>
                <button type="button" onclick="setValue('service_patient', '')">Tous</button>
                <button type="button" onclick="setValue('service_patient', 'Urgences')">Urgences</button>
                <button type="button" onclick="setValue('service_patient', 'Chirurgie')">Chirurgie</button>
                <button type="button" onclick="setValue('service_patient', 'Médecine interne')">Médecine interne</button>
            </div>

            <input type="text"
                name="recherche"
                placeholder="Rechercher un patient..."
                onchange="this.form.submit()"
                value="<?= htmlspecialchars($_GET['recherche'] ?? '') ?>">
        </form>

        <ul>
            <?php if (empty($patients)): ?>
                <li>Aucun patient trouvé.</li>
            <?php else: ?>
                <?php foreach ($patients as $patient): ?>
                    <li <?php if ($row['id_patient'] == $patient['id']): ?>style="background-color: #cfe9ff;" <?php endif; ?>
                        onclick="patientSelected(this, <?= $patient['id'] ?>)">
                        <?= htmlspecialchars($patient['nom'] . ' ' . $patient['prenom']) ?>
                        — Chambre <?= htmlspecialchars($patient['chambre']) ?>
                        — <?= htmlspecialchars($patient['service']) ?>
                        — <?= htmlspecialchars($patient['numero_dossier']) ?>

                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>


    <form action="/reservations/<?= $row['id'] ?>/edit" method="post" id="form-edit">

        <input type="hidden" name="patient_id" id="input-patient-id" value="<?= $row['id_patient'] ?>">


        <div>
            <h2>Date de retrait prévue</h2>

            <div id="calendarContainer"></div>

            <p id="horraireLabel">Créneaux disponibles</p>
            <div id="horraireGrid"></div>

            <p id="recapRdv">
                Sélectionné : <?= htmlspecialchars($row['date_retrait_effective']) ?>
            </p>

            <input type="hidden" name="date_retrait" id="input-date-retrait"
                value="<?= htmlspecialchars($row['date_retrait_effective']) ?>">
        </div>


        <div>
            <h2>Quantités des articles</h2>
            <table>
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
                                <?= htmlspecialchars($ligne['article_nom']) ?>
                                <?= htmlspecialchars($ligne['marque'] ?? '') ?>
                            </td>
                            <td><?= htmlspecialchars($ligne['taille']) ?></td>
                            <td><?= htmlspecialchars($ligne['couleur']) ?></td>
                            <td>
                                <input type="number"
                                    name="quantite[<?= $ligne['article_reserve_id'] ?>]"
                                    value="<?= $ligne['quantite'] ?>"
                                    min="1"
                                    max="<?=  $ligne['stock']?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div>
            <h2>Commentaire</h2>
            <textarea name="commentaire" maxlength="250" rows="3"
                placeholder="Optionnel…"><?= htmlspecialchars($row['commentaire'] ?? '') ?></textarea>
        </div>

        <div>
            <button type="submit">Enregistrer les modifications</button>
            <a href="/reservations/<?= $row['id'] ?>">Annuler</a>       
        </div>

    </form>
</div>
<script>
    const gridHorraire = document.getElementById('horraireGrid');
    const labelHorraire = document.getElementById('horraireLabel');
    const HORAIRES = ['08h00', '10h00', '11h30', '14h30', '16h00'];

    const dateActuelle = '<?= $dateActuelle ?>';
    const heureActuelle = '<?= $heureActuelle ?>';

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

            let isActive = (date === dateActuelle && h === heureActuelle);
            btn.className = isActive ? 'btn btn-primary horraire' : 'btn btn-secondary';

            btn.addEventListener('click', () => {

                reservationSelected(h, date);
                gridHorraire.querySelectorAll('button').forEach(b => {
                    b.className = 'btn btn-secondary';
                });
               
                btn.className = 'btn btn-primary'

            });
            gridHorraire.appendChild(btn);
        });
    }

    function reservationSelected(horaire, date) {
        document.getElementById('recapRdv').textContent = `Sélectionné : ${date} à ${horaire}`;
        document.getElementById('input-date-retrait').value = convertToSqlFormat(date, horaire);
    }

    function convertToSqlFormat(date, horaire) {
        return `${date} ${horaire.replace('h', ':')}:00`;
    }

    function patientSelected(li, patientId) {
        document.querySelectorAll('ul li').forEach(l => l.style.backgroundColor = '');
        li.style.backgroundColor = '#cfe9ff';
        document.getElementById('input-patient-id').value = patientId;
    }
</script>