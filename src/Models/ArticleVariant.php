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

        if ($this->photo) {
            $sql = "UPDATE article_variante 
                SET stock = :stock, taille = :taille, photo = :photo 
                WHERE id = :id";
            $params = [
                ':stock'  => $this->stock,
                ':taille' => $this->taille,
                ':photo'  => $this->photo,
                ':id'     => $this->id,
            ];
        } else {
            $sql = "UPDATE article_variante 
                SET stock = :stock, taille = :taille 
                WHERE id = :id";
            $params = [
                ':stock'  => $this->stock,
                ':taille' => $this->taille,
                ':id'     => $this->id,
            ];
        }

        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
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
    public function getCouleurs()
    {
        $db = Database::getInstance()->getConnection();

        $sql = "SELECT DISTINCT couleur 
            FROM article_variante";

        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getInfosVariante()
    {

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT a.nom AS article_nom, av.taille, av.couleur, av.stock
            FROM article_variante av
            JOIN article a ON a.id = av.id_article
            WHERE av.id = :id
        ");
        $stmt->execute([':id' => $this->id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPhoto()
    {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT photo FROM article_variante WHERE id = :id');
        $stmt->execute([':id' => $this->id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getIdVariante()
    {

        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
        SELECT id_article_variante 
        FROM article_reserve 
        WHERE id = :id ");
        $stmt->execute([':id' => $this->id]);
        $idVariante = $stmt->fetchColumn();

        return $idVariante;
    }
}
