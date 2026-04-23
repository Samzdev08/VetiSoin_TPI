<?php
/**
 * Fichier : ArticleVariant.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Entite Variante d'article (taille+couleur+stock)
 */

namespace App\Models;

use App\Outils\Database;
use PDO;

class ArticleVariant
{
    public $idArticle;
    public $taille;
    public $couleur;
    public $photo;
    public $stock;

    public function __construct($idArticle, $taille, $couleur, $photo, $stock)
    {
        $this->idArticle = $idArticle;
        $this->taille = $taille;
        $this->couleur = $couleur;
        $this->photo = $photo;
        $this->stock = $stock;
    }
}
