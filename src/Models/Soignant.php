<?php

/**
 * Fichier : User.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Entite Soignant
 */

namespace App\Models;

use App\Outils\Database;
use PDO;

class Soignant
{
    public $id;
    public $nom;
    public $prenom;
    public $email;
    public $mot_de_passe;
    public $service;
    public $telephone;

    public function __construct($id, $nom, $prenom, $email, $mot_de_passe, $service, $telephone)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->mot_de_passe = $mot_de_passe;
        $this->service = $service;
        $this->telephone = $telephone;
    }

    public function isUnique($id = null)
    {
        $db = Database::getInstance()->getConnection();
        if ($id === null) {
            $stmt = $db->prepare('SELECT id FROM soignant WHERE email = :email');
            $stmt->execute([':email' => trim($this->email)]);
        } else {
            $stmt = $db->prepare('SELECT id FROM soignant WHERE email = :email AND id != :id');
            $stmt->execute([':email' => trim($this->email), ':id' => $id]);
        }
        return $stmt->fetch() === false;
    }


    public function createSoignant($role = 'Soignant')
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare(
            "INSERT INTO soignant (nom, prenom, email, mot_de_passe, service,telephone, role)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $success = $stmt->execute([
            $this->nom,
            $this->prenom,
            trim($this->email),
            password_hash($this->mot_de_passe, PASSWORD_ARGON2ID),
            $this->service,
            $this->telephone,
            $role
        ]);

        if ($success) {
            return $db->lastInsertId();
        }

        return false;
    }

    public function login()
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare('SELECT id, nom, prenom, mot_de_passe, role, statut FROM soignant WHERE email = ?');
        $stmt->execute([$this->email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$user) {
            return ['success' => false, 'message' => 'Email ou mot de passe incorrect.'];
        }


        if ($user['statut'] === 'Inactif') {
            return ['success' => false, 'message' => 'Compte inactif, vous ne pouvez pas vous connecter.'];
        }


        if (!password_verify($this->mot_de_passe, $user['mot_de_passe'])) {
            return ['success' => false, 'message' => 'Email ou mot de passe incorrect.'];
        }


        return ['success' => true, 'user' => $user, 'message' => 'Connexion réussie, bienvenue ' . $user['prenom']];
    }

    public function getAll()
    {
        $db = Database::getInstance()->getConnection();

        $sql = "SELECT id, nom, prenom, email, service, telephone, role, statut 
            FROM soignant ORDER BY nom ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, nom, prenom, email, service, telephone, role, statut 
                          FROM soignant 
                          WHERE id = :id");
        $stmt->execute([':id' => $this->id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function resetPassword()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE soignant SET mot_de_passe = :mdp WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':mdp' => $this->mot_de_passe,
            ':id' => $this->id,
        ]);
    }


    public function update($role = 'Soignant')
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE soignant 
            SET nom = :nom, prenom = :prenom, email = :email, 
                service = :service, telephone = :telephone, role = :role
            WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':nom' => $this->nom,
            ':prenom' => $this->prenom,
            ':email' => $this->email,
            ':service' => $this->service,
            ':telephone' => $this->telephone,
            ':id' => $this->id,
            ':role' => $role
        ]);
    }

    public function isActive()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT statut FROM soignant WHERE id = :id");
        $stmt->execute([':id' => $this->id]);
        $statut = $stmt->fetchColumn();
        return $statut === 'Actif';
    }

    public function activer()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE soignant SET statut = 'Actif' WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }

    public function desactiver()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE soignant SET statut = 'Inactif' WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }

    public function changePassword($newPwd)
    {
        $db = Database::getInstance()->getConnection();


        $stmt = $db->prepare("SELECT mot_de_passe FROM soignant WHERE id = :id");
        $stmt->execute([':id' => $this->id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return ['success' => false, 'message' => 'Utilisateur introuvable.'];
        }


        if (!password_verify($this->mot_de_passe, $row['mot_de_passe'])) {
            return ['success' => false, 'message' => 'Le mot de passe actuel est incorrect.'];
        }


        if (password_verify($newPwd, $row['mot_de_passe'])) {
            return ['success' => false, 'message' => 'Le nouveau mot de passe doit être différent de l\'ancien.'];
        }

        $hash = password_hash($newPwd, PASSWORD_ARGON2ID);

        $stmtUpdate = $db->prepare("
        UPDATE soignant 
        SET mot_de_passe = :mdp 
        WHERE id = :id
    ");
        $success = $stmtUpdate->execute([
            ':mdp' => $hash,
            ':id'  => $this->id,
        ]);

        if (!$success) {
            return ['success' => false, 'message' => 'Erreur technique lors de la mise à jour.'];
        }

        return ['success' => true, 'message' => 'Mot de passe modifié.'];
    }
}
