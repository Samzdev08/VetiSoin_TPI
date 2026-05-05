<?php

/**
 * Fichier : PatientController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : CRUD patients cote soignant
 */

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Outils\Csrf;
use App\Outils\Validator;
use App\Models\Patient;

class PatientController
{
    public function __invoke(Request $request, Response $response): Response
    {
        $_GET['nom'] = $_GET['nom'] ?? null;
        $_GET['numeroDossier'] = $_GET['numeroDossier'] ?? null;
        $patient = new Patient(
            null,
            $_GET['nom'],
            null,
            null,
            null,
            $_GET['numeroDossier'],
            null,
            null,
            null
        );
        $patients = $patient->getAll();

        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title' => 'Patients',
            'patients' => $patients
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/patients/list.php');
    }

    public function read(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $patient = new Patient($id, null, null, null, null, null, null, null, null);
        $statutPatient = $patient->getStatut();
        $patientData = $patient->getById($statutPatient);

        if (!$patientData) {
            $response->getBody()->write('Patient non trouvé');
            return $response->withStatus(404);
        }

        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title' => 'Détails du patient',
            'patient' => $patientData
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/patients/detail.php');
    }


    public function form(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? null;
        $patientData = null;

        if ($id) {
            $patient = new Patient($id, null, null, null, null, null, null, null, null);
            $patientData = $patient->getById();
        }

        Csrf::generate();
        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title' => $id ? 'Modifier le patient' : 'Ajouter un patient',
            'patient' => $patientData,
            'id' => $id
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/patients/form.php');
    }

    public function createUpdate(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? null;
        $data = filter_input_array(INPUT_POST, [
            'nom' => FILTER_SANITIZE_SPECIAL_CHARS,
            'prenom' => FILTER_SANITIZE_SPECIAL_CHARS,
            'date_naissance' => FILTER_SANITIZE_SPECIAL_CHARS,
            'genre' => FILTER_SANITIZE_SPECIAL_CHARS,
            'numeroDossier' => FILTER_SANITIZE_SPECIAL_CHARS,
            'service' => FILTER_SANITIZE_SPECIAL_CHARS,
            'statut' => FILTER_SANITIZE_SPECIAL_CHARS,
            'chambre' => FILTER_SANITIZE_SPECIAL_CHARS,
            'csrf_token' => FILTER_SANITIZE_SPECIAL_CHARS
        ]);

       
        if (!Csrf::check($_POST['csrf_token'] ?? $data['csrf_token'])) {
            $_SESSION['flash']['error'] = 'Token CSRF invalide';
            return $response->withHeader('Location', $id ? "/patient/form/$id/edit" : '/patient/form')->withStatus(302);
        }

        if (
            !Validator::isNotEmpty($data['nom']) ||
            !Validator::isNotEmpty($data['prenom']) ||
            !Validator::isNotEmpty($data['date_naissance']) ||
            !Validator::isNotEmpty($data['genre']) ||
            !Validator::isNotEmpty($data['numeroDossier'])
        ) {

            $_SESSION['flash']['error'] = 'Tous les champs sont requis.';
            return $response->withHeader('Location', $id ? "/patient/form/$id/edit" : '/patient/form')->withStatus(302);
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date_naissance'])) {
            $_SESSION['flash']['error'] = 'La date de naissance doit être au format YYYY-MM-DD.';
            return $response->withHeader('Location', $id ? "/patient/form/$id/edit" : '/patient/form')->withStatus(302);
        }

        if (time() < strtotime($data['date_naissance'])) {
            $_SESSION['flash']['error'] = 'La date de naissance ne peut pas être dans le futur.';
            return $response->withHeader('Location', $id ? "/patient/form/$id/edit" : '/patient/form')->withStatus(302);
        }

        if(!preg_match('/^DOS-2026-\d+$/', $data['numeroDossier'])) {
            $_SESSION['flash']['error'] = 'Le numéro de dossier doit être au format DOS-2026-XXX.';
            return $response->withHeader('Location', $id ? "/patient/form/$id/edit" : '/patient/form')->withStatus(302);
        }


        $patient = new Patient(
            $id,
            $data['nom'] ?? null,
            $data['prenom'] ?? null,
            $data['date_naissance'] ?? null,
            $data['genre'] ?? null,
            $data['numeroDossier'] ?? null,
            $data['service'] ?? null,
            $data['chambre'] ?? null,
            $data['statut'] ?? null
        );

        if(!$patient->isNumeroDossierUnique()){

            $_SESSION['flash']['error'] = 'Le numéro de dossier existe déjà.';
            return $response->withHeader('Location', $id ? "/patient/form/$id/edit" : '/patient/form')->withStatus(302);
        }

        if ($id) {
            $patient->update();
        } else {
            $patient->create();
        }

        $_SESSION['flash']['success'] = $id ? 'Patient mis à jour avec succès.' : 'Patient ajouté avec succès.';
        return $response->withHeader('Location', '/patients')->withStatus(302);
    }

  
}
