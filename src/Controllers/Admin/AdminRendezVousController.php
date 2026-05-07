<?php

/**
 * Fichier : AdminRendezVousController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Gestion des rendez-vous par l'admin
 */

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\RendezVous;
use App\Models\Reservation;
use App\Models\Soignant;
use App\Outils\Csrf;
use App\Outils\Validator;
use App\Models\Notification;

class AdminRendezVousController
{
    public function __construct() {}


    public function __invoke(Request $request, Response $response): Response
    {
        $statut     = $_GET['statut'] ?? null;
        $idSoignant = $_GET['soignant'] ?? null;
        $service    = $_GET['service'] ?? null;
        $date       = $_GET['date'] ?? null;

        $rdvObj = new RendezVous(null, null, $date, null, null, $statut, $idSoignant);
        $rdvs   = $rdvObj->getAllAdmin($service);

        $soignantObj = new Soignant(null, null, null, null, null, null, null);
        $soignants   = $soignantObj->getAll();

        Csrf::generate();

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title'      => 'Rendez-vous',
            'rendezVous' => $rdvs,
            'soignants'  => $soignants,
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/appointments/list.php');
    }

    public function detail(Request $request, Response $response, $args): Response
    {
        $idRdv = $args['id'] ?? null;

        if (!$idRdv) {
            $_SESSION['flash']['error'] = 'ID de rendez-vous manquant.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        $rdvObj = new RendezVous($idRdv, null, null, null, null);
        $rdv    = $rdvObj->getRendezVousById();

        if (empty($rdv)) {
            $_SESSION['flash']['error'] = 'Rendez-vous introuvable.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        Csrf::generate();

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title' => 'Détail du rendez-vous',
            'rendezVous' => $rdv,
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/appointments/detail.php');
    }


    public function edit(Request $request, Response $response, $args): Response
    {
        $idRdv = $args['id'] ?? null;

        if (!$idRdv) {
            $_SESSION['flash']['error'] = 'ID de rendez-vous manquant.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        $rdvObj = new RendezVous($idRdv, null, null, null, null);
        $rdv = $rdvObj->getRendezVousById();

        if (!$rdv) {
            $_SESSION['flash']['error'] = 'Rendez-vous introuvable.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        if ($rdv['statut'] !== 'Planifié') {
            $_SESSION['flash']['error'] = 'Seuls les rendez-vous planifiés peuvent être modifiés.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        Csrf::generate();

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title' => 'Modifier le rendez-vous',
            'rendezVous' => $rdv,
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/appointments/edit.php');
    }


    public function editPost(Request $request, Response $response, $args): Response
    {
        $errors = [];
        $idRdv  = $args['id'] ?? null;

        if (!$idRdv) {
            $_SESSION['flash']['error'] = 'ID de rendez-vous manquant.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        $data = filter_input_array(INPUT_POST, [
            'date_rdv'  => FILTER_SANITIZE_SPECIAL_CHARS,
            'heure_rdv' => FILTER_SANITIZE_SPECIAL_CHARS,
            'lieu' => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        $csrf_token = $_POST['csrf_token'] ?? null;

        if (!Csrf::check($csrf_token)) {
            $errors[] = 'Token invalide.';
        }

        if (
            !Validator::isNotEmpty($data['date_rdv']) ||
            !Validator::isNotEmpty($data['heure_rdv']) ||
            !Validator::isNotEmpty($data['lieu'])
        ) {
            $errors[] = 'Veuillez choisir une date, une heure et un lieu.';
        }

        $horaires = ['08:00:00', '10:00:00', '11:30:00', '14:30:00', '16:00:00'];
        if (Validator::isNotEmpty($data['heure_rdv']) && !in_array($data['heure_rdv'], $horaires)) {
            $errors[] = 'Cette heure n\'est pas disponible.';
        }

        $lieux = ['Vestiaire principal', 'Secrétariat'];
        if (Validator::isNotEmpty($data['lieu']) && !in_array($data['lieu'], $lieux)) {
            $errors[] = 'Lieu invalide.';
        }

        if (!empty($errors)) {
            $_SESSION['flash']['error'] = $errors[0];
            return $response->withHeader('Location', '/admin/rdv/' . $idRdv . '/edit')->withStatus(302);
        }

        $dateRdv  = $data['date_rdv'];
        $heureRdv = $data['heure_rdv'];

        $timestamp = strtotime($dateRdv . ' ' . $heureRdv);

        if ($timestamp === false || $timestamp < time()) {
            $_SESSION['flash']['error'] = 'Le rendez-vous doit être dans le futur.';
            return $response->withHeader('Location', '/admin/rdv/' . $idRdv . '/edit')->withStatus(302);
        }

        if ($timestamp > strtotime('+7 days')) {
            $_SESSION['flash']['error'] = 'Le rendez-vous doit être dans les 7 prochains jours.';
            return $response->withHeader('Location', '/admin/rdv/' . $idRdv . '/edit')->withStatus(302);
        }

        $current = (new RendezVous($idRdv, null, null, null, null))->getRendezVousById();

        if (empty($current)) {
            $_SESSION['flash']['error'] = 'Rendez-vous introuvable.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        $rendezVous = new RendezVous($idRdv, null, $dateRdv, $heureRdv, $data['lieu']);

        $hasChange = (
            $current['date_rdv']  !== $dateRdv ||
            $current['heure_rdv'] !== $heureRdv ||
            $current['lieu'] !== $data['lieu']
        );

        if ($hasChange && $rendezVous->isCreneauPris()) {
            $_SESSION['flash']['error'] = 'Ce créneau est déjà réservé. Merci d\'en choisir un autre.';
            return $response->withHeader('Location', '/admin/rdv/' . $idRdv . '/edit')->withStatus(302);
        }

        if (!$rendezVous->update()) {
            $_SESSION['flash']['error'] = 'Erreur lors de la modification (le RDV doit être au statut Planifié).';
            return $response->withHeader('Location', '/admin/rdv/' . $idRdv . '/edit')->withStatus(302);
        }

        $_SESSION['flash']['success'] = 'Rendez-vous modifié avec succès.';


        if (!empty($current['id_soignant'])) {
            $titre   = 'Rendez-vous modifié';
            $message = "Votre rendez-vous #{$idRdv} a été modifié par l'administrateur. ";
            $message .= "Nouvelle date : " . date('d.m.Y', strtotime($dateRdv));
            $message .= " à " . substr($heureRdv, 0, 5);
            $message .= " ({$data['lieu']}).";

            (new Notification(null, $current['id_soignant'], 'Rappel rendez-vous', $titre, $message))->create();
        }




        return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
    }

    public function annuler(Request $request, Response $response, $args): Response
    {
        $csrf_token = $_POST['csrf_token'] ?? null;
        if (!Csrf::check($csrf_token)) {
            $_SESSION['flash']['error'] = 'Token de sécurité invalide.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        $idRdv = $args['id'] ?? null;

        if (!$idRdv) {
            $_SESSION['flash']['error'] = 'ID de rendez-vous manquant.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        $rdvObj  = new RendezVous($idRdv, null, null, null, null);

        $infos = $rdvObj->getRendezVousById();

        $success = $rdvObj->cancel($idRdv);

        if ($success) {

            $_SESSION['flash']['success'] = 'Rendez-vous annulé.';

            if (!empty($infos['id_soignant'])) {
                $titre   = 'Rendez-vous annulé';
                $message = "Votre rendez-vous #{$idRdv} (réservation #{$infos['id_reservation']}) ";
                $message .= "a été annulé par l'administrateur.";
 
                (new Notification(null, $infos['id_soignant'], 'Rappel rendez-vous', $titre, $message))->create();
            }

        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de l\'annulation.';
        }

        return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
    }


    public function marquerRealise(Request $request, Response $response, $args): Response
    {
        $csrf_token = $_POST['csrf_token'] ?? null;
        if (!Csrf::check($csrf_token)) {
            $_SESSION['flash']['error'] = 'Token de sécurité invalide.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        $idRdv = $args['id'] ?? null;

        if (!$idRdv) {
            $_SESSION['flash']['error'] = 'ID de rendez-vous manquant.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        $rdvObj  = new RendezVous($idRdv, null, null, null, null);

        $idReservation = $rdvObj->getIdReservation();
        $success = $rdvObj->marquerRealise();



        if ($success) {

            (new Reservation($idReservation, null, null, null, null, null))->cloturee();
            $_SESSION['flash']['success'] = 'Rendez-vous marqué comme réalisé.';
        } else {
            $_SESSION['flash']['error'] = 'Action impossible (le RDV doit être au statut Planifié).';
        }

        return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
    }


    public function marquerNonHonore(Request $request, Response $response, $args): Response
    {
        $csrf_token = $_POST['csrf_token'] ?? null;
        if (!Csrf::check($csrf_token)) {
            $_SESSION['flash']['error'] = 'Token de sécurité invalide.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        $idRdv = $args['id'] ?? null;

        if (!$idRdv) {
            $_SESSION['flash']['error'] = 'ID de rendez-vous manquant.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        $rdvObj  = new RendezVous($idRdv, null, null, null, null);

        $infos = $rdvObj->getRendezVousById();

        $success = $rdvObj->marquerNonHonore();

        if ($success) {

            $_SESSION['flash']['success'] = 'Rendez-vous marqué comme non honoré.';

            if (!empty($infos['id_soignant'])) {
                $titre   = 'Rendez-vous manqué';
                $message = "Votre rendez-vous #{$idRdv} (réservation #{$infos['id_reservation']}) ";
                $message .= "a été marqué comme non honoré.";
 
                (new Notification(null, $infos['id_soignant'], 'Rappel rendez-vous', $titre, $message))->create();
            }

        } else {
            $_SESSION['flash']['error'] = 'Action impossible (le RDV doit être au statut Planifié).';
        }

        return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
    }
}
