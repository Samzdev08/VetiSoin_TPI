<?php

/**
 * Fichier : GuestMiddleware.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Verifie si le soignant est deja connecte
 */

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class GuestMiddleware {
    public function __invoke(Request $request, RequestHandler $handler) {

        if (isset($_SESSION['user_id'])) {
            $_SESSION['flash']['error'] = "Vous êtes déjà connecté.";
            $response = new SlimResponse();
            return $response->withHeader('Location', '/catalogue')->withStatus(302);
        }
        return $handler->handle($request);
    }
}
