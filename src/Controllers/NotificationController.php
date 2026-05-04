<?php

/**
 * Fichier : NotificationController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Consultation et gestion des notifications du soignant connecté
 */

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Notification;

class NotificationController
{
    public function __construct() {}

    
    public function __invoke(Request $request, Response $response): Response
    {
        $idDestinataire = $_SESSION['user_id'] ?? null;

        if (!$idDestinataire) {
            $_SESSION['flash']['error'] = 'Vous devez être connecté.';
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }

        $notifObj = new Notification(null, $idDestinataire, null, null, null);
        $notifications = $notifObj->getByDestinataire();

        $view = new PhpRenderer(__DIR__ . '/../../templates', [
            'title'         => 'Mes notifications',
            'notifications' => $notifications,
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/notifications/list.php');
    }

    
    public function markRead(Request $request, Response $response, $args): Response
    {
        $idNotif = $args['id'] ?? null;
        $idDestinataire = $_SESSION['user_id'] ?? null;

        if (!$idDestinataire) {
            $_SESSION['flash']['error'] = 'Vous devez être connecté.';
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }

        if (!$idNotif) {
            $_SESSION['flash']['error'] = 'ID de notification manquant.';
            return $response->withHeader('Location', '/notifications')->withStatus(302);
        }

        $notifObj = new Notification($idNotif, $idDestinataire, null, null, null);
        $notifObj->markAsRead();

        return $response->withHeader('Location', '/notifications')->withStatus(302);
    }

   
    public function markAllRead(Request $request, Response $response): Response
    {
        $idDestinataire = $_SESSION['user_id'] ?? null;

        if (!$idDestinataire) {
            $_SESSION['flash']['error'] = 'Vous devez être connecté.';
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }

        $notifObj = new Notification(null, $idDestinataire, null, null, null);
        $notifObj->markAllAsRead();

        $_SESSION['flash']['success'] = 'Toutes les notifications ont été marquées comme lues.';
        return $response->withHeader('Location', '/notifications')->withStatus(302);
    }
}