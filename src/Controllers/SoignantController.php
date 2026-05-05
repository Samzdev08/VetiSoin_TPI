<?php

/**
 * Fichier : SoignantController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Gestion du profil du soignant connecté
 */

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Outils\Validator;
use App\Outils\Csrf;
use App\Models\Soignant;

class SoignantController
{
    public function __construct() {}


    public function __invoke(Request $request, Response $response): Response
    {
        $id = $_SESSION['user_id'] ?? null;

        if (!$id) {
            $_SESSION['flash']['error'] = 'Vous devez être connecté.';
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }

        $soignant = (new Soignant($id, null, null, null, null, null, null))->getById();

        if (empty($soignant)) {
            $_SESSION['flash']['error'] = 'Profil introuvable.';
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }

        Csrf::generate();

        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title'    => 'Mon profil',
            'soignant' => $soignant,
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/users/form.php');
    }


    public function updateInfos(Request $request, Response $response): Response
    {
        $errors = [];
        $id = $_SESSION['user_id'] ?? null;

        if (!$id) {
            $_SESSION['flash']['error'] = 'Vous devez être connecté.';
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }

        $data = filter_input_array(INPUT_POST, [
            'nom'       => FILTER_SANITIZE_SPECIAL_CHARS,
            'prenom'    => FILTER_SANITIZE_SPECIAL_CHARS,
            'email'     => FILTER_SANITIZE_EMAIL,
            'service'   => FILTER_SANITIZE_SPECIAL_CHARS,
            'telephone' => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        $csrf_token = $_POST['csrf_token'] ?? null;

        if (!Csrf::check($csrf_token)) {
            $errors[] = 'Token invalide.';
        }

        // Trim pour respecter A14 (espaces inutiles)
        $nom       = trim($data['nom']       ?? '');
        $prenom    = trim($data['prenom']    ?? '');
        $email     = trim($data['email']     ?? '');
        $service   = trim($data['service']   ?? '');
        $telephone = trim($data['telephone'] ?? '');

        if (
            !Validator::isNotEmpty($nom) ||
            !Validator::isNotEmpty($prenom) ||
            !Validator::isNotEmpty($email) ||
            !Validator::isNotEmpty($service)
        ) {
            $errors[] = 'Tous les champs obligatoires doivent être remplis.';
        }

        if (!Validator::isEmail($email)) {
            $errors[] = 'Adresse e-mail invalide.';
        }

        if (preg_match('/[0-9!@#$%^&*()-+]/', $nom)) {
            $errors[] = 'Le nom ne doit pas contenir de chiffres ou de caractères spéciaux.';
        }

        if (preg_match('/[0-9!@#$%^&*()-+]/', $prenom)) {
            $errors[] = 'Le prénom ne doit pas contenir de chiffres ou de caractères spéciaux.';
        }

        if (Validator::isNotEmpty($telephone) && !preg_match('/^[0-9]{10,11}$/', $telephone)) {
            $errors[] = 'Entrez un numéro de téléphone valide.';
        }

        $servicesValides = ['Urgences', 'Chirurgie', 'Médecine interne'];
        if (!in_array($service, $servicesValides)) {
            $errors[] = 'Service invalide.';
        }

        if (!empty($errors)) {
            $_SESSION['flash']['error'] = $errors[0];
            return $response->withHeader('Location', '/profil/')->withStatus(302);
        }

        $soignantObj = new Soignant($id, $nom, $prenom, $email, null, $service, $telephone);

        if (!$soignantObj->isUnique()) {
            $_SESSION['flash']['error'] = 'Cet email est déjà utilisé par un autre compte.';
            return $response->withHeader('Location', '/profil/')->withStatus(302);
        }

        if (!$soignantObj->update()) {
            $_SESSION['flash']['error'] = 'Erreur lors de la mise à jour du profil.';
            return $response->withHeader('Location', '/profil/')->withStatus(302);
        }

        $_SESSION['flash']['success'] = 'Profil mis à jour avec succès.';
        return $response->withHeader('Location', '/profil/')->withStatus(302);
    }


    public function changePassword(Request $request, Response $response): Response
    {
        $errors = [];
        $id = $_SESSION['user_id'] ?? null;

        if (!$id) {
            $_SESSION['flash']['error'] = 'Vous devez être connecté.';
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }

        $data = filter_input_array(INPUT_POST, [
            'current_password' => FILTER_SANITIZE_SPECIAL_CHARS,
            'new_password'     => FILTER_SANITIZE_SPECIAL_CHARS,
            'confirm_password' => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        $csrf_token = $_POST['csrf_token'] ?? null;

        if (!Csrf::check($csrf_token)) {
            $errors[] = 'Token invalide.';
        }

        if (
            !Validator::isNotEmpty($data['current_password']) ||
            !Validator::isNotEmpty($data['new_password']) ||
            !Validator::isNotEmpty($data['confirm_password'])
        ) {
            $errors[] = 'Tous les champs sont requis.';
        }

        if ($data['new_password'] !== $data['confirm_password']) {
            $errors[] = 'Le nouveau mot de passe et la confirmation ne correspondent pas.';
        }

        if (!Validator::minLength($data['new_password'], 8)) {
            $errors[] = 'Min. 8 caractères pour le mot de passe.';
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W])[^\s]{8,}$/', $data['new_password'])) {
            $errors[] = 'Le mot de passe doit contenir au moins 1 chiffre, 1 majuscule et 1 caractère spécial.';
        }

        if (!empty($errors)) {
            $_SESSION['flash']['error'] = $errors[0];
            return $response->withHeader('Location', '/profil/')->withStatus(302);
        }

        $soignantObj = new Soignant($id, null, null, null, $data['current_password'], null, null);
        $changeResult = $soignantObj->changePassword($data['new_password']);

        if (!$changeResult['success']) {
            $_SESSION['flash']['error'] = $changeResult['message'];
            return $response->withHeader('Location', '/profil/')->withStatus(302);
        }

        $_SESSION['flash']['success'] = 'Mot de passe changé avec succès.';
        return $response->withHeader('Location', '/profil/')->withStatus(302);
    }
}