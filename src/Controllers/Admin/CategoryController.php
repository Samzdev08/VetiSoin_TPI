<?php

/**
 * Fichier : CategoryController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Gestion des categories par l'admin
 */

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Soignant;
use App\Models\Category;
use App\Outils\Csrf;
use App\Outils\Validator;


class CategoryController
{

    public function __invoke(Request $request, Response $response): Response
    {


        $userObj = new Category(null, null, null, null);

        $categories = $userObj->getCategroy();

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title' => 'Mes catégories',
            'categories' => $categories
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/categories/list.php');
    }
}
