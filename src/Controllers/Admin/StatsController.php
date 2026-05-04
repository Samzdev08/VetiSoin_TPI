<?php

/**
 * Fichier : StatsController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Statistiques d'activité (admin)
 */

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Stats;

class StatsController
{
    public function __construct() {}

    public function __invoke(Request $request, Response $response): Response
    {
        $dateDebut = $_GET['debut'] ?? date('Y-m-d', strtotime('-30 days'));
        $dateFin   = $_GET['fin']   ?? date('Y-m-d');

        $stats = new Stats($dateDebut, $dateFin);

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title'          => 'Statistiques',
            'dateDebut'      => $dateDebut,
            'dateFin'        => $dateFin,
            'nbReservations' => $stats->getNbReservations(),
            'articlesTop'    => $stats->getArticlesTop(),
            'categoriesTop'  => $stats->getCategoriesTop(),
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/stats/dashboard.php');
    }
}