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

    <?php if (empty($reservations)): ?>
        <p>Votre panier est vide.</p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td>
                            <img src="" alt="">
                            <div>
                                <p></p>
                                <small></small>
                            </div>
                        </td>
                        <td>
                            <span></span>
                        </td>
                        <td>
                            <form action="">
                                <input type="number" name="" id="">
                            </form>
                        </td>
                        <td>
                            <a href="/panier/remove/<?= $i ?>" class="btn btn-outline-danger btn-sm">✕</a>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
        <div class="card-footer bg-white d-flex justify-content-end gap-2">
                        <a href="/panier/vider" class="btn btn-outline-secondary btn-sm btn-clear">Vider le panier</a>
                        
                    </div>

        <div class="d-flex justify-content-end">
            <h4>Total : <?= number_format($total, 2) ?> €</h4>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <a href="/reservations/checkout" class="btn btn-success">Passer à la Caisse</a>
        </div>
    <?php endif; ?>