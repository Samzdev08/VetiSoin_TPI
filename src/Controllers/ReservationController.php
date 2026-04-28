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
use App\Models\Reservation;
use App\Models\ReservationItem;

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

    function add(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $idSoignant = $_SESSION['user_id'] ?? null;

        if (!$idSoignant) {
            $_SESSION['flash']['error'] = 'Vous devez être connecté.';
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        if (!empty($data['commentaire'])) {

            $data['commentaire'] = filter_var($data['commentaire'], FILTER_SANITIZE_SPECIAL_CHARS);
        } else {
            $data['commentaire'] = null;
        }


        if (!isset($data['patient_id'], $data['date_retrait'])) {
            $_SESSION['flash']['error'] = 'Données manquantes pour ajouter la réservation.';
            return $response->withHeader('Location', '/reservations/checkout')->withStatus(302);
        }

        $reservation = new Reservation(null, $idSoignant, $data['patient_id'], $data['date_retrait'], $data['commentaire']);

        $reservationId = $reservation->create();

        if (!$reservationId) {
            $_SESSION['flash']['error'] = 'Erreur lors de la création de la réservation.';
            return $response->withHeader('Location', '/reservations/checkout')->withStatus(302);
        }

        foreach ($_SESSION['cart'] as $item) {

            $reservationItem = new ReservationItem(
                null,
                $reservationId,
                $item['variante_id'],
                $item['quantite'],
                $item['taille']
            );

            $reservationItemId = $reservationItem->create();

            if (!$reservationItemId) {
                $_SESSION['flash']['error'] = 'Erreur lors de l\'ajout de l\'article à la réservation.';
                return $response->withHeader('Location', '/reservations/checkout')->withStatus(302);
            }
        }


        $_SESSION['flash']['success'] = 'Réservation créée avec succès.';
        return $response->withHeader('Location', '/catalogue')->withStatus(302);
    }
}
