<?php

/**
 * Fichier : UserController.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Gestion des comptes soignants
 */

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Soignant;
use App\Outils\Csrf;
use App\Outils\Validator;
use App\Models\Reservation;


class UserController
{

    public function renderResponse(Response $response, array $old_post = [], $id = null): Response
    {
        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title'    => $id ? 'Modifier soignant' : 'Nouveau soignant',
            'id'       => $id,
            'soignant' => $old_post,
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/users/form.php');
    }

    public function __invoke(Request $request, Response $response): Response
    {


        $userObj = new Soignant(null, null, null, null, null, null, null);

        $soignants = $userObj->getAll();

        Csrf::generate();

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title' => 'Accueil',
            'soignants' => $soignants
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/users/list.php');
    }

    public function showCreateForm(Request $request, Response $response): Response
    {
        Csrf::generate();

        $view = new PhpRenderer(__DIR__ . '/../../../templates', [
            'title'    => 'Nouveau soignant',
            'id'       => null,
            'soignant' => [],
        ]);
        $view->setLayout('layout.php');
        return $view->render($response, '/admin/users/form.php');
    }

    public function showEditForm(Request $request, Response $response, $args): Response
    {
        $soignantObj = new Soignant($args['id'], null, null, null, null, null, null);
        $soignant = $soignantObj->getById();

        if (!$soignant) {
            $_SESSION['flash']['error'] = 'Soignant introuvable.';
            return $response->withHeader('Location', '/admin/soignants')->withStatus(302);
        }

        Csrf::generate();

        return $this->renderResponse($response, $soignant, $args['id']);
    }

    public function createPost(Request $request, Response $response): Response
    {
        $data = filter_input_array(INPUT_POST, [
            'csrf_token' => FILTER_SANITIZE_SPECIAL_CHARS,
            'nom' => FILTER_SANITIZE_SPECIAL_CHARS,
            'prenom' => FILTER_SANITIZE_SPECIAL_CHARS,
            'email' => FILTER_SANITIZE_EMAIL,
            'mot_de_passe' => FILTER_UNSAFE_RAW,
            'service' => FILTER_SANITIZE_SPECIAL_CHARS,
            'role' => FILTER_SANITIZE_SPECIAL_CHARS,
            'telephone' => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        if (!Csrf::check($data['csrf_token'] ?? '')) {
            $_SESSION['flash']['error'] = 'Jeton de sécurité invalide.';
            return $this->renderResponse($response, $_POST);
        }

        $errors = [];

        if (
            !Validator::isNotEmpty($data['nom']) ||
            !Validator::isNotEmpty($data['prenom']) ||
            !Validator::isNotEmpty($data['mot_de_passe']) ||
            !Validator::isNotEmpty($data['email']) ||
            !Validator::isNotEmpty($data['service']) ||
            !Validator::isNotEmpty($data['role']) ||
            !Validator::isNotEmpty($data['telephone'])
        ) {
            $errors[] = 'Tous les champs sont obligatoires.';
        }
        if (!Validator::isEmail($data['email'])) {
            $errors[] = 'Email invalide.';
        }
        if (preg_match('/[0-9!@#$%^&*()\-+]/', $data['nom'])) {
            $errors[] = 'Le nom ne doit pas contenir de chiffres ou de caractères spéciaux.';
        }
        if (preg_match('/[0-9!@#$%^&*()\-+]/', $data['prenom'])) {
            $errors[] = 'Le prénom ne doit pas contenir de chiffres ou de caractères spéciaux.';
        }
        if (!preg_match('/^[0-9]{10,11}$/', $data['telephone'])) {
            $errors[] = 'Entrez un numéro de téléphone valide (10 ou 11 chiffres).';
        }
        if (!Validator::minLength($data['mot_de_passe'], 8)) {
            $errors[] = 'Min. 8 caractères pour le mot de passe.';
        }
        if (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W])[^\s]{8,}$/', $data['mot_de_passe'])) {
            $errors[] = "Le mot de passe doit contenir au moins 1 chiffre, 1 majuscule et 1 caractère spécial.";
        }
        if (!in_array($data['service'], ['Urgences', 'Chirurgie', 'Médecine interne'], true)) {
            $errors[] = 'Le service est invalide.';
        }
        if (!in_array($data['role'], ['Administrateur', 'Soignant'], true)) {
            $errors[] = 'Le role est invalide.';
        }

        if (!empty($errors)) {
            $_SESSION['flash']['error'] = $errors[0];
            return $this->renderResponse($response, $_POST);
        }

        $soignant = new Soignant(
            null,
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['mot_de_passe'],
            $data['service'],
            $data['telephone']
        );

        if (!$soignant->isUnique()) {
            $_SESSION['flash']['error'] = 'Cette adresse email est déjà utilisée.';
            return $this->renderResponse($response, $_POST);
        }

        $lastInsertId = $soignant->createSoignant($data['role']);

        if ($lastInsertId) {
            $_SESSION['flash']['success'] = 'Soignant créé avec succès.';
            return $response->withHeader('Location', '/admin/soignants')->withStatus(302);
        }

        $_SESSION['flash']['error'] = 'Erreur lors de la création du compte.';
        return $this->renderResponse($response, $_POST);
    }

