<?php
/**
 * Fichier : layout.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Layout principal
 */
/** @var string|null $content */
/** @var string|null $title */
$flash      = $flash ?? $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin    = $isLoggedIn && $_SESSION['user_role'] === 'Administrateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/fr.js"></script>
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>


<nav class="navbar navbar-dark px-3" style="background:#1A5C8A; height:54px;">
    <div class="d-flex align-items-center gap-2">
        <?php if ($isLoggedIn): ?>
            <button class="btn btn-sm text-white p-1" id="sidebarToggle">
                <i class="bi bi-list fs-5"></i>
            </button>
        <?php endif; ?>
        <a class="navbar-brand fw-bold mb-0" href="<?= $isLoggedIn ? ($isAdmin ?? '/admin/stats') : '/catalogue' ?>">VetiSoin</a>
    </div>
    <div class="d-flex align-items-center gap-1">
        <?php if ($isLoggedIn): ?>
           
            <a href="<?= $isAdmin ? '#' : '/profil/' ?>" class="btn btn-sm text-white align-items-center d-flex ">
                <i class="bi bi-person-circle fs-5 mx-2"></i>
                <small class="d-none d-md-inline fs-6"><?= $isAdmin ? 'Connecté en tant que Admin' : 'Connecté en tant que Soignant' ?></small>
            </a>
            <a href="/auth/logout" class="btn btn-sm text-warning fs-6">Se déconnecter </a>
        <?php else: ?>
            <a href="/auth/login" class="btn btn-sm text-white">Connexion</a>
            <a href="/auth/register" class="btn btn-sm text-white">Inscription</a>
        <?php endif; ?>
    </div>
</nav>

<div class="d-flex">

    <?php if ($isLoggedIn): ?>
   
    <div id="sidebar" class="sidebar bg-white border-end">
        <ul class="nav flex-column py-2">

            <?php if ($isAdmin): ?>

                <li class="nav-item px-2 pt-2">
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.68rem">Vue d'ensemble</small>
                </li>
                <li class="nav-item">
                    <a href="/admin/stats" class="nav-link sidebar-link ">
                        <i class="bi bi-bar-chart-line"></i> Statistiques
                    </a>
                </li>

                <li class="nav-item px-2 pt-3">
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.68rem">Catalogue</small>
                </li>
                <li class="nav-item">
                    <a href="/admin/articles" class="nav-link sidebar-link">
                        <i class="bi bi-box-seam"></i> Articles
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/categories" class="nav-link sidebar-link">
                        <i class="bi bi-tags"></i> Catégories
                    </a>
                </li>

                 <li class="nav-item">
                    <a href="/notifications" class="nav-link sidebar-link ">
                        <i class="bi bi-bell"></i> Notifications
                    </a>
                </li>

                <li class="nav-item px-2 pt-3">
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.68rem">Gestion</small>
                </li>
                <li class="nav-item">
                    <a href="/admin/soignants" class="nav-link sidebar-link">
                        <i class="bi bi-people"></i> Utilisateurs
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/reservations" class="nav-link sidebar-link">
                        <i class="bi bi-clipboard-check"></i> Réservations
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/rdv" class="nav-link sidebar-link ">
                        <i class="bi bi-calendar-check"></i> Rendez-vous
                    </a>
                </li>

            <?php else: ?>

             
                <li class="nav-item">
                    <a href="/patients" class="nav-link sidebar-link ">
                        <i class="bi bi-person-heart"></i> Patients
                    </a>
                </li>

                <li class="nav-item px-2 pt-3">
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.68rem">Activité</small>
                </li>
                <li class="nav-item">
                    <a href="/reservations" class="nav-link sidebar-link ">
                        <i class="bi bi-clipboard-check"></i> Réservations
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/rdv" class="nav-link sidebar-link ">
                        <i class="bi bi-calendar-check"></i> Rendez-vous
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/catalogue" class="nav-link sidebar-link ">
                        <i class="bi bi-grid"></i> Catalogue
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/panier" class="nav-link sidebar-link ">
                        <i class="bi bi-cart3"></i> Panier
                    </a>
                </li>

                <li class="nav-item px-2 pt-3">
                    <small class="text-muted text-uppercase fw-bold" style="font-size:.68rem">Mon compte</small>
                </li>
                <li class="nav-item">
                    <a href="/notifications" class="nav-link sidebar-link ">
                        <i class="bi bi-bell"></i> Notifications
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/profil/" class="nav-link sidebar-link ">
                        <i class="bi bi-person-gear"></i> Mon profil
                    </a>
                </li>

            <?php endif; ?>

        </ul>
    </div>
    <?php endif; ?>

    <main class="flex-grow-1 p-4" style="min-width:0">

        <?php if (!empty($flash['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $flash['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($flash['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $flash['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $content ?>

    </main>
</div>

<?php if (!$isLoggedIn): ?>
<footer class="py-4 mt-5 text-center text-white" style="background:#1A5C8A">
    <small>&copy; 2026 VetiSoin — Plateforme de réservation vestimentaire pour patients hospitalisés</small>
</footer>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
