<?php

/**
 * Fichier : PanierController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Gestion du panier d'achats
 */

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\ArticleVariant;
use App\Outils\Csrf;
use App\Outils\Validator;

class PanierController
{
    public function __invoke(Request $request, Response $response): Response
    {
        $cart = $_SESSION['cart'] ?? [];
        $errors = [];

        foreach ($cart as $i => &$item) {
            $articleModel = new ArticleVariant($item['variante_id'], null, null, null, null, null);
            $stockReel = $articleModel->getStockById();

            if ($stockReel == 0) {
                $errors[] = 'L\'article "' . $item['nom'] . '" est en rupture de stock et a été retiré du panier.';
                unset($cart[$i]);
                continue;
            }

            if ($item['quantite'] > $stockReel) {
                $errors[] = 'La quantité de l\'article "' . $item['nom'] . '" a été ajustée (stock actuel : ' . $stockReel . ').';
                $item['quantite'] = $stockReel;
            }


            $item['maxStock'] = $stockReel;
        }


        $_SESSION['cart'] = array_values($cart);

        if (!empty($errors)) {
            $_SESSION['flash']['error'] = $errors;
        }

        Csrf::generate();

        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title'   => 'Panier',
            'paniers' => $_SESSION['cart'],
        ]);
        $view->setLayout('layout.php');

        return $view->render($response, '/reservations/cart.php');
    }

    public function addToCart(Request $request, Response $response): Response
    {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        $data = $request->getParsedBody();

        $csrf_token = $_POST['csrf_token'] ?? null;
        if (!Csrf::check($csrf_token)) {
            $_SESSION['flash']['error'] = 'Token de sécurité invalide, veuillez réessayer.';
            return $response->withHeader('Location', '/catalogue/' . ($data['article_id'] ?? ''))->withStatus(302);
        }

        if (
            !Validator::isNotEmpty($data['variante_id'] ?? null) ||
            !Validator::isNotEmpty($data['quantite'] ?? null) ||
            !Validator::isNotEmpty($data['taille'] ?? null) ||
            !Validator::isNotEmpty($data['nom'] ?? null) ||
            !Validator::isNotEmpty($data['couleur'] ?? null) ||
            !Validator::isNotEmpty($data['photo'] ?? null) ||
            !Validator::isNotEmpty($data['marque'] ?? null)
        ) {
            $_SESSION['flash']['error'] = 'Données manquantes pour ajouter l\'article au panier.';
            return $response->withHeader('Location', '/catalogue/' . ($data['article_id'] ?? ''))->withStatus(302);
        }

        if ($data['quantite'] <= 0) {

            $_SESSION['flash']['error'] = 'La quantité doit être supérieure à zéro.';
            return $response->withHeader('Location', '/catalogue/' . $data['article_id'])->withStatus(302);
        }

        if (!Validator::isNotEmpty($data['taille'] ?? null)) {
            $_SESSION['flash']['error'] = 'Veuillez sélectionner une taille.';
            return $response->withHeader('Location', '/catalogue/' . ($data['article_id'] ?? ''))->withStatus(302);
        }

        if ($data['quantite'] > $data['maxStock']) {
            $_SESSION['flash']['error'] = 'La quantité demandée dépasse le stock disponible.';
            return $response->withHeader('Location', '/catalogue/' . $data['article_id'])->withStatus(302);
        }

        $_SESSION['cart'][] = [
            'variante_id' => $data['variante_id'],
            'quantite' => $data['quantite'],
            'taille' => $data['taille'],
            'nom' => $data['nom'],
            'couleur' => $data['couleur'],
            'photo' => $data['photo'],
            'marque' => $data['marque'],
            'maxStock' => $data['maxStock']
        ];


        $combinaisons = [];
        foreach ($_SESSION['cart'] as $i => $v) {

            $cle =  strtolower(trim($v['taille'] ?? '')) . '|' . strtolower(trim($v['couleur'] ?? '')) . '|' . ($v['variante_id'] ?? '');

            if (in_array($cle, $combinaisons, true)) {

                $_SESSION['flash']['error'] = "Article deja présent dans le panier";
                
                unset($_SESSION['cart'][$i]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);

                return $response->withHeader('Location', '/catalogue/' . $data['article_id'])->withStatus(302);
            }
            $combinaisons[] = $cle;
        }





        $_SESSION['flash']['success'] = 'Article ajouté au panier avec succès.';
        return $response->withHeader('Location', '/panier')->withStatus(302);
    }

    public function removeFromCart(Request $request, Response $response, array $args): Response
    {
        $csrf_token = $_POST['csrf_token'] ?? null;
        if (!Csrf::check($csrf_token)) {
            $_SESSION['flash']['error'] = 'Token de sécurité invalide, veuillez réessayer.';
            return $response->withHeader('Location', '/panier')->withStatus(302);
        }

        $index = $args['id'];

        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            $_SESSION['flash']['success'] = 'Article retiré du panier avec succès.';
        } else {
            $_SESSION['flash']['error'] = 'Article non trouvé dans le panier.';
        }

        return $response->withHeader('Location', '/panier')->withStatus(302);
    }

    public function updateCart(Request $request, Response $response, array $args): Response
    {
        $csrf_token = $_POST['csrf_token'] ?? null;
        if (!Csrf::check($csrf_token)) {
            $_SESSION['flash']['error'] = 'Token de sécurité invalide, veuillez réessayer.';
            return $response->withHeader('Location', '/panier')->withStatus(302);
        }

        $id = $args['id'];

        $data = $request->getParsedBody();

        if ($data['quantite'] > (int)$data['maxStock']) {

            $_SESSION['flash']['error'] = 'La quantité demandée dépasse le stock disponible.';
            return $response->withHeader('Location', '/panier')->withStatus(302);
        }




        $_SESSION['cart'][$id]['quantite'] = $data['quantite'];


        $_SESSION['flash']['success'] = 'Quantité mise à jour avec succès.';
        return $response->withHeader('Location', '/panier')->withStatus(302);
    }


    public function clearCart(Request $request, Response $response, array $args): Response
    {
        $csrf_token = $_POST['csrf_token'] ?? null;
        if (!Csrf::check($csrf_token)) {
            $_SESSION['flash']['error'] = 'Token de sécurité invalide, veuillez réessayer.';
            return $response->withHeader('Location', '/panier')->withStatus(302);
        }

        $_SESSION['cart'] = [];
        $_SESSION['flash']['success'] = 'Panier vidé avec succès.';
        return $response->withHeader('Location', '/catalogue')->withStatus(302);
    }
}
