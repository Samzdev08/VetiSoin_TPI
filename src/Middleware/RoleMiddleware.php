<?php
/**
 * Fichier : RoleMiddleware.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Verifie le role requis (soignant/admin)
 */

namespace App\Middleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class RoleMiddleware {

    public function __construct(private string $roleRequis) {}

    public function __invoke(Request $request, RequestHandler $handler) {

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash']['error'] = "Vous devez être connecté.";
            $response = new SlimResponse();
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $role = $_SESSION['user_role'] ?? '';
        $acces = match($this->roleRequis) {
            'administrateur' => $role === 'Administrateur',
            'soignant'       => in_array($role, ['Soignant', 'Administrateur']),
            default          => false,
        };
        if (!$acces) {
            $_SESSION['flash']['error'] = "Vous n'avez pas les droits pour accéder à cette page.";
            $response = new SlimResponse();
            return $response->withHeader('Location', '/catalogue')->withStatus(302);
        }
        return $handler->handle($request);
    }
}