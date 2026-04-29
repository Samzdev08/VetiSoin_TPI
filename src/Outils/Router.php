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
use App\Controllers\NotificationController;



$app->get('/', AuthController::class);
$app->get('/catalogue', CatalogController::class);
$app->get('/catalogue/{id}', [CatalogController::class, 'detail']);
$app->get('/patients', PatientController::class);

$app->get('/panier', PanierController::class);
$app->post('/panier/add', [PanierController::class, 'addToCart']);
$app->post('/panier/remove/{id}', [PanierController::class, 'removeFromCart']);
$app->post('/panier/update/{id}', [PanierController::class, 'updateCart']);
$app->get('/panier/vider', [PanierController::class, 'clearCart']);


$group = $app->group('/reservations', function ($group) {

    $group->get('', ReservationController::class);
    $group->get('/checkout', [ReservationController::class, 'checkout']);
    $group->get('/{id}', [ReservationController::class, 'detail']);
    //$group->post('/update', [ReservationController::class, 'updateReservations']);
   $group->post('/add', [ReservationController::class, 'add']);
   // $group->post('/{id}/delete', [ReservationController::class, 'delete']);
});


$app->post('/setColor/{color}/{id}', [CatalogController::class, 'setColor']);

$group = $app->group('/patient', function ($group) {

    $group->get('/form', [PatientController::class, 'form']);
    $group->get('/form/{id}/edit', [PatientController::class, 'form']);
    $group->get('/{id}', [PatientController::class, 'read']);
    $group->post('/add', [PatientController::class, 'createUpdate']);
    $group->post('/{id}/update', [PatientController::class, 'createUpdate']);
    $group->post('/{id}/delete', [PatientController::class, 'delete']);
});

$group = $app->group('/auth', function ($group) {

    $group->get('/register', [AuthController::class, 'showRegisterForm']);
    $group->get('/login', [AuthController::class, 'showLoginForm']);
    $group->post('/create', [AuthController::class, 'create']);
    $group->post('/login/post', [AuthController::class, 'login']);
    $group->get('/logout', [AuthController::class, 'logout']);
});
