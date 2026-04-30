<?php

/**
 * Fichier : ReservationController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Gestion des réservations par l'admin
 */

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Reservation;
use App\Models\Soignant;

class ReservationControllerAdmin
{
    public function __invoke(Request $request, Response $response): Response
    {
        $statut     = $_GET['statut'] ?? null;
        $idSoignant = $_GET['soignant'] ?? null;
        $service    = $_GET['service'] ?? null;
        $date       = $_GET['date'] ?? null;

        $reservationObj = new Reservation(null, $idSoignant, null, $date, $statut, null);

       // $reservations = $reservationObj->getAllAdmin();

        
        $soignantObj = new Soignant(null, null, null, null, null, null, null);
        $soignants = $soignantObj->getAll();

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title'        => 'Réservations',
          //  'reservations' => $reservations,
            'soignants'    => $soignants,
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/reservations/list.php');
    }


     public function validerRetrait(Request $request, Response $response, $args): Response
    {
        $reservationObj = new Reservation($args['id'], null, null, null, null, null);
        $success = $reservationObj->validerRetrait();

        if ($success) {
            $_SESSION['flash']['success'] = 'Retrait validé avec succès.';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de la validation du retrait.';
        }

        return $response->withHeader('Location', '/admin/reservations')->withStatus(302);
    }

    public function annuler(Request $request, Response $response, $args): Response
    {
        $reservationObj = new Reservation($args['id'], null, null, null, null, null);
        $success = $reservationObj->cancel();

        if ($success) {
            $_SESSION['flash']['success'] = 'Réservation annulée.';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de l\'annulation.';
        }

        return $response->withHeader('Location', '/admin/reservations')->withStatus(302);
    }
}
