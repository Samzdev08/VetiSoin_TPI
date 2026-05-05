<?php

/**
 * Fichier : dashboard.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Tableau de bord statistiques
 */
/** @var string $dateDebut */
/** @var string $dateFin */
/** @var int $nbReservations */
/** @var array $articlesTop */
/** @var array $categoriesTop */
?>

<div class="container mt-4">

    <h1 class="h3 mb-3">Statistiques</h1>

    <form action="/admin/stats" method="get" class="card card-body mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="debut" class="form-label small text-muted mb-1">Du</label>
                <input type="date" id="debut" name="debut"
                    value="<?= htmlspecialchars($dateDebut) ?>"
                    class="form-control form-control-sm" required>
            </div>

            <div class="col-md-4">
                <label for="fin" class="form-label small text-muted mb-1">Au</label>
                <input type="date" id="fin" name="fin"
                    value="<?= htmlspecialchars($dateFin) ?>"
                    class="form-control form-control-sm" required>
            </div>

            <div class="col-md-4">
                <button type="submit" class="btn btn-sm btn-dark">Filtrer</button>
            </div>
        </div>
    </form>


    <div class="card mb-4">
        <div class="card-body">
            <div class="text-muted small mb-1">Nombre de réservations sur la période</div>
            <div class="h2 mb-0"><?= $nbReservations ?></div>
        </div>
    </div>

    <div class="row g-3">


        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Articles les plus demandés</h5>

                    <?php if (empty($articlesTop)) : ?>
                        <p class="text-muted small mb-0">Aucune donnée pour cette période.</p>
                    <?php else : ?>
                        <canvas id="articlesChart"></canvas>
                    <?php endif; ?>

                </div>
            </div>
        </div>


        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Catégories les plus demandées</h5>

                    <?php if (empty($categoriesTop)) : ?>
                        <p class="text-muted small mb-0">Aucune donnée pour cette période.</p>
                    <?php else : ?>
                        <canvas id="categoriesChart"></canvas>
                    <?php endif; ?>

                </div>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const articles = <?php echo json_encode($articlesTop); ?>;
    const categories = <?php echo json_encode($categoriesTop); ?>;

    if (articles.length > 0) {
        const labelsArticles = articles.map(a => a.nom);
        const dataArticles = articles.map(a => a.total);

        new Chart(document.getElementById('articlesChart'), {
            type: 'bar',
            data: {
                labels: labelsArticles,
                datasets: [{
                    label: 'Quantité',
                    data: dataArticles,
                    backgroundColor: '#2E7BB5',
                    borderRadius: 6
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }


    if (categories.length > 0) {
        const labelsCategories = categories.map(c => c.nom);
        const dataCategories = categories.map(c => c.total);

        new Chart(document.getElementById('categoriesChart'), {
            type: 'bar',
            data: {
                labels: labelsCategories,
                datasets: [{
                    label: 'Quantité',
                    data: dataCategories,
                    backgroundColor: '#2E7BB5',
                    borderRadius: 6
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }

        });

    }
</script>