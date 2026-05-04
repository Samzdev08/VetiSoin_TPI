<?php

/**
 * Fichier : ReservationController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Panier et reservations du soignant
 */

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Patient;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Outils\Database;
use App\Models\Notification;
use App\Outils\Csrf;
use App\Outils\Validator;

class ReservationController
{
    public function __construct() {}

    public function __invoke(Request $request, Response $response): Response
    {
        $idSoignant = $_SESSION['user_id'];
        $statut = $_GET['statut'] ?? null;

        if (!$idSoignant) {

            $response->getBody()->write('Vous devez etre connecter !');
            return $response->withStatus(404);
        }
        $reservation = new Reservation(null, $idSoignant, null, null, $statut, null);

        $allReservations = $reservation->getReservationsBySoignantId();

        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title'   => 'Mes réservations',
            'reservations' => $allReservations,
        ]);
        $view->setLayout('layout.php');

        return $view->render($response, '/reservations/list.php');
    }

    public function detail(Request $request, Response $response, $args): Response
    {

        $idReservation = $args['id'];

        $reservation = new Reservation($idReservation, null, null, null, null, null);

        $allReservations = $reservation->getReservationById();

        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title'   => 'Détails de le réservations',
            'reservations' => $allReservations,
        ]);
        $view->setLayout('layout.php');

        return $view->render($response, '/reservations/detail.php');
    }

    public function checkout(Request $request, Response $response): Response
    {
        $nom = $_GET['recherche'] ?? null;
        $service = $_GET['service'] ?? null;

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
    }
    public function add(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $idSoignant = $_SESSION['user_id'] ?? null;

        if (!$idSoignant) {
            $_SESSION['flash']['error'] = 'Vous devez être connecté.';
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        $data['commentaire'] = !empty($data['commentaire'])
            ? filter_var($data['commentaire'], FILTER_SANITIZE_SPECIAL_CHARS)
            : null;

        if (!isset($data['patient_id'], $data['date_retrait'])) {
            $_SESSION['flash']['error'] = 'Données manquantes pour ajouter la réservation.';
            return $response->withHeader('Location', '/reservations/checkout')->withStatus(302);
        }

        $db = Database::getInstance()->getConnection();
        try {
            $db->beginTransaction();

            $stmtLock = $db->prepare("SELECT stock FROM article_variante WHERE id = ? FOR UPDATE");
            foreach ($_SESSION['cart'] as $item) {
                $stmtLock->execute([$item['variante_id']]);
                $stockReel = (int)$stmtLock->fetchColumn();
                if ($stockReel < $item['quantite']) {
                    $db->rollBack();
                    $_SESSION['flash']['error'] = "Stock insuffisant pour « {$item['nom']} » (dispo : $stockReel).";
                    return $response->withHeader('Location', '/panier')->withStatus(302);
                }
            }


            $reservation = new Reservation(null, $idSoignant, $data['patient_id'], $data['date_retrait'], null, $data['commentaire']);
            $reservationId = $reservation->create();

            if (!$reservationId) {
                $db->rollBack();
                $_SESSION['flash']['error'] = 'Erreur lors de la création de la réservation.';
                return $response->withHeader('Location', '/reservations/checkout')->withStatus(302);
            }

            foreach ($_SESSION['cart'] as $item) {
                $reservationItem = new ReservationItem(null, $reservationId, $item['variante_id'], $item['quantite']);
                if (!$reservationItem->create() || !$reservationItem->updateStock()) {
                    $db->rollBack();
                    $_SESSION['flash']['error'] = 'Erreur lors de l\'ajout d\'un article à la réservation.';
                    return $response->withHeader('Location', '/reservations/checkout')->withStatus(302);
                }
            }

            $db->commit();
            $_SESSION['cart'] = [];
            $_SESSION['flash']['success'] = 'Réservation créée avec succès.';
            return $response->withHeader('Location', '/catalogue')->withStatus(302);
        } catch (\Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            $_SESSION['flash']['error'] = 'Erreur technique lors de la création de la réservation.';
            return $response->withHeader('Location', '/reservations/checkout')->withStatus(302);
        }
    }

    public function annuler(Request $request, Response $response, $args): Response
    {
        $idReservation = $args['id'] ?? null;

        if (!$idReservation) {
            $_SESSION['flash']['error'] = 'ID de réservation manquant.';
            return $response->withHeader('Location', '/reservations')->withStatus(302);
        }

        $reservationItems = new ReservationItem(null, $idReservation, null, null);

        $items = $reservationItems->findById();

        foreach ($items as $i) {

            $item = new ReservationItem($i['id'], null, null, $i['quantite']);
            $item->retourner();
        }


        $reservation = new Reservation($idReservation, null, null, null, null, null);
        $ok = $reservation->cancel();

        if (!$ok) {
            $_SESSION['flash']['error'] = 'Erreur lors de l\'annulation de la réservation.';
        } else {
            $_SESSION['flash']['success'] = 'Réservation annulée avec succès.';
        }

        return $response->withHeader('Location', '/reservations/' . $idReservation)->withStatus(302);
    }

    public function updateForm(Request $request, Response $response, $args): Response
    {
        $idReservation = $args['id'];

        $reservation = new Reservation($idReservation, null, null, null, null, null);
        $reservationById = $reservation->getReservationById();


        if (empty($reservationById) || $reservationById['statut'] !== 'En attente') {
            $_SESSION['flash']['error'] = 'Seules les réservations en attente peuvent être modifiées.';
            return $response
                ->withHeader('Location', '/reservations/' . $idReservation)
                ->withStatus(302);
        }

        $nom = $_GET['recherche'] ?? null;
        $service = $_GET['service'] ?? null;

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

        $reservation = new Reservation($idReservation, null, null, null, null, null);

        $reservationById = $reservation->getReservationById();

        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title'   => 'Modidfication d\'une réservation',
            'reservations' => $reservationById,
            'patients' => $patients
        ]);
        $view->setLayout('layout.php');

        return $view->render($response, '/reservations/form.php');
    }

    public function editPost(Request $request, Response $response, $args): Response
    {
        $errors        = [];
        $idReservation = $args['id'] ?? null;
        $idSoignant    = $_SESSION['user_id'] ?? null;

        if (!$idSoignant) {
            $_SESSION['flash']['error'] = 'Vous devez être connecté.';
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        if (!$idReservation) {
            $_SESSION['flash']['error'] = 'ID de réservation manquant.';
            return $response->withHeader('Location', '/reservations')->withStatus(302);
        }

        $data = $request->getParsedBody();


        $csrf_token = $_POST['csrf_token'] ?? null;
        if (!Csrf::check($csrf_token)) {
            $errors[] = 'Token invalide.';
        }


        if (
            !Validator::isNotEmpty($data['patient_id'] ?? null) ||
            !Validator::isNotEmpty($data['date_retrait'] ?? null)
        ) {
            $errors[] = 'Données manquantes pour la modification.';
        }


        if (empty($data['quantite']) || !is_array($data['quantite'])) {
            $errors[] = 'Aucune quantité fournie.';
        }

        if (!empty($errors)) {
            $_SESSION['flash']['error'] = $errors[0];
            return $response->withHeader('Location', '/reservations/' . $idReservation . '/updateForm')->withStatus(302);
        }


        $commentaire = !empty($data['commentaire'])
            ? trim(filter_var($data['commentaire'], FILTER_SANITIZE_SPECIAL_CHARS))
            : null;

        $db = Database::getInstance()->getConnection();
        try {
            $db->beginTransaction();


            foreach ($data['quantite'] as $idItem => $nouvelleQuantite) {
                $nouvelleQuantite = (int)$nouvelleQuantite;

                if ($nouvelleQuantite < 1) {
                    $db->rollBack();
                    $_SESSION['flash']['error'] = 'Les quantités doivent être au moins 1.';
                    return $response->withHeader('Location', '/reservations/' . $idReservation . '/updateForm')->withStatus(302);
                }

                $item = new ReservationItem($idItem, null, null, null);
                $stockDispo = $item->getStockById();

                if ($nouvelleQuantite > $stockDispo) {
                    $db->rollBack();
                    $_SESSION['flash']['error'] = "Stock insuffisant (dispo : $stockDispo).";
                    return $response->withHeader('Location', '/reservations/' . $idReservation . '/updateForm')->withStatus(302);
                }
            }


            $reservation = new Reservation(
                $idReservation,
                $idSoignant,
                $data['patient_id'],
                $data['date_retrait'],
                null,
                $commentaire
            );

            if (!$reservation->update()) {
                $db->rollBack();
                $_SESSION['flash']['error'] = 'Erreur lors de la mise à jour de la réservation.';
                return $response->withHeader('Location', '/reservations/' . $idReservation . '/updateForm')->withStatus(302);
            }


            foreach ($data['quantite'] as $idItem => $nouvelleQuantite) {
                $item = new ReservationItem($idItem, null, null, (int)$nouvelleQuantite);
                if (!$item->updateQuantite()) {
                    $db->rollBack();
                    $_SESSION['flash']['error'] = 'Erreur lors de la mise à jour des quantités.';
                    return $response->withHeader('Location', '/reservations/' . $idReservation . '/updateForm')->withStatus(302);
                }
            }

            $db->commit();


            $titre   = 'Réservation modifiée';
            $message = "Votre réservation #{$idReservation} a été modifiée. ";
            $message .= "Nouvelle date de retrait : " . date('d.m.Y à H:i', strtotime($data['date_retrait'])) . ".";

            (new Notification(null, $idSoignant, 'Réservation confirmée', $titre, $message))->create();

            $_SESSION['flash']['success'] = 'Réservation modifiée avec succès.';
            return $response->withHeader('Location', '/reservations/' . $idReservation)->withStatus(302);
        } catch (\Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            $_SESSION['flash']['error'] = 'Erreur technique lors de la modification.';
            return $response->withHeader('Location', '/reservations/' . $idReservation . '/updateForm')->withStatus(302);
        }
    }

    public function demanderRetour(Request $request, Response $response, $args): Response
    {
        $idReservation  = $args['id'] ?? null;
        $idArticleReserve = $args['itemId'] ?? null;
        $idSoignant = $_SESSION['user_id'] ?? null;

        if (!$idReservation || !$idArticleReserve || !$idSoignant) {
            $_SESSION['flash']['error'] = 'Données manquantes.';
            return $response->withHeader('Location', '/reservations')->withStatus(302);
        }

        $reservation = new Reservation($idReservation, $idSoignant, null, null, null, null);
        $ok = $reservation->demanderRetourItem($idArticleReserve);

        if (!$ok) {
            $_SESSION['flash']['error'] = 'Impossible de signaler le retour (article déjà traité ou réservation non éligible).';
        } else {
            $_SESSION['flash']['success'] = 'Retour signalé, en attente de validation par l\'administrateur.';

            $titre   = 'Demande de retour';
            $message = "Une demande de retour a été faite pour la réservation #{$idReservation}, ";
            $message .= "article #{$idArticleReserve}. À valider.";

            $idsAdmins = new Notification(null, null, null, null, null)->getAllAdminIds();

            foreach ($idsAdmins as $idAdmin) {
                (new Notification(null, $idAdmin, 'Retour attendu', $titre, $message))->create();
            }
        }

        return $response->withHeader('Location', '/reservations/' . $idReservation)->withStatus(302);
    }
}
