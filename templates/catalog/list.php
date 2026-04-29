<?php

/**
 * Fichier : list.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Catalogue d'articles avec filtres
 */
/** @var array $articles */

?>

<h1>Catalogue</h1>

<form action="/catalogue" method="GET">

    <input type="hidden" name="genre" id="genre-hidden" value="<?= htmlspecialchars($_GET['genre'] ?? '')  ?>">
    <input type="hidden" name="taille" id="taille-hidden" value="<?= htmlspecialchars($_GET['taille'] ?? '')  ?>">
    <input type="hidden" name="categorie" id="categorie-hidden" value="<?= htmlspecialchars($_GET['categorie'] ?? '')  ?>">

    <label for="recherche">Recherche</label>
    <input type="text" id="recherche" name="recherche" placeholder="Nom de l'article" onchange="this.form.submit()" value="<?= htmlspecialchars($_GET['recherche'] ?? '') ?>">

    <p>Catégorie</p>
    <button type="submit" onclick="setValue('categorie-hidden', '')">Toutes</button>
    <button type="submit" onclick="setValue('categorie-hidden', '1')">Blouses</button>
    <button type="submit" onclick="setValue('categorie-hidden', '2')">Pantalons</button>
    <button type="submit" onclick="setValue('categorie-hidden', '3')">Tuniques</button>
    <button type="submit" onclick="setValue('categorie-hidden', '4')">Casaques</button>
    <button type="submit" onclick="setValue('categorie-hidden', '5')">Chaussures</button>
    <button type="submit" onclick="setValue('categorie-hidden', '6')">Coifffes</button>
    <button type="submit" onclick="setValue('categorie-hidden', '7')">Vestes & Polaires</button>

    <p>Genre</p>
    <button type="submit" onclick="setValue('genre-hidden', '')">Tous</button>
    <button type="submit" onclick="setValue('genre-hidden', 'Homme')">Homme</button>
    <button type="submit" onclick="setValue('genre-hidden', 'Femme')">Femme</button>
    <button type="submit" onclick="setValue('genre-hidden', 'Mixte')">Mixte</button>

    <p>Taille</p>
    <button type="submit" onclick="setValue('taille-hidden', '')">Toutes</button>
    <button type="submit" onclick="setValue('taille-hidden', 'XS')">XS</button>
    <button type="submit" onclick="setValue('taille-hidden', 'S')">S</button>
    <button type="submit" onclick="setValue('taille-hidden', 'M')">M</button>
    <button type="submit" onclick="setValue('taille-hidden', 'L')">L</button>
    <button type="submit" onclick="setValue('taille-hidden', 'XL')">XL</button>
    <button type="submit" onclick="setValue('taille-hidden', 'XXL')">XXL</button>

    <br>
    <a href="/catalogue" id="filter-form">Réinitialiser</a>

</form>

<?php if ($articles): ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 container-articles">
        <?php foreach ($articles as $article): ?>
            <div class="col articles" data-id="<?= $article['id'] ?>">
                <div class="card h-100 shadow-sm article-item" style="cursor: pointer;">
                    <div class="card-body text-center position-relative">

                        <img src="<?= $article['photo'] ?>" alt="<?= htmlspecialchars($article['nom']) ?>" class="card-img-top mb-3" style="height: 200px; object-fit: cover;">
                        <h5 class="card-title fw-bold"><?= htmlspecialchars($article['nom']) ?></h5>
                        <p class="card-text text-muted"><?= htmlspecialchars($article['marque']) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Aucune article disponible pour le moment.</p>
<?php endif; ?>

<div id="pagination"></div>

<script>
    function setValue(input, value) {

        document.getElementById(input).value = value;
        
    }

    const pagination = document.getElementById('pagination');

    const articles = document.querySelectorAll('.articles');

    let pages = Math.ceil(articles.length / 12)

    let count = 0;

    function Affichage(page) {

        articles.forEach((item, index) => {
            item.style.display = Math.floor(index / 12) === page ? '' : 'none';
        });

        pagination.innerHTML = '';

        for (let i = 0; i < pages; i++) {
            let btn = document.createElement('button');
            btn.textContent = i + 1;
            btn.classList.add('btn', 'btn-outline-primary', 'm-1');

            btn.addEventListener('click', () => {

                count = i
                Affichage(count);

            })

            pagination.appendChild(btn);
        }

    }


    Affichage(count);
    console.log(document.querySelectorAll('.articles'))

    
    document.querySelectorAll('.articles').forEach(item => {
        item.addEventListener('click', () => {
            const id = item.getAttribute('data-id');
            window.location.href = `/catalogue/${id}`;
        });
    });
</script>