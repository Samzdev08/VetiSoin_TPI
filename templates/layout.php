<?php

/**
 * Fichier : layout.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Layout principal
 */


$flash = $flash ?? $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <header>
        <nav>
            <a href="/catalogue">VetiSoin</a>

            <ul>
                <li><a href="/catalogue">Catalogue</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>

                    <?php if ($_SESSION['user_role'] === 'administrateur'): ?>
                        <li><a href="/admin/dashboard">Dashboard Admin</a></li>
                        <li><a href="/admin/stocks">Gestion des stocks</a></li>
                        <li><a href="/admin/reservations">Réservations</a></li>
                        
                        <li><a href="/auth/logout">Déconnexion</a></li>


                    <?php else: ?>
                        <li><a href="/dashboard">Tableau de bord</a></li>
                        <li><a href="/patients">Patients</a></li>
                        <li><a href="/auth/logout">Déconnexion</a></li>
                    <?php endif; ?>

                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <div class="container mt-3">

        <?php if (!empty($flash['success'])) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✓ <?= $flash['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($flash['error'])) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ✕ <?= $flash['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

    </div>

    <main>

        <?= $content ?>

    </main>

    <footer>
        <p>
            &copy; 2026 VetiSoin — Plateforme de réservation vestimentaire pour patients hospitalisés
        </p>
    </footer>

</body>

</html>