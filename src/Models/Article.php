<?php

/**
 * Fichier : Article.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Entite Article
 */

namespace App\Models;

use App\Outils\Database;

use PDO;

class Article
{
    public $idCategorie;
    public $nom;
    public $genre;
    public $matiere;
    public $marque;
    public $taille;

    public function __construct($idCategorie, $nom, $genre, $matiere, $marque,$taille)
    {
        $this->idCategorie = $idCategorie;
        $this->nom = $nom;
        $this->genre = $genre;
        $this->matiere = $matiere;
        $this->marque = $marque;
        $this->taille = $taille;
    }

    public function getAll()
    {
        $params = [];
        $db = Database::getInstance()->getConnection();

        $sql = 'SELECT
        a.id,
        a.nom,
        a.marque,
        a.genre,
        a.id_categorie,
        v.photo
        FROM article a
        INNER JOIN article_variante v 
            ON v.id_article = a.id
        WHERE v.stock > 0';

        if ($this->genre) {
            $sql .= ' AND a.genre = :genre';
            $params[':genre'] = $this->genre;
        }

        if ($this->taille) {
            $sql .= ' AND EXISTS (
            SELECT 1
            FROM article_variante v2
            WHERE v2.id_article = a.id
            AND v2.taille = :taille
            AND v2.stock > 0
        )';
            $params[':taille'] = $this->taille;
        }

        if ($this->idCategorie) {
            $sql .= ' AND a.id_categorie = :categorie';
            $params[':categorie'] = $this->idCategorie;
        }

        if ($this->nom) {
            $sql .= ' AND (a.marque LIKE :nom OR a.nom LIKE :nom)';
            $params[':nom'] = "%$this->nom%";
        }

        $sql .= ' ORDER BY a.date_creation DESC';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
