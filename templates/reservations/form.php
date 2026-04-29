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
                    <li>
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
                value="<?= htmlspecialchars($row['date_retrait_effective']) ?>" required>
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
                                    name=""
                                    value="<?= $ligne['quantite'] ?>"
                                    min="1"
                                    max=""
                                    >
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
