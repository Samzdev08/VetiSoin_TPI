<?php
/**
 * Fichier : index.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Front controller - point d'entree unique
 */

require __DIR__ . '/../vendor/autoload.php';


use Slim\Factory\AppFactory;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$app = AppFactory::create();

$app->add(\App\Middleware\SessionMiddleware::class);

require __DIR__ . '/../src/Outils/Router.php';

$app->run();