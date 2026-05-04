<?php
$flash = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);
/** @var string|null $title */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../assets/css/login.css" rel="stylesheet">
</head>
<body>
    <div class="container" style="max-width: 420px;">

        <?php if (!empty($flash['success'])) : ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3 rounded-3">
                <i class="bi bi-check-circle-fill"></i>
                <?= htmlspecialchars($flash['success']) ?>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($flash['error'])) : ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3 rounded-3">
                <i class="bi bi-exclamation-circle-fill"></i>
                <?= htmlspecialchars($flash['error']) ?>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0">
            <div class="card-header text-center">
                <i class="bi bi-heart-pulse-fill text-white fs-1"></i>
                <h1 class="text-white fw-bold mt-2 mb-1" style="font-size: 22px;">VetiSoin</h1>
                <p class="mb-0" style="color: rgba(255,255,255,0.7); font-size: 13px;">Connexion à votre espace soignant</p>
            </div>

            <div class="card-body bg-white p-4" style="border-radius: 0 0 12px 12px;">
                <form action="/auth/login" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" id="email" name="email" class="form-control"
                                placeholder="prenom.nom@hopital.ch"
                                value="<?= htmlspecialchars($old_post['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="mot_de_passe" class="form-label">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" id="mot_de_passe" name="mot_de_passe"
                                class="form-control" placeholder="••••••••">
                            <button class="btn btn-outline-secondary" type="button" id="togglePwd"
                                style="border-color: #E2E5EA;">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-login">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Se connecter
                        </button>
                    </div>
                </form>

                <p class="text-center lien-register mb-0">
                    Pas encore de compte ? <a href="/auth/register">S'inscrire</a>
                </p>
            </div>
        </div>

        <p class="text-center mt-3" style="font-size: 12px; color: #9aa0af;">
            &copy; <?= date('Y') ?> VetiSoin
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePwd').addEventListener('click', function() {
            var input = document.getElementById('mot_de_passe');
            var icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    </script>
</body>
</html>