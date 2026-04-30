<?php

/**
 * Fichier : ArticleController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Gestion des articles par l'admin
 */

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Article;

class ArticleController
{


    public function __invoke(Request $request, Response $response): Response
    {

        $genre = $_GET['genre'] ?? null;


        $articesObj = new Article(null, null, null, $genre, null, null, null);

        $articles = $articesObj->getAll();

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title' => 'Accueil',
            'articles' => $articles
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/articles/list.php');
    }

    public function showDetails(Request $request, Response $response, $args): Response
    {

        

        $idArticle = $args['id'];

        $articesObj = new Article($idArticle, null, null, null, null, null, null);

        $article = $articesObj->getById();

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title' => 'Page détails',
            'article' => $article
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/articles/details.php');
    }
}
