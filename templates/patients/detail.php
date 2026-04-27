<?php

/**
 * Fichier : detail.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Fiche detaillee d'un patient
 */


?>
<h1>Détails du patient</h1>

<p><strong>Nom :</strong> <?= htmlspecialchars($patient['nom']) ?></p>
<p><strong>Prénom :</strong> <?= htmlspecialchars($patient['prenom']) ?></p>
<p><strong>Date de naissance :</strong>
    <?= htmlspecialchars((new DateTime($patient['date_naissance']))->format('d-m-Y')) ?>
</p>
<p><strong>Genre :</strong> <?= htmlspecialchars(ucfirst($patient['genre'][0])) ?></p>
<p><strong>Numéro de dossier :</strong> <?= htmlspecialchars($patient['numero_dossier']) ?></p>
<p><strong>Statut :</strong> <?= htmlspecialchars($patient['statut']) ?></p>
<p><strong>Service :</strong> <?= htmlspecialchars($patient['service']) ?></p>
<p><strong>Chambre :</strong> <?= htmlspecialchars($patient['chambre'] ?? '-') ?></p>

<h2>Réservations</h2>

<?php if (!empty($patient['reservations'])): ?>
    <?php foreach ($patient['reservations'] as $reservation): ?>

        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">



            <p><strong>Soignant :</strong>
                <?= htmlspecialchars($reservation['soignant_nom'] ?? '') ?>
                <?= htmlspecialchars($reservation['soignant_prenom'] ?? '') ?>
            </p>

            <p><strong>Date de réservation :</strong>
                <?= htmlspecialchars((new DateTime($reservation['date_reservation']))->format('d-m-Y')) ?>
            </p>

            <p><strong>Statut :</strong> <?= htmlspecialchars($reservation['reservation_statut'] ?? '') ?></p>
            <p><strong>Commentaire :</strong> <?= htmlspecialchars($reservation['reservation_commentaire'] ?? '-') ?></p>

        </div>

    <?php endforeach; ?>
<?php else: ?>
    <p>Aucune réservation</p>
<?php endif; ?>

<a href="/patients">Retour à la liste</a>