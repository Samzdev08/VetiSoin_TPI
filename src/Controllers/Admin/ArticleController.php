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
use App\Outils\Validator;
use App\Outils\Csrf;

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

        Csrf::generate();


        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title'      => 'Modifier article',
            'article'    => $article,
            'categories' => $categories,
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/articles/form.php');
    }

    public function editPost(Request $request, Response $response, $args): Response
    {
        $idArticle = $args['id'];

        $data = filter_input_array(INPUT_POST, [
            'csrf_token'   => FILTER_SANITIZE_SPECIAL_CHARS,
            'nom'          => FILTER_SANITIZE_SPECIAL_CHARS,
            'id_categorie' => FILTER_VALIDATE_INT,
            'genre'        => FILTER_SANITIZE_SPECIAL_CHARS,
            'marque'       => FILTER_SANITIZE_SPECIAL_CHARS,
            'matiere'      => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        if (!Csrf::check($data['csrf_token'] ?? '')) {
            $_SESSION['flash']['error'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
            return $response->withHeader('Location', '/admin/articles/' . $idArticle . '/edit')->withStatus(302);
        }

        $errors = [];

        if (!Validator::isNotEmpty($data['nom']) || !Validator::minLength($data['nom'], 1) || !Validator::maxLength($data['nom'], 101)) {
            $errors[] = 'Le nom est invalide (2 à 100 caractères).';
        }
        if (!Validator::isNotEmpty($data['marque']) || !Validator::minLength($data['marque'], 1) || !Validator::maxLength($data['marque'], 51)) {
            $errors[] = 'La marque est invalide (2 à 50 caractères).';
        }
        if (!Validator::isNotEmpty($data['matiere']) || !Validator::minLength($data['matiere'], 1) || !Validator::maxLength($data['matiere'], 51)) {
            $errors[] = 'La matière est invalide (2 à 50 caractères).';
        }
        if (!in_array($data['genre'], ['Femme', 'Homme', 'Mixte'], true)) {
            $errors[] = 'Le genre est invalide.';
        }
        if (!Validator::isNumeric($data['id_categorie']) || $data['id_categorie'] < 1) {
            $errors[] = 'La catégorie est invalide.';
        }

        if (!empty($errors)) {
            $_SESSION['flash']['error'] = implode('<br>', $errors);
            return $response->withHeader('Location', '/admin/articles/' . $idArticle . '/edit')->withStatus(302);
        }

        $articleObj = new Article(
            $idArticle,
            $data['id_categorie'],
            $data['nom'],
            $data['genre'],
            $data['matiere'],
            $data['marque'],
            null
        );
        $success = $articleObj->update();

        if ($success) {
            $_SESSION['flash']['success'] = 'Article mis à jour avec succès.';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de la mise à jour de l\'article.';
        }

        return $response->withHeader('Location', '/admin/articles/' . $idArticle . '/edit')->withStatus(302);
    }

    public function editVariante(Request $request, Response $response, $args): Response
    {
        $idArticleVariante = $args['id'];

        $data = filter_input_array(INPUT_POST, [
            'csrf_token' => FILTER_SANITIZE_SPECIAL_CHARS,
            'stock'      => FILTER_VALIDATE_INT,
        ]);

        $idArticle = (new ArticleVariant($idArticleVariante, null, null, null, null, null))->getArticleId();

        if (!Csrf::check($data['csrf_token'] ?? '')) {
            $_SESSION['flash']['error'] = 'Jeton de sécurité invalide. Veuillez réessayer.';
            return $response->withHeader('Location', '/admin/articles/' . $idArticle . '/edit')->withStatus(302);
        }

        $stock = $data['stock'] ?? 0;
        $photo = null;

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {

            $result = FileManager::checkMedia($_FILES['photo']);

            if (!$result['success']) {

                $_SESSION['flash']['error'] = $result['message'];
                return $response->withHeader('Location', '/admin/articles/' . $idArticle . '/edit')->withStatus(302);

            }
            $photo = $result['filename'];
        }

        $articleVariantObj = new ArticleVariant($idArticleVariante, null, null, null, $photo, $stock);
        $success = $articleVariantObj->updateArticleVariante();

        if ($success) {

            $_SESSION['flash']['success'] = 'Variante mise à jour avec succès.';
        } else {
            
            $_SESSION['flash']['error'] = 'Erreur lors de la mise à jour de la variante.';
        }

        return $response->withHeader('Location', '/admin/articles/' . $idArticle . '/edit')->withStatus(302);
    }
}
