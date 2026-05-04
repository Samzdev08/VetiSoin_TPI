<?php

/**
 * Fichier : list.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Liste des notifications du soignant connecté
 */
/** @var array $notifications */


$badges = [
    'Réservation confirmée' => 'success',
    'Rappel rendez-vous'    => 'primary',
    'Retour attendu'        => 'warning',
    'Stock bas'             => 'danger',
];

$nbNonLues = 0;
foreach ($notifications as $n) {

    if (!$n['lu']) $nbNonLues++;
}
?>

<div class="container mt-4" style="max-width: 780px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">
            Mes notifications
            <?php if ($nbNonLues > 0) : ?>
                <span class="badge bg-danger ms-2"><?= $nbNonLues ?> non lue<?= $nbNonLues > 1 ? 's' : '' ?></span>
            <?php endif; ?>
        </h1>

        <?php if ($nbNonLues > 0) : ?>
            <a href="/notifications/lire-tout" class="btn btn-sm btn-outline-secondary">
                Tout marquer comme lu
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($notifications)) : ?>
        <div class="alert alert-info">Vous n'avez aucune notification.</div>
    <?php else : ?>
        <div class="list-group">
            <?php foreach ($notifications as $n) :
                $couleur = $badges[$n['type']] ?? 'secondary';
                $isLue  = $n['lu'];
            ?>
                <div class="list-group-item <?= $isLue ? '' : 'list-group-item-light fw-semibold' ?>">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-<?= $couleur ?>"><?= htmlspecialchars($n['type']) ?></span>
                                <?php if (!$isLue) : ?>
                                    <span class="badge bg-primary">Nouveau</span>
                                <?php endif; ?>
                            </div>

                            <h6 class="mb-1"><?= htmlspecialchars($n['titre']) ?></h6>
                            <p class="mb-1 small <?= $isLue ? 'text-muted' : '' ?>">
                                <?= nl2br(htmlspecialchars($n['message'])) ?>
                            </p>
                            <small class="text-muted">
                                <?= htmlspecialchars(date('d.m.Y à H:i', strtotime($n['date_envoi']))) ?>
                            </small>
                        </div>

                        <?php if (!$isLue) : ?>
                            <a href="/notifications/<?= $n['id'] ?>/lire"
                                class="btn btn-sm btn-outline-primary ms-2"
                                title="Marquer comme lue">
                                ✓
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>