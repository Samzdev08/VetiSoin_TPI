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


    public function __construct($id, $nom, $description)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
    }

    public function getCategroy()
    {

        $db = Database::getInstance()->getConnection();

        $sql = "SELECT id, nom FROM categorie";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
