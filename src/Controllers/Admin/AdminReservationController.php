<?php

/**
 * Fichier : AdminReservationController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Gestion des réservations par l'admin
 */

namespace App\Controllers\Admin;

use App\Models\RendezVous;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\Soignant;
use App\Models\Notification;
use App\Outils\Csrf;

class AdminReservationController
{

    public function __invoke(Request $request, Response $response): Response
    {
        $statut     = $_GET['statut'] ?? null;
        $idSoignant = $_GET['soignant'] ?? null;
        $service    = $_GET['service']  ?? null;
        $date       = $_GET['date']  ?? null;

        $reservationObj = new Reservation(null, $idSoignant, null, $date, $statut, null);
        $reservations   = $reservationObj->getAllAdmin($service);

        $soignantObj = new Soignant(null, null, null, null, null, null, null);
        $soignants   = $soignantObj->getAll();

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title'        => 'Réservations',
            'reservations' => $reservations,
            'soignants'    => $soignants,
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/reservations/list.php');
    }

    public function detail(Request $request, Response $response, $args): Response
    {
        $idReservation = $args['id'] ?? null;

        if (!$idReservation) {
            $_SESSION['flash']['error'] = 'ID de réservation manquant.';
            return $response->withHeader('Location', '/admin/reservations')->withStatus(302);
        }

        $reservationObj = new Reservation($idReservation, null, null, null, null, null);
        $reservations   = $reservationObj->getReservationById();

        if (empty($reservations)) {
            $_SESSION['flash']['error'] = 'Réservation introuvable.';
            return $response->withHeader('Location', '/admin/reservations')->withStatus(302);
        }

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title'        => 'Détail de la réservation',
            'reservations' => $reservations,
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/reservations/detail.php');
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

        return $response->withHeader('Location', '/admin/reservations/' . $args['id'])->withStatus(302);
    }


    public function annuler(Request $request, Response $response, $args): Response
    {
        $idReservation = $args['id'] ?? null;

        if (!$idReservation) {
            $_SESSION['flash']['error'] = 'ID de réservation manquant.';
            return $response->withHeader('Location', '/admin/reservations')->withStatus(302);
        }


        $reservationObj = new Reservation($idReservation, null, null, null, null, null);
        $infos = $reservationObj->getReservationById();

        if (empty($infos)) {
            $_SESSION['flash']['error'] = 'Réservation introuvable.';
            return $response->withHeader('Location', '/admin/reservations')->withStatus(302);
        }



        $rdvObj = new RendezVous(null, $idReservation, null, null, null, null);
        $reservationItems = new ReservationItem(null, $idReservation, null, null);

        $items = $reservationItems->findById();
        $idRdv = $rdvObj->getIdByReservation();

        if (!$idRdv) {
            $_SESSION['flash']['error'] = 'Aucun rendez-vous pour cette réservation';
            return $response->withHeader('Location', '/admin/reservations')->withStatus(302);
        }

        foreach ($items as $i) {
            $item = new ReservationItem($i['id'], null, null, $i['quantite']);
            $item->retourner();
        }


        $success = $reservationObj->cancel();
        $rdvObj->cancel($idRdv);

        if ($success) {
            $_SESSION['flash']['success'] = 'Réservation annulée.';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de l\'annulation.';
        }

        return $response->withHeader('Location', '/admin/reservations/' . $idReservation)->withStatus(302);
    }



    public function validerRetour(Request $request, Response $response, $args): Response
    {
        $idItem = $args['itemId'] ?? null;
        $idReservation = $args['id'] ?? null;

        if (!$idItem || !$idReservation) {
            $_SESSION['flash']['error'] = 'Paramètres manquants.';
            return $response->withHeader('Location', '/admin/reservations')->withStatus(302);
        }


        $itemObj = new ReservationItem($idItem, null, null, null);

        $row = $itemObj->getById();

        if (!$row) {
            $_SESSION['flash']['error'] = 'Article introuvable.';
            return $response->withHeader('Location', '/admin/reservations/' . $idReservation)->withStatus(302);
        }

        $itemObj = new ReservationItem($idItem, null, $row['id_article_variante'], $row['quantite']);
        $success = $itemObj->retourner();

        if ($success) {
            $_SESSION['flash']['success'] = 'Retour validé.';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de la validation du retour (déjà retourné ?).';
        }

        return $response->withHeader('Location', '/admin/reservations/' . $idReservation)->withStatus(302);
    }

    public function edit(Request $request, Response $response, $args): Response
    {
        $idReservation = $args['id'] ?? null;

        if (!$idReservation) {
            $_SESSION['flash']['error'] = 'ID de réservation manquant.';
            return $response->withHeader('Location', '/admin/reservations')->withStatus(302);
        }

        $reservationObj = new Reservation($idReservation, null, null, null, null, null);
        $reservation    = $reservationObj->getReservationById();

        if (empty($reservation)) {
            $_SESSION['flash']['error'] = 'Réservation introuvable.';
            return $response->withHeader('Location', '/admin/reservations')->withStatus(302);
        }

        Csrf::generate();

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title'       => 'Modifier la réservation',
            'reservation' => $reservation,
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/reservations/edit.php');
    }

    public function editPost(Request $request, Response $response, $args): Response
    {
        $idReservation = $args['id'] ?? null;

        if (!$idReservation) {
            $_SESSION['flash']['error'] = 'ID de réservation manquant.';
            return $response->withHeader('Location', '/admin/reservations')->withStatus(302);
        }

        $data = filter_input_array(INPUT_POST, [
            'csrf_token'  => FILTER_SANITIZE_SPECIAL_CHARS,
            'statut' => FILTER_SANITIZE_SPECIAL_CHARS,
            'date_retrait' => FILTER_SANITIZE_SPECIAL_CHARS,
            'commentaire' => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        if (!Csrf::check($data['csrf_token'] ?? '')) {
            $_SESSION['flash']['error'] = 'Token invalide.';
            return $response->withHeader('Location', '/admin/reservations/' . $idReservation . '/edit')->withStatus(302);
        }




        $statuts = ['En attente', 'Confirmée', 'Clôturée', 'Annulée'];
        if (!in_array($data['statut'], $statuts, true)) {
            $_SESSION['flash']['error'] = 'Statut invalide.';
            return $response->withHeader('Location', '/admin/reservations/' . $idReservation . '/edit')->withStatus(302);
        }

        $reservationObj = new Reservation($idReservation, null, null, $data['date_retrait'], $data['statut'], $data['commentaire']);
        $success = $reservationObj->updateAdmin();

        if ($success) {
            $_SESSION['flash']['success'] = 'Réservation modifiée avec succès.';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de la modification.';
        }

        return $response->withHeader('Location', '/admin/reservations/' . $idReservation)->withStatus(302);
    }
}