    public function editPost(Request $request, Response $response, $args): Response
    {
        $idUser = $args['id'];

        $data = filter_input_array(INPUT_POST, [
            'csrf_token' => FILTER_SANITIZE_SPECIAL_CHARS,
            'nom' => FILTER_SANITIZE_SPECIAL_CHARS,
            'prenom' => FILTER_SANITIZE_SPECIAL_CHARS,
            'email' => FILTER_SANITIZE_EMAIL,
            'service'  => FILTER_SANITIZE_SPECIAL_CHARS,
            'role' => FILTER_SANITIZE_SPECIAL_CHARS,
            'telephone'  => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        if (!Csrf::check($data['csrf_token'] ?? '')) {
            $_SESSION['flash']['error'] = 'Jeton de sécurité invalide.';
            return $this->renderResponse($response, $_POST, $idUser);
        }

        $errors = [];

        if (
            !Validator::isNotEmpty($data['nom']) ||
            !Validator::isNotEmpty($data['prenom']) ||
            !Validator::isNotEmpty($data['email']) ||
            !Validator::isNotEmpty($data['service']) ||
            !Validator::isNotEmpty($data['telephone'])
        ) {
            $errors[] = 'Tous les champs sont obligatoires.';
        }
        if (!Validator::isEmail($data['email'])) {
            $errors[] = 'Email invalide.';
        }
        if (preg_match('/[0-9!@#$%^&*()\-+]/', $data['nom'])) {
            $errors[] = 'Le nom ne doit pas contenir de chiffres ou de caractères spéciaux.';
        }
        if (preg_match('/[0-9!@#$%^&*()\-+]/', $data['prenom'])) {
            $errors[] = 'Le prénom ne doit pas contenir de chiffres ou de caractères spéciaux.';
        }
        if (!preg_match('/^[0-9]{10,11}$/', $data['telephone'])) {
            $errors[] = 'Entrez un numéro de téléphone valide (10 ou 11 chiffres).';
        }
        if (!in_array($data['service'], ['Urgences', 'Chirurgie', 'Médecine interne'], true)) {
            $errors[] = 'Le service est invalide.';
        }

        if (!in_array($data['role'], ['Administrateur', 'Soignant'], true)) {
            $errors[] = 'Le role est invalide.';
        }

        

        if (!empty($errors)) {
            $_SESSION['flash']['error'] = $errors[0];
            return $this->renderResponse($response, $_POST, $idUser);
        }

        $soignant = new Soignant(
            $idUser,
            $data['nom'],
            $data['prenom'],
            $data['email'],
            null,
            $data['service'],
            $data['telephone']
        );

        $success = $soignant->update($data['role']);

        if ($success) {
            $_SESSION['flash']['success'] = 'Soignant mis à jour avec succès.';
            return $response->withHeader('Location', '/admin/soignants')->withStatus(302);
        }

        $_SESSION['flash']['error'] = 'Erreur lors de la mise à jour du soignant.';
        return $this->renderResponse($response, $_POST, $idUser);
    }

    public function resetPassword(Request $request, Response $response, $args): Response
    {
        $csrf_token = $_POST['csrf_token'] ?? null;
        if (!Csrf::check($csrf_token)) {
            $_SESSION['flash']['error'] = 'Token de sécurité invalide.';
            return $response->withHeader('Location', '/admin/soignants')->withStatus(302);
        }

        $idUser = $args['id'];


        $userObj = new Soignant($idUser, null, null, null, null, null, null);
        $soignant = $userObj->getById();

        if (!$soignant) {
            $_SESSION['flash']['error'] = 'Soignant introuvable.';
            return $response->withHeader('Location', '/admin/soignants')->withStatus(302);
        }

        if ($soignant['role'] === 'Administrateur') {
            $_SESSION['flash']['error'] = 'Impossible de réinitialiser le mot de passe d\'un administrateur.';
            return $response->withHeader('Location', '/admin/soignants')->withStatus(302);
        }


        $temp = bin2hex(random_bytes(8));
        $hash = password_hash($temp, PASSWORD_ARGON2ID);

        $userObj = new Soignant($idUser, null, null, null, $hash, null, null);
        $success = $userObj->resetPassword();

        if ($success) {
            $_SESSION['flash']['success'] = 'Mot de passe réinitialisé. Nouveau mot de passe temporaire : <strong>' . htmlspecialchars($temp) . '</strong> (à transmettre au soignant).';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de la réinitialisation du mot de passe.';
        }

        return $response->withHeader('Location', '/admin/soignants')->withStatus(302);
    }


    public function toggleStatut(Request $request, Response $response, $args): Response
    {
        $csrf_token = $_POST['csrf_token'] ?? null;
        if (!Csrf::check($csrf_token)) {
            $_SESSION['flash']['error'] = 'Token de sécurité invalide.';
            return $response->withHeader('Location', '/admin/soignants')->withStatus(302);
        }

        $idUser = $args['id'];
        $soignantObj = new Soignant($idUser, null, null, null, null, null, null);
        $reservationObj = new Reservation(null, $idUser, null, null, null, null);
        $isActive = $soignantObj->isActive();

        if ($isActive) {
            $success = $soignantObj->desactiver();
            $reservationObj->desactiver();
        } else {
            $success = $soignantObj->activer();
            $reservationObj->activer();
        }

        if ($success) {
            $_SESSION['flash']['success'] = $isActive ? 'Soignant désactivé.' : 'Soignant réactivé.';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors du changement de statut.';
        }

        return $response->withHeader('Location', '/admin/soignants')->withStatus(302);
    }
}
