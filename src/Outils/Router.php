<?php
/**
 * Fichier : Router.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Routage des requetes HTTP
 */

use App\Controllers\AuthController;
use App\Controllers\CatalogController;



$app->get('/', AuthController::class);
$app->get('/catalogue', CatalogController::class);

$group = $app->group('/auth', function ($group) {
    
    $group->get('/register', [AuthController::class, 'showRegisterForm']);
    $group->get('/login', [AuthController::class, 'showLoginForm']);
    $group->post('/create', [AuthController::class, 'create']);
    $group->post('/login/post', [AuthController::class, 'login']);
    $group->get('/logout', [AuthController::class, 'logout']);


});