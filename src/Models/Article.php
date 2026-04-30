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
    public $id;
    public $idCategorie;
    public $nom;
    public $genre;
    public $matiere;
    public $marque;
    public $taille;

    public function __construct($id, $idCategorie, $nom, $genre, $matiere, $marque, $taille)
    {
        $this->id = $id;
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
        MIN(v.photo) AS photo
    FROM article a
    INNER JOIN article_variante v ON v.id_article = a.id
    WHERE v.stock > 0';

        if ($this->genre) {
            $sql .= ' AND a.genre = :genre';
            $params[':genre'] = $this->genre;
        }
        if ($this->taille) {
            $sql .= ' AND EXISTS (
            SELECT 1 FROM article_variante v2
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
            $sql .= ' AND (a.marque LIKE :nom1 OR a.nom LIKE :nom)';
            $params[':nom1'] = "%$this->nom%";
            $params[':nom']  = "%$this->nom%";
        }

        $sql .= ' GROUP BY a.id, a.nom, a.marque, a.genre, a.id_categorie';
        $sql .= ' ORDER BY MAX(a.date_creation) DESC';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById()
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare('SELECT v.id AS variante_id, a.id, a.nom, a.genre, a.matiere, a.marque, c.nom AS categorie, v.taille, v.couleur, v.stock, v.photo
        FROM article a
        INNER JOIN categorie c ON a.id_categorie = c.id
        INNER JOIN article_variante v ON v.id_article = a.id
        WHERE a.id = ? AND v.stock > 0');
        $stmt->execute([$this->id]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return null;
        }

        $article = [
            'id' => $rows[0]['id'],
            'nom' => $rows[0]['nom'],
            'genre' => $rows[0]['genre'],
            'matiere' => $rows[0]['matiere'],
            'marque' => $rows[0]['marque'],
            'categorie' => $rows[0]['categorie'],
            'variantes' => []
        ];

        foreach ($rows as $row) {
            $article['variantes'][] = [
                'id' => $row['variante_id'],
                'taille' => $row['taille'],
                'couleur' => $row['couleur'],
                'stock' => $row['stock'],
                'photo' => $row['photo']
            ];
        }
        return $article;
    }


    public function update()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE article 
            SET nom = :nom, 
                marque = :marque, 
                matiere = :matiere, 
                genre = :genre, 
                id_categorie = :idCategorie 
            WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':nom'         => $this->nom,
            ':marque'      => $this->marque,
            ':matiere'     => $this->matiere,
            ':genre'       => $this->genre,
            ':idCategorie' => $this->idCategorie,
            ':id'          => $this->id,
        ]);
    }
    public function create()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO article (id_categorie, nom, genre, matiere, marque, date_creation) 
            VALUES (:idCategorie, :nom, :genre, :matiere, :marque, NOW())";
        $stmt = $db->prepare($sql);
        $success = $stmt->execute([
            ':idCategorie' => $this->idCategorie,
            ':nom' => $this->nom,
            ':genre' => $this->genre,
            ':matiere' => $this->matiere,
            ':marque' => $this->marque,
        ]);
        return $success ? $db->lastInsertId() : false;
    }
}
