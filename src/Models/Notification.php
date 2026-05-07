<?php

/**
 * Fichier : Notification.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Entité Notification
 */

namespace App\Models;

use App\Outils\Database;
use PDO;

class Notification
{
    public $id;
    public $idDestinataire;
    public $type;
    public $titre;
    public $message;

    public function __construct($id, $idDestinataire, $type, $titre, $message)
    {
        $this->id = $id;
        $this->idDestinataire = $idDestinataire;
        $this->type = $type;
        $this->titre = $titre;
        $this->message  = $message;
    }

   
    public function create()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "
            INSERT INTO notification (id_soignant, type, titre, message, date_envoi, lu)
            VALUES (:id_soignant, :type, :titre, :message, NOW(), 0)
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':id_soignant' => $this->idDestinataire,
            ':type' => $this->type,
            ':titre' => $this->titre,
            ':message' => $this->message,
        ]);
        $this->id = $db->lastInsertId();
        return $this->id;
    }

    
    public function getByDestinataire()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT id, type, titre, message, date_envoi, lu
            FROM notification
            WHERE id_soignant = :id
            ORDER BY date_envoi DESC
        ");
        $stmt->execute([':id' => $this->idDestinataire]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function countUnread()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM notification
            WHERE id_soignant = :id
            AND lu = 0
        ");
        $stmt->execute([':id' => $this->idDestinataire]);
        return (int)$stmt->fetchColumn();
    }

   
    public function markAsRead()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            UPDATE notification
            SET lu = 1
            WHERE id = :id
            AND   id_soignant = :id_soignant
        ");
        return $stmt->execute([
            ':id' => $this->id,
            ':id_soignant' => $this->idDestinataire,
        ]);
    }

    
    public function markAllAsRead()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            UPDATE notification
            SET lu = 1
            WHERE id_soignant = :id
            AND   lu = 0
        ");
        return $stmt->execute([':id' => $this->idDestinataire]);
    }

    
    public  function getAllAdminIds()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT id FROM soignant
            WHERE role = 'Administrateur'
            AND   statut = 'Actif'
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}