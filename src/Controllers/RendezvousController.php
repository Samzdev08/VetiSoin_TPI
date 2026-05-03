<?php

/**
 * Fichier : RendezvousController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Prise et consultation de RDV par le soignant
 */

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Reservation;
use App\Outils\Database;
use App\Models\RendezVous;

class RendezvousController
{
    public function __construct() {}

   
    public function __invoke(Request $request, Response $response): Response
    {
        $idSoignant = $_SESSION['user_id'] ?? null;
        $statut     = $_GET['statut'] ?? null;

        if (!$idSoignant) {
            $_SESSION['flash']['error'] = 'Vous devez être connecté.';
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }

        $rendezVous = new RendezVous(null, null, null, null, null, $statut, $idSoignant);
        $allRdv     = $rendezVous->getRendezVousBySoignantId();

        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title'      => 'Mes rendez-vous',
            'rendezVous' => $allRdv,
        ]);
        $view->setLayout('layout.php');

        return $view->render($response, '/appointments/list.php');
    }

    /**
     * Détail d'un rendez-vous du soignant connecté
     */
    public function detail(Request $request, Response $response, $args): Response
    {
        $idRdv      = $args['id'] ?? null;
        $idSoignant = $_SESSION['user_id'] ?? null;

        if (!$idSoignant) {
            $_SESSION['flash']['error'] = 'Vous devez être connecté.';
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }

        if (!$idRdv) {
            $_SESSION['flash']['error'] = 'ID de rendez-vous manquant.';
            return $response->withHeader('Location', '/rdv')->withStatus(302);
        }

        $rendezVous = new RendezVous($idRdv, null, null, null, null);
        $rdv        = $rendezVous->getRendezVousById();

        // Le RDV doit appartenir au soignant connecté
        if (empty($rdv) || (int)$rdv['id_soignant'] !== (int)$idSoignant) {
            $_SESSION['flash']['error'] = 'Rendez-vous introuvable.';
            return $response->withHeader('Location', '/rdv')->withStatus(302);
        }

        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title'      => 'Détail du rendez-vous',
            'rendezVous' => $rdv,
        ]);
        $view->setLayout('layout.php');

        return $view->render($response, '/appointments/detail.php');
    }

    public function showRdv(Request $request, Response $response, $args): Response
    {

        $idReservation = $args['id'] ?? null;
        $reservation = new Reservation($idReservation, null, null, null, null, null);

        $infos = $reservation->getReservationById();



        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title'   => 'Mon Rdv',
            'infos' => $infos,
        ]);
        $view->setLayout('layout.php');

        return $view->render($response, '/appointments/new.php');
    }

    public function rdvPost(Request $request, Response $response, $args): Response
    {
        $data = $request->getParsedBody();
        $idReservation = $args['id'] ?? null;
        $idSoignant = $_SESSION['user_id'] ?? null;

        if (!$idSoignant) {
            $_SESSION['flash']['error'] = 'Vous devez être connecté.';
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        if (!$idReservation) {
            $_SESSION['flash']['error'] = 'ID de réservation manquant.';
            return $response->withHeader('Location', '/reservations')->withStatus(302);
        }

        if (empty($data['date_rdv']) || empty($data['lieu'])) {
            $_SESSION['flash']['error'] = 'Veuillez choisir une date, une heure et un lieu.';
            return $response->withHeader('Location', '/reservations/' . $idReservation . '/rdv')->withStatus(302);
        }
        $horaires = ['08h00', '10h00', '11h30', '14h30', '16h00'];

        $heureChoisie = date('H\hi', strtotime($data['date_rdv']));

        if (!in_array($heureChoisie, $horaires)) {
            $_SESSION['flash']['error'] = 'Cette heure n\'est pas disponible.';
            return $response->withHeader('Location', '/reservations/' . $idReservation . '/rdv')->withStatus(302);
        }


        $timestamp = strtotime($data['date_rdv']);
        if ($timestamp === false) {
            $_SESSION['flash']['error'] = 'Date du rendez-vous invalide.';
            return $response->withHeader('Location', '/reservations/' . $idReservation . '/rdv')->withStatus(302);
        }
        $dateRdv  = date('Y-m-d', $timestamp);
        $heureRdv = date('H:i:s', $timestamp);


        if ($timestamp < time()) {
            $_SESSION['flash']['error'] = 'Le rendez-vous doit être dans le futur.';
            return $response->withHeader('Location', '/reservations/' . $idReservation . '/rdv')->withStatus(302);
        }


        $rendezVous = new RendezVous(null, $idReservation, $dateRdv, $heureRdv, $data['lieu']);


        if (!$rendezVous->getIdByReservation()) {
            $_SESSION['flash']['error'] = 'Un rendez-vous existe déjà pour cette réservation.';
            return $response->withHeader('Location', '/reservations/' . $idReservation)->withStatus(302);
        }


        if ($rendezVous->isCreneauPris()) {
            $_SESSION['flash']['error'] = 'Ce créneau est déjà réservé. Merci d\'en choisir un autre.';
            return $response->withHeader('Location', '/reservations/' . $idReservation . '/rdv')->withStatus(302);
        }

        $db = Database::getInstance()->getConnection();
        try {
            $db->beginTransaction();


            if (!$rendezVous->create()) {
                $db->rollBack();
                $_SESSION['flash']['error'] = 'Erreur lors de la création du rendez-vous.';
                return $response->withHeader('Location', '/reservations/' . $idReservation . '/rdv')->withStatus(302);
            }


            $reservation = new Reservation($idReservation, null, null, null, null, null);
            if (!$reservation->confirmer()) {
                $db->rollBack();
                $_SESSION['flash']['error'] = 'Erreur lors de la confirmation de la réservation.';
                return $response->withHeader('Location', '/reservations/' . $idReservation . '/rdv')->withStatus(302);
            }

            $db->commit();
            $_SESSION['flash']['success'] = 'Rendez-vous planifié avec succès.';
            return $response->withHeader('Location', '/reservations/' . $idReservation)->withStatus(302);
        } catch (\Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            $_SESSION['flash']['error'] = 'Erreur technique lors de la création du rendez-vous.';
            return $response->withHeader('Location', '/reservations/' . $idReservation . '/rdv')->withStatus(302);
        }
    }
}