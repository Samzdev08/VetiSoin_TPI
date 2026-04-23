<?php

/**
 * Fichier : CatalogController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Consultation du catalogue d'articles
 */

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Outils\Csrf;
use App\Outils\Validator;
use App\Models\Article;

class CatalogController
{
    

    public function __invoke(Request $request, Response $response): Response
    {
        $genre = $_GET['genre'] ?? null;
        $category = $_GET['categorie'] ?? null;
        $taille = $_GET['taille'] ?? null;
        $recherche = $_GET['recherche'] ?? null;

        $articles = new Article($category, $recherche, $genre, null, null, $taille);

        $articlesItem = $articles->getAll();
        
        $view = new PhpRenderer(__DIR__ . '/../../templates', 
        [
            'title' => 'Accueil',
            'articles' => $articlesItem
        
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/catalog/list.php');
    }
}
