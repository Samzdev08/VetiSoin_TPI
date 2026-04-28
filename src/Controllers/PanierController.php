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

class PanierController
{
    public function __invoke(Request $request, Response $response): Response
    {
        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title' => 'Panier',
            'paniers' => $_SESSION['cart'] ?? [],
        ]);

        return $view->render($response, '/reservations/cart.php');
    }

    public function addToCart(Request $request, Response $response): Response
    {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        $data = $request->getParsedBody();


        if (!isset($data['variante_id'], $data['quantite'], $data['taille'], $data['nom'], $data['couleur'], $data['photo'], $data['marque'])) {
            $_SESSION['flash']['error'] = 'Données manquantes pour ajouter l\'article au panier.';
            return $response->withHeader('Location', '/catalogue/' . $data['article_id'])->withStatus(302);
        }



        if ($data['quantite'] <= 0) {

            $_SESSION['flash']['error'] = 'La quantité doit être supérieure à zéro.';
            return $response->withHeader('Location', '/catalogue/' . $data['article_id'])->withStatus(302);
        }

        if ($data['taille'] === null) {
            $_SESSION['flash']['error'] = 'Veuillez sélectionner une taille.';
            return $response->withHeader('Location', '/catalogue/' . $data['article_id'])->withStatus(302);
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
            'marque' => $data['marque']
        ];

        $_SESSION['flash']['success'] = 'Article ajouté au panier avec succès.';
        return $response->withHeader('Location', '/panier')->withStatus(302);
    }
}
