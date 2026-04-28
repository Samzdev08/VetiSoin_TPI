<?php
/**
 * Fichier : cart.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Panier de reservation
 */
?>
<div class="container mt-5">
    <h2 class="mb-4">Panier de Réservations</h2>


    <?php if (empty($paniers)): ?>
        <div class="alert alert-info">
            Votre panier est vide.
            <a href="/catalogue" class="alert-link">Retour au catalogue</a>
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Article</th>
                            <th scope="col">Couleur</th>
                            <th scope="col">Taille</th>
                            <th scope="col">Quantité</th>
                            <th scope="col" class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paniers as $i => $panier): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= htmlspecialchars($panier['photo']) ?>"
                                             alt="<?= htmlspecialchars($panier['nom']) ?>"
                                             style="width: 64px; height: 64px; object-fit: cover; background: #f5f5f5;"
                                             class="rounded">
                                        <div>
                                            <p class="mb-0 fw-semibold"><?= htmlspecialchars($panier['nom']) ?></p>
                                            <small class="text-muted text-uppercase"><?= htmlspecialchars($panier['marque']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($panier['couleur']) ?></td>
                                <td><?= htmlspecialchars($panier['taille']) ?></td>
                                <td>
                                    <form action="/panier/update/<?= $i ?>" method="POST" class="d-inline">
                                        <input type="hidden" name="index" value="<?= $i ?>">
                                        <input type="hidden" name="maxStock" value="<?= $panier['maxStock'] ?>">
                                        <input type="number"
                                               name="quantite"
                                               value="<?= $panier['quantite'] ?>"
                                               min="1"
                                               class="form-control form-control-sm"
                                               style="width: 80px;">
                                    </form>
                                </td>
                                <td class="text-end">
                                    <form action="/panier/remove/<?= $i ?>" method="POST">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            X
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white d-flex justify-content-end gap-2">
                <a href="/catalogue" class="btn btn-outline-secondary btn-sm">← Continuer mes achats</a>
                <a href="/panier/vider" class="btn btn-outline-danger btn-sm">Vider le panier</a>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <a href="/reservations/checkout" class="btn btn-success btn-lg">
                Passer à la caisse →
            </a>
        </div>
    <?php endif; ?>
</div>