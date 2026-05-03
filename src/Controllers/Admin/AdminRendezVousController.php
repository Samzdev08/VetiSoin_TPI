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
use App\Models\Soignant;

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

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title'      => 'Détail du rendez-vous',
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

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title'      => 'Modifier le rendez-vous',
            'rendezVous' => $rdv,
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/appointments/edit.php');
    }


    public function editPost(Request $request, Response $response, $args): Response
    {
        $data  = $request->getParsedBody();
        $idRdv = $args['id'] ?? null;

        if (!$idRdv) {
            $_SESSION['flash']['error'] = 'ID de rendez-vous manquant.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        if (empty($data['date_rdv']) || empty($data['heure_rdv']) || empty($data['lieu'])) {
            $_SESSION['flash']['error'] = 'Veuillez choisir une date, une heure et un lieu.';
            return $response->withHeader('Location', '/admin/rdv/' . $idRdv . '/edit')->withStatus(302);
        }

        // Les values du <select> sont déjà au format H:i:s
        $horaires = ['08:00:00', '10:00:00', '11:30:00', '14:30:00', '16:00:00'];

        if (!in_array($data['heure_rdv'], $horaires)) {
            $_SESSION['flash']['error'] = 'Cette heure n\'est pas disponible.';
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

       
        $aChange = (
            $current['date_rdv']  !== $dateRdv ||
            $current['heure_rdv'] !== $heureRdv ||
            $current['lieu']      !== $data['lieu']
        );

        if ($aChange && $rendezVous->isCreneauPris()) {
            $_SESSION['flash']['error'] = 'Ce créneau est déjà réservé. Merci d\'en choisir un autre.';
            return $response->withHeader('Location', '/admin/rdv/' . $idRdv . '/edit')->withStatus(302);
        }

        if (!$rendezVous->update()) {
            $_SESSION['flash']['error'] = 'Erreur lors de la modification (le RDV doit être au statut Planifié).';
            return $response->withHeader('Location', '/admin/rdv/' . $idRdv . '/edit')->withStatus(302);
        }

        $_SESSION['flash']['success'] = 'Rendez-vous modifié avec succès.';
        return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
    }

    public function annuler(Request $request, Response $response, $args): Response
    {
        $idRdv = $args['id'] ?? null;

        if (!$idRdv) {
            $_SESSION['flash']['error'] = 'ID de rendez-vous manquant.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        $rdvObj  = new RendezVous($idRdv, null, null, null, null);
        $success = $rdvObj->cancel($idRdv);

        if ($success) {
            $_SESSION['flash']['success'] = 'Rendez-vous annulé.';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de l\'annulation.';
        }

        return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
    }


    public function marquerRealise(Request $request, Response $response, $args): Response
    {
        $idRdv = $args['id'] ?? null;

        if (!$idRdv) {
            $_SESSION['flash']['error'] = 'ID de rendez-vous manquant.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        $rdvObj  = new RendezVous($idRdv, null, null, null, null);
        $success = $rdvObj->marquerRealise();

        if ($success) {
            $_SESSION['flash']['success'] = 'Rendez-vous marqué comme réalisé.';
        } else {
            $_SESSION['flash']['error'] = 'Action impossible (le RDV doit être au statut Planifié).';
        }

        return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
    }


    public function marquerNonHonore(Request $request, Response $response, $args): Response
    {
        $idRdv = $args['id'] ?? null;

        if (!$idRdv) {
            $_SESSION['flash']['error'] = 'ID de rendez-vous manquant.';
            return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
        }

        $rdvObj  = new RendezVous($idRdv, null, null, null, null);
        $success = $rdvObj->marquerNonHonore();

        if ($success) {
            $_SESSION['flash']['success'] = 'Rendez-vous marqué comme non honoré.';
        } else {
            $_SESSION['flash']['error'] = 'Action impossible (le RDV doit être au statut Planifié).';
        }

        return $response->withHeader('Location', '/admin/rdv')->withStatus(302);
    }
}