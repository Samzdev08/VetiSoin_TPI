<?php
/**
 * Fichier : list.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Catalogue d'articles avec filtres
 */


?>

<h1>Catalogue</h1>

<form action="/catalogue" method="GET">

    <input type="hidden" name="genre" id="genre-hidden" value="">
    <input type="hidden" name="taille" id="taille-hidden" value="">
    <input type="hidden" name="categorie" id="categorie-hidden" value="">

    <label for="recherche">Recherche</label>
    <input type="text" id="recherche" name="recherche" placeholder="Nom de l'article">

    <p>Catégorie</p>
    <button type="submit" onclick="">Toutes</button>
    <button type="submit" onclick="">Hauts</button>
    <button type="submit" onclick="">Pantalons</button>
    <button type="submit" onclick="">Chaussures</button>
    <button type="submit" onclick="">Pulls</button>

    <p>Genre</p>
    <button type="submit" onclick="">Tous</button>
    <button type="submit" onclick="">Homme</button>
    <button type="submit" onclick="">Femme</button>
    <button type="submit" onclick=">Mixte</button>

    <p>Taille</p>
    <button type="submit" onclick="">Toutes</button>
    <button type="submit" onclick="">XS</button>
    <button type="submit" onclick="">S</button>
    <button type="submit" onclick="">M</button>
    <button type="submit" onclick="">L</button>
    <button type="submit" onclick="">XL</button>
    <button type="submit" onclick="">XXL</button>

    <br>
    <a href="/catalogue">Réinitialiser</a>

</form>

<hr>



