<?php

/**
 * Fichier : AuthController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Inscription, connexion, deconnexion
 */

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Outils\Csrf;
use App\Outils\Validator;
use App\Models\Soignant;

class AuthController
{
    private Soignant $soignant;

    public function __construct() {}


    public function __invoke(Request $request, Response $response): Response
    {
        $view = new PhpRenderer(__DIR__ . '/../../templates', ['title' => 'Connexion']);
        return $view->render($response, '/auth/login.php');
    }

    public function renderResponse(Response $response, string $error, array $old_post = [], string $file = 'register'): Response
    {
        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title'    => 'Inscription',
            'error'    => $error ?? null,
            'old_post' => $old_post,
        ]);

        return $view->render($response, '/auth/' . $file . '.php');
    }

    public function showRegisterForm(Request $request, Response $response): Response
    {
        $view = new PhpRenderer(__DIR__ . '/../../templates', ['title' => 'Inscription']);
        Csrf::generate();
        return $view->render($response, '/auth/register.php');
    }
    public function showLoginForm(Request $request, Response $response): Response
    {
        $view = new PhpRenderer(__DIR__ . '/../../templates', ['title' => 'Connexion']);
        return $view->render($response, '/auth/login.php');
    }

    public function create(Request $request, Response $response)
    {
        $errors = [];

        $data = filter_input_array(INPUT_POST, [
            'nom' => FILTER_SANITIZE_SPECIAL_CHARS,
            'prenom' => FILTER_SANITIZE_SPECIAL_CHARS,
            'email' => FILTER_SANITIZE_EMAIL,
            'mot_de_passe' => FILTER_SANITIZE_SPECIAL_CHARS,
            'service' => FILTER_SANITIZE_SPECIAL_CHARS,
            'telephone' => FILTER_SANITIZE_SPECIAL_CHARS
        ]);

        $data['csrf_token'] = $_POST['csrf_token'];

        if (!Csrf::check($data['csrf_token'])) {
            $errors[] = 'Token invalide.';
        }

        if (
            !Validator::isNotEmpty($data['nom']) ||
            !Validator::isNotEmpty($data['prenom']) ||
            !Validator::isNotEmpty($data['mot_de_passe']) ||
            !Validator::isNotEmpty($data['email']) ||
            !Validator::isNotEmpty($data['service']) ||
            !Validator::isNotEmpty($data['telephone'])
        ) {
            $errors[] = 'Tous les champs sont obligatoires.';
        } 

        if (!Validator::isEmail($data['email'])) {
            $errors[] = 'Email invalide';
        }
         if (preg_match('/[0-9!@#$%^&*()-+]/', $data['nom'])) {
            $errors[] = 'Le nom ne doit pas contenir de chiffres ou de caractères spéciaux.';
        }

        if (preg_match('/[0-9!@#$%^&*()-+]/', $data['prenom'])) {
            $errors[] = 'Le prénom ne doit pas contenir de chiffres ou de caractères spéciaux.';
        }
        
        if(!Validator::minLength($data['telephone'], 10) || !Validator::maxLength($data['telephone'], 11)){

             $errors[] = 'Entrez un numéro de téléphone valide.';
        }


        if (!Validator::minLength($data['mot_de_passe'], 8)) {
            $errors[] = 'Min. 8 caractères pour le mot de passe.';
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W])[^\s]{8,}$/', $data['mot_de_passe'])) {
            $errors[] = "Le mot de passe doit contenir au moins 1 chiffre, 1 majuscule et 1 caractère spécial.";
        }

        if (!empty($errors)) {
            return $this->renderResponse($response, $errors[0], $_POST);
        }

        $soignant = new Soignant(
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['mot_de_passe'],
            $data['service'],
            $data['telephone']
        );


        if (!$soignant->isUnique()) {
            return $this->renderResponse($response, 'Cette adresse email est déjà utilisée.', $_POST);
        }

        $lastInsertId = $soignant->createSoignant();
        

        if ($lastInsertId) {
            return $response
                ->withHeader('Location', '/auth/login')
                ->withStatus(302);
        }

        return $this->renderResponse($response, 'Erreur lors de la création du compte', $_POST);
    }
}
