<?php
/**
 * Fichier : UserController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Gestion des comptes soignants
 */

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Soignant;


class UserController
{
     public function __invoke(Request $request, Response $response): Response
    {


        $userObj = new Soignant(null, null, null, null, null, null);

        $soignants = $userObj->getAll();

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title' => 'Accueil',
            'soignants' => $soignants
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/users/list.php');
    }
}