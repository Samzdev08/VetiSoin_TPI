<?php

/**
 * Fichier : detail.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Fiche detaillee d'un article
 */
/** @var array $article */
/** @var string|null $selectedColor */
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



$photoActive = $variantesFiltrees[0]['photo'];
$stockInitial = $variantesFiltrees[0]['stock'] ?? 0;
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
                <img src="../<?= htmlspecialchars($photoActive) ?>"
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
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                <button type="submit"
                                    class="color-option rounded-circle border-black"
                                    style="background-color: <?= $hex ?>;"
                                    title="<?= htmlspecialchars($couleur) ?>"></button>
                            </form>
                        <?php endforeach; ?>
                    </div>


                    <form action="/panier/add" method="post" id="form-panier">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <input type="hidden" name="variante_id" value="" id="variante_article-input">
                        <input type="hidden" name="nom" value="<?= $article['nom'] ?>">
                        <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                        <input type="hidden" name="couleur" value="<?= $selectedColor ?>">
                        <input type="hidden" name="photo" value="<?= $photoActive ?>">
                        <input type="hidden" name="marque" value="<?= $article['marque'] ?>">
                        <input type="hidden" name="maxStock" value="" id="maxStock-input">

                        <p class="mb-1"><strong>Taille</strong></p>
                        <select name="taille" id="select-taille" class="form-select mb-3">
                            <?php foreach ($variantesFiltrees as $variante): ?>
                                <option value="<?= $variante['taille'] ?>"
                                    data-stock="<?= $variante['stock'] ?>" data-id="<?= $variante['id'] ?>">  
                                    <?= htmlspecialchars($variante['taille']) ?> — <?= $variante['stock'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <p class="mb-1"><strong>Quantité</strong></p>
                        <input type="number"
                            name="quantite"
                            id="input-quantite"
                            min="1"
                            max="<?= $stockInitial ?>"
                            value="1"
                            class="form-control mb-3"
                            style="width: 100px;">

                        <button type="submit" class="btn btn-primary">
                            Ajouter au panier
                        </button>
                    </form>


                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <a href="/catalogue" class="btn btn-secondary">← Retour au catalogue</a>
    </div>
</div>

<script>

    const variante_article_input = document.getElementById('variante_article-input');
    const selectTaille = document.getElementById('select-taille');
    const inputQuantite = document.getElementById('input-quantite');
    const maxStockInput = document.getElementById('maxStock-input');

    function updateMaxQuantite() {

        const selectedOption = selectTaille.options[selectTaille.selectedIndex];

        const stock = parseInt(selectedOption.dataset.stock);
        const varianteId = selectedOption.dataset.id;
        const quantite = parseInt(inputQuantite.value);

        maxStockInput.value = stock;
        inputQuantite.max = stock;
        variante_article_input.value = varianteId;

        if (quantite > stock) {
            inputQuantite.value = stock;
        }
    }

    selectTaille.addEventListener('change', updateMaxQuantite)

    updateMaxQuantite();
</script>