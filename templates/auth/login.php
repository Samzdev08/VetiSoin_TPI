<?php

/**
 * Fichier : login.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Page de connexion
 */
/** @var string $title */

$flash = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
</head>

<body>

    <h1>Se connecter un compte</h1>

    <div class="container mt-3">

        <?php if (!empty($flash['success'])) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✓ <?= $flash['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($flash['error'])) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ✕ <?= $flash['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

    </div>

    <form action="/auth/login/post" method="POST">

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($old_post['email'] ?? '') ?>">

        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe">

        <button type="submit">Se connecter</button>

    </form>

    <a href="/auth/register">S'inscrire</a>

</body>

</html>