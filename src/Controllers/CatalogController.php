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
use App\Models\Article;
use App\Models\ArticleVariant;
use App\Outils\Csrf;

class CatalogController
{
    public function __invoke(Request $request, Response $response): Response
    {
        $genre = $_GET['genre'] ?? null;
        $category = $_GET['categorie'] ?? null;
        $taille = $_GET['taille'] ?? null;
        $recherche = $_GET['recherche'] ?? null;
        $couleur = $_GET['couleur'] ?? null;

        $articles = new Article(null, $category, $recherche, $genre, null, null, $taille, $couleur);
        $articleVariants = new ArticleVariant(null, null, null, null, null, null);
        
        $articlesItem = $articles->getAll();
        $couleurs = $articleVariants->getCouleurs();

        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title' => 'Accueil',
            'articles' => $articlesItem,
            'couleurs' => $couleurs
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/catalog/list.php');
    }

    public function detail(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $article = new Article($id, null, null, null, null, null, null, null);
        $articleData = $article->getById();

        if (!$articleData) {
            $response->getBody()->write('Article non trouvé');
            return $response->withStatus(404);
        }

       
        $selectedColor = $articleData['variantes'][0]['couleur'] ?? null;

        Csrf::generate();

        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title' => 'Détails de l\'article',
            'article' => $articleData,
            'selectedColor' => $selectedColor
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/catalog/detail.php');
    }

    public function setColor(Request $request, Response $response, array $args): Response
    {
        $color = $args['color'];
        $id = $args['id'];

        $csrf_token = $_POST['csrf_token'] ?? null;
        if (!Csrf::check($csrf_token)) {
            $_SESSION['flash']['error'] = 'Token de sécurité invalide, veuillez réessayer.';
            return $response->withHeader('Location', '/catalogue/' . $id)->withStatus(302);
        }

        $article = new Article($id, null, null, null, null, null, null, null);
        $articleData = $article->getById();

        if (!$articleData) {
            $response->getBody()->write('Article non trouvé');
            return $response->withStatus(404);
        }

        Csrf::generate();

        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title' => 'Détails de l\'article',
            'article' => $articleData,
            'selectedColor' => $color
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/catalog/detail.php');
    }
}