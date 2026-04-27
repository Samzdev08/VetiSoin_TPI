<?php

/**
 * Fichier : detail.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Fiche detaillee d'un article
 */
$arrayColor = [
    'Rouge'        => '#dc3545',
    'Bleu'         => '#007bff',
    'Bleu ciel'    => '#87CEEB',
    'Bleu marine'  => '#394972',
    'Vert'         => '#28a745',
    'Vert hopital' => '#4a8b6f',
    'Jaune'        => '#ffc107',
    'Noir'         => '#343a40',
    'Blanc'        => '#f8f9fa',
    'Rose'         => '#f4a6c0',
    'Violet'       => '#6f42c1',
    'Motif'        => '#cccccc',
];
$couleursUniques = [];


$variantesFiltrees = array_values(array_filter(
    $article['variantes'],
    fn($v) => $v['couleur'] === ($selectedColor ?? null)
));


$photoActive = $variantesFiltrees[0]['photo'] ?? ($article['variantes'][0]['photo']);
?>
<style>
    .color-option {
        width: 32px;
        height: 32px;
        border: 2px solid transparent;
        cursor: pointer;
        transition: transform .15s, border-color .15s;
    }

    .color-option:hover {
        transform: scale(1.1);
        border-color: #1a1a1a;
    }

    .color-option.active {
        border-color: #1a1a1a;
    }

    form.color-form {
        display: inline;
        margin: 0;
    }
</style>
<div class="container my-5">
    <div class="card shadow-sm mb-4">
        <div class="row g-0">
            <div class="col-md-5">
                <img src="<?= htmlspecialchars($photoActive) ?>"
                    alt="<?= htmlspecialchars($article['nom'] ?? '') ?>"
                    class="img-fluid rounded-start w-100"
                    style="height: 100%; object-fit: cover; background:#f5f5f5;">
            </div>
            <div class="col-md-7">
                <div class="card-body">
                    <small class="text-muted text-uppercase"><?= htmlspecialchars($article['marque'] ?? '') ?></small>
                    <h2 class="mb-3"><?= htmlspecialchars($article['nom'] ?? '') ?></h2>

                    <p class="mb-2"><strong>Couleur :</strong> <?= htmlspecialchars($selectedColor ?? '') ?></p>
                    <div class="d-flex gap-2 mb-3 flex-wrap">
                        <?php foreach ($article['variantes'] as $variante): ?>
                            <?php
                            $couleur = $variante['couleur'];
                            if (in_array($couleur, $couleursUniques, true)) {
                                continue;
                            }
                            $couleursUniques[] = $couleur;
                            $hex = $arrayColor[$couleur] ?? '#6c757d';
                            
                            ?>
                            <form action="/setColor/<?= $couleur ?>/<?= $article['id'] ?>"
                                method="POST"
                                class="color-form">
                                <button type="submit"
                                    class="color-option rounded-circle border-black"
                                    style="background-color: <?= $hex ?>;"
                                    title="<?= htmlspecialchars($couleur) ?>"></button>
                            </form>
                        <?php endforeach; ?>
                    </div>

                    <p class="mb-1"><strong>Taille</strong></p>
                    <select name="taille" class="form-select mb-3">
                        <?php foreach ($variantesFiltrees as $variante): ?>
                            <option value="<?= htmlspecialchars($variante['taille']) ?>">
                                <?= htmlspecialchars($variante['taille']) ?>
                                <?= ($variante['stock']) === 0 ? ' (rupture)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <a href="" class="btn btn-dark w-100">Ajouter au panier</a>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <a href="/catalogue" class="btn btn-secondary">← Retour au catalogue</a>
    </div>
</div>