<?php

/**
 * Fichier : ReservationController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Panier et reservations du soignant
 */

namespace App\Controllers;

use App\Outils\Router;
use App\Outils\View;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Patient;

class ReservationController
{
    public function __construct() {}


    public function checkout(Request $request, Response $response): Response
    {
        $nom = $_GET['recherche'] ?? null;
        $service = $_GET['service'] = $_GET['service'] ?? null;

        $patient = new Patient(
            null,
            $nom,
            null,
            null,
            null,
            null,
            $service,
            null,
            null
        );
        $patients = $patient->getAll();


        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title' => 'Ma réservation',
            'paniers' => $_SESSION['cart'] ?? [],
            'patients' => $patients
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/reservations/checkout.php');


        return $view->render($response, '/reservations/checkout.php');
    }
}
