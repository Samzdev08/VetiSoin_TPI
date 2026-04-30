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
    public $id;
    public $idArticle;
    public $taille;
    public $couleur;
    public $photo;
    public $stock;

    public function __construct($id, $idArticle, $taille, $couleur, $photo, $stock)
    {
        $this->id = $id;
        $this->idArticle = $idArticle;
        $this->taille = $taille;
        $this->couleur = $couleur;
        $this->photo = $photo;
        $this->stock = $stock;
    }

    public function getStockById()
    {

        $db = Database::getInstance()->getConnection();

        $sql = "SELECT stock FROM article_variante WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $this->id]);

        return $stmt->fetchColumn();
    }


    public function updateArticleVariante()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE article_variante 
            SET stock = :stock, photo = :photo 
            WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':stock' => $this->stock,
            ':photo' => $this->photo,
            ':id'    => $this->id,
        ]);
    }

    public function getArticleId()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id_article FROM article_variante WHERE id = :id");
        $stmt->execute([':id' => $this->id]);
        return $stmt->fetchColumn();
    }

    public function create()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO article_variante (id_article, taille, couleur, photo, stock) 
            VALUES (:idArticle, :taille, :couleur, :photo, :stock)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':idArticle' => $this->idArticle,
            ':taille'    => $this->taille,
            ':couleur'   => $this->couleur,
            ':photo'     => $this->photo,
            ':stock'     => $this->stock,
        ]);
    }
}
