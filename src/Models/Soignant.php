<?php

/**
 * Fichier : User.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Entite Soignant / Administrateur
 */

namespace App\Models;

use App\Outils\Database;
use PDO;

class Soignant
{
    public $nom;
    public $prenom;
    public $email;
    public $mot_de_passe;
    public $service;
    public $telephone;

    public function __construct($nom, $prenom, $email, $mot_de_passe, $service, $telephone)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->mot_de_passe = $mot_de_passe;
        $this->service = $service;
        $this->telephone = $telephone;
    }

    public function isUnique()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT id FROM soignant WHERE email = ?');
        $stmt->execute([
            $this->email

        ]);
        
        return $stmt->fetch() === false;
    }


    public function createSoignant()
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare(
            "INSERT INTO soignant (nom, prenom, email, mot_de_passe, service, telephone)
         VALUES (?, ?, ?, ?, ?, ?)"
        );

        $success = $stmt->execute([
            $this->nom,
            $this->prenom,
            trim($this->email),
            password_hash($this->mot_de_passe, PASSWORD_ARGON2ID),
            $this->service,
            $this->telephone
        ]);

        if ($success) {
            return $db->lastInsertId();
        }

        return false;
    }
}
