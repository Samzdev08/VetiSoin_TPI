<?php
/**
 * Fichier : list.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Liste et recherche de patients
 */


?>
<h1>Liste des patients</h1>

<form method="GET" action="/patients">
    <label for="nom">Nom ou prénom</label>
    <input type="text" id="nom" name="nom" placeholder="Nom ou prénom" onchange="this.form.submit()"
           value="<?= htmlspecialchars($_GET['nom'] ?? '') ?>">

    <label for="numeroDossier">Numéro de dossier</label>
    <input type="text" id="numeroDossier" name="numeroDossier" placeholder="Numéro de dossier" onchange="this.form.submit()"
           value="<?= htmlspecialchars($_GET['numeroDossier'] ?? '') ?>">

    <button type="submit">Rechercher</button>
    <a href="/patients">Réinitialiser</a>
</form>

<a href="/patient/form">Ajouter un patient</a>

<?php if ($patients): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>N° DOSSIER</th>
                <th>NOM</th>
                <th>PRÉNOM</th>
                <th>NAISSANCE</th>
                <th>GENRE</th>
                <th>CHAMBRE</th>
                <th>SERVICE</th>
                <th>STATUT</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($patients as $patient): ?>
                <tr data-id="<?= $patient['id'] ?>" style="cursor: pointer;">
                    <td><?= htmlspecialchars($patient['numero_dossier']) ?></td>
                    <td><?= htmlspecialchars($patient['nom']) ?></td>
                    <td><?= htmlspecialchars($patient['prenom']) ?></td>
                    <td><?= htmlspecialchars((new DateTime($patient['date_naissance']))->format('d-m-Y')) ?></td>
                    <td><?= htmlspecialchars(ucfirst($patient['genre'][0])) ?></td>
                    <td><?= htmlspecialchars($patient['chambre'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($patient['service']) ?></td>
                    <td><?= htmlspecialchars($patient['statut']) ?></td>
                    <td>
                        <?php if ($patient['statut'] === 'Hospitalisé'): ?>
                            <a href="/patient/<?= $patient['id'] ?>">Voir plus</a>
                            <a href="/patient/form/<?= $patient['id'] ?>/edit">Modifier</a>
                            <form method="POST" action="/patient/<?= $patient['id'] ?>/delete"
                                  onsubmit="return confirm('Supprimer ce patient ?');"
                                  style="display:inline;">

                                <button type="submit">Supprimer</button>
                            </form>
                        <?php else: ?>
                            <a href="/patient/<?= $patient['id'] ?>">Historique</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucun patient trouvé.</p>
<?php endif; ?>