<?php

/**
 * Fichier : Category.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Entite Categorie
 */


namespace App\Models;

use App\Outils\Database;
use PDO;

class Category
{
    public $id;
    public $nom;
    public $description;
    public $type_taille;


    public function __construct($id, $nom, $description, $type_taille)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->type_taille = $type_taille;
    }

    public function getCategroy()
    {

        $db = Database::getInstance()->getConnection();

        $sql = "SELECT id, nom, description, type_taille FROM categorie";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO categorie (nom, description, type_taille) 
            VALUES (:nom, :description, :typeTaille)";
        $stmt = $db->prepare($sql);
        $success = $stmt->execute([
            ':nom' => $this->nom,
            ':description' => $this->description,
            ':typeTaille' => $this->type_taille,
        ]);
        return $success ? $db->lastInsertId() : false;
    }

    public function isUnique()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM categorie WHERE nom = :nom");
        $stmt->execute([':nom' => $this->nom]);
        return $stmt->fetchColumn() == 0;
    }
}
