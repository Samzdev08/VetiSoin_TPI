<?php

/**
 * Fichier : CategorieController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Gestion des catégories par l'admin
 */

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Categorie;
use App\Models\Category;
use App\Outils\Csrf;
use App\Outils\Validator;

class CategoryController
{
    public function __invoke(Request $request, Response $response): Response
    {
        $categorieObj = new Category(null, null, null, null);
        $categories = $categorieObj->getCategroy();

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title'      => 'Catégories',
            'categories' => $categories,
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/categories/list.php');
    }

    public function renderResponse(Response $response, array $old_post = [], $id = null): Response
    {
        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title'     => $id ? 'Modifier catégorie' : 'Nouvelle catégorie',
            'id'        => $id,
            'categorie' => $old_post,
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/categories/form.php');
    }

    public function showCreateForm(Request $request, Response $response): Response
    {
        Csrf::generate();
        return $this->renderResponse($response);
    }

    public function createPost(Request $request, Response $response): Response
    {
        $data = filter_input_array(INPUT_POST, [
            'csrf_token'  => FILTER_SANITIZE_SPECIAL_CHARS,
            'nom'         => FILTER_SANITIZE_SPECIAL_CHARS,
            'description' => FILTER_SANITIZE_SPECIAL_CHARS,
            'type_taille' => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        if (!Csrf::check($data['csrf_token'] ?? '')) {
            $_SESSION['flash']['error'] = 'Jeton de sécurité invalide.';
            return $this->renderResponse($response, $_POST);
        }

        $errors = [];

        if (!Validator::isNotEmpty($data['nom']) || !Validator::maxLength($data['nom'], 81)) {
            $errors[] = 'Le nom est invalide (1 à 80 caractères).';
        }
        if (!in_array($data['type_taille'], ['habit', 'chaussure', 'unique'], true)) {
            $errors[] = 'Le type de taille est invalide.';
        }

        if (!empty($errors)) {
            $_SESSION['flash']['error'] = $errors[0];
            return $this->renderResponse($response, $_POST);
        }

        $categorieObj = new Category(
            null,
            trim($data['nom']),
            $data['description'] ?? null,
            $data['type_taille']
        );

        if (!$categorieObj->isUnique()) {
            $_SESSION['flash']['error'] = 'Une catégorie avec ce nom existe déjà.';
            return $this->renderResponse($response, $_POST);
        }

        $idCategorie = $categorieObj->create();

        if ($idCategorie) {
            $_SESSION['flash']['success'] = 'Catégorie créée avec succès.';
            return $response->withHeader('Location', '/admin/categories')->withStatus(302);
        }

        $_SESSION['flash']['error'] = 'Erreur lors de la création de la catégorie.';
        return $this->renderResponse($response, $_POST);
    }
    public function showEditForm(Request $request, Response $response, $args): Response
    {
        $categorieObj = new Category($args['id'], null, null, null);
        $categorie = $categorieObj->();

        if (!$categorie) {
            $_SESSION['flash']['error'] = 'Catégorie introuvable.';
            return $response->withHeader('Location', '/admin/categories')->withStatus(302);
        }

        Csrf::generate();
        return $this->renderResponse($response, $categorie, $args['id']);
    }
}
