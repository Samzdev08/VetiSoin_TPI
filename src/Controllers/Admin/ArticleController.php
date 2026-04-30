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
use App\Models\Category;
use App\Models\ArticleVariant;
use App\Outils\FileManager;

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

    public function edit(Request $request, Response $response, $args): Response
    {
        $articleObj = new Article($args['id'], null, null, null, null, null, null);
        $article = $articleObj->getById();

        $categorieObj = new Category(null, null, null);

        $categories = $categorieObj->getCategroy();

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title'      => 'Modifier article',
            'article'    => $article,
            'categories' => $categories,
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/articles/form.php');
    }

    

    public function editVariante(Request $request, Response $response, $args): Response
    {
        $idArticleVariante = $args['id'];

        $data = filter_input_array(INPUT_POST, [
            'stock' => FILTER_VALIDATE_INT,
        ]);

        $stock = $data['stock'] ?? 0;

        


        $photo = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {

            $result = FileManager::checkMedia($_FILES['photo']);

            if (!$result['success']) {

                $_SESSION['flash']['error'] = $result['message'];

                $idArticle = (new ArticleVariant($idArticleVariante, null, null, null, null, null))->getArticleId();

                return $response->withHeader('Location', '/admin/articles/' . $idArticle . '/edit')->withStatus(302);

            }
            $photo = $result['filename'];
        }

        $articleVariantObj = new ArticleVariant($idArticleVariante, null,null, null, $photo, $stock);
        $success = $articleVariantObj->updateArticleVariante();

        if ($success) {
            $_SESSION['flash']['success'] = 'Variante mise à jour avec succès.';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de la mise à jour de la variante.';
        }

        $idArticle = $articleVariantObj->getArticleId();
        return $response->withHeader('Location', '/admin/articles/' . $idArticle . '/edit')->withStatus(302);
    }
}
