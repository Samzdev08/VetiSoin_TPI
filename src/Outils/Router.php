<?php
/**
 * Fichier : Router.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Routage des requetes HTTP
 */

/** @var \Slim\App $app */

use App\Controllers\AuthController;
use App\Controllers\CatalogController;
use App\Controllers\PatientController;
use App\Controllers\PanierController;
use App\Controllers\ReservationController;
use App\Controllers\RendezvousController;
use App\Controllers\Admin\ArticleController;
use App\Controllers\Admin\UserController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\AdminReservationController;
use App\Controllers\Admin\AdminRendezVousController;
use App\Controllers\SoignantController;
use App\Controllers\NotificationController;
use App\Controllers\Admin\StatsController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\GuestMiddleware;


// ── Public ────────────────────────────────────────────
$app->get('/', AuthController::class);


// ── Guest (déjà connecté → redirigé) ─────────────────
$app->group('/auth', function ($group) {
    $group->get('/register', [AuthController::class, 'showRegisterForm']);
    $group->get('/login',    [AuthController::class, 'showLoginForm']);
    $group->post('/create',  [AuthController::class, 'create']);
    $group->post('/login',   [AuthController::class, 'login']);
})->add(new GuestMiddleware());

$app->get('/auth/logout', [AuthController::class, 'logout']);


// ── Connecté (soignant + admin) ───────────────────────
$app->get('/catalogue',      CatalogController::class)->add(new AuthMiddleware());
$app->get('/catalogue/{id}', [CatalogController::class, 'detail'])->add(new AuthMiddleware());
$app->get('/patients',       PatientController::class)->add(new AuthMiddleware());
$app->post('/setColor/{color}/{id}', [CatalogController::class, 'setColor'])->add(new AuthMiddleware());

$app->group('/panier', function ($group) {
    $group->get('',                  PanierController::class);
    $group->post('/add',             [PanierController::class, 'addToCart']);
    $group->post('/remove/{id}',     [PanierController::class, 'removeFromCart']);
    $group->post('/update/{id}',     [PanierController::class, 'updateCart']);
    $group->post('/vider',           [PanierController::class, 'clearCart']);
})->add(new AuthMiddleware());

$app->group('/reservations', function ($group) {
    $group->get('',                                        ReservationController::class);
    $group->get('/checkout',                               [ReservationController::class, 'checkout']);
    $group->get('/{id}',                                   [ReservationController::class, 'detail']);
    $group->post('/{id}/annuler',                          [ReservationController::class, 'annuler']);
    $group->get('/{id}/updateForm',                        [ReservationController::class, 'updateForm']);
    $group->post('/add',                                   [ReservationController::class, 'add']);
    $group->post('/{id}/edit',                             [ReservationController::class, 'editPost']);
    $group->get('/{id}/rdv',                               [RendezvousController::class, 'showRdv']);
    $group->post('/{id}/items/{itemId}/demander-retour',   [ReservationController::class, 'demanderRetour']);
})->add(new AuthMiddleware());

$app->group('/rdv', function ($group) {
    $group->get('',             RendezvousController::class);
    $group->get('/{id}/detail', [RendezvousController::class, 'detail']);
    $group->get('/{id}',        [RendezvousController::class, 'showRdv']);
    $group->post('/{id}/post',  [RendezvousController::class, 'rdvPost']);
})->add(new AuthMiddleware());

$app->group('/profil', function ($group) {
    $group->get('/',             SoignantController::class);
    $group->post('/infos',       [SoignantController::class, 'updateInfos']);
    $group->post('/password',    [SoignantController::class, 'changePassword']);
})->add(new AuthMiddleware());

$app->group('/notifications', function ($group) {
    $group->get('',              NotificationController::class);
    $group->get('/lire-tout',    [NotificationController::class, 'markAllRead']);
    $group->get('/{id}/lire',    [NotificationController::class, 'markRead']);
})->add(new AuthMiddleware());

$app->group('/patient', function ($group) {
    $group->get('/form',            [PatientController::class, 'form']);
    $group->get('/form/{id}/edit',  [PatientController::class, 'form']);
    $group->get('/{id}',            [PatientController::class, 'read']);
    $group->post('/add',            [PatientController::class, 'createUpdate']);
    $group->post('/{id}/update',    [PatientController::class, 'createUpdate']);
})->add(new AuthMiddleware());


// ── Admin uniquement ──────────────────────────────────
$app->group('/admin', function ($group) {

    $group->get('/articles',                ArticleController::class);
    $group->get('/articles/create',         [ArticleController::class, 'showCreateForm']);
    $group->post('/articles/create',        [ArticleController::class, 'createPost']);
    $group->get('/articles/{id}',           [ArticleController::class, 'showDetails']);
    $group->get('/articles/{id}/edit',      [ArticleController::class, 'edit']);
    $group->post('/articles/{id}/edit',     [ArticleController::class, 'editPost']);
    $group->post('/articles/{id}/delete',   [ArticleController::class, 'delete']);
    $group->post('/variantes/{id}/edit',    [ArticleController::class, 'editVariante']);

    $group->get('/soignants',                   UserController::class);
    $group->get('/soignants/create',            [UserController::class, 'showCreateForm']);
    $group->get('/soignants/{id}/edit',         [UserController::class, 'showEditForm']);
    $group->post('/soignants/{id}/edit',        [UserController::class, 'editPost']);
    $group->post('/soignants/create',           [UserController::class, 'createPost']);
    $group->post('/soignants/{id}/reset-password', [UserController::class, 'resetPassword']);
    $group->post('/soignants/{id}/toggle',      [UserController::class, 'toggleStatut']);

    $group->get('/categories',              CategoryController::class);
    $group->get('/categories/create',       [CategoryController::class, 'showCreateForm']);
    $group->post('/categories/create',      [CategoryController::class, 'createPost']);
    $group->get('/categories/{id}/edit',    [CategoryController::class, 'showEditForm']);
    $group->post('/categories/{id}/edit',   [CategoryController::class, 'editPost']);

    $group->get('/reservations',                            AdminReservationController::class);
    $group->get('/reservations/{id}',                       [AdminReservationController::class, 'detail']);
    $group->post('/reservations/{id}/items/{itemId}/retour', [AdminReservationController::class, 'validerRetour']);
    $group->post('/reservations/{id}/valider-retrait',      [AdminReservationController::class, 'validerRetrait']);
    $group->post('/reservations/{id}/annuler',              [AdminReservationController::class, 'annuler']);
    $group->get('/reservations/{id}/edit',                  [AdminReservationController::class, 'edit']);
    $group->post('/reservations/{id}/edit',                 [AdminReservationController::class, 'editPost']);

    $group->get('/rdv',                  AdminRendezVousController::class);
    $group->get('/rdv/{id}',             [AdminRendezVousController::class, 'detail']);
    $group->get('/rdv/{id}/edit',        [AdminRendezVousController::class, 'edit']);
    $group->post('/rdv/{id}/edit',       [AdminRendezVousController::class, 'editPost']);
    $group->post('/rdv/{id}/annuler',    [AdminRendezVousController::class, 'annuler']);
    $group->post('/rdv/{id}/realise',    [AdminRendezVousController::class, 'marquerRealise']);
    $group->post('/rdv/{id}/non-honore', [AdminRendezVousController::class, 'marquerNonHonore']);

    $group->get('/stats', StatsController::class);

})->add(new RoleMiddleware('administrateur'));