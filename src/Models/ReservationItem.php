<?php

/**
 * Fichier : ReservationItem.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Entite Ligne d'article reserve
 */

namespace App\Models;

use App\Outils\Database;
use PDO;

class ReservationItem
{

    private $id;
    private $reservationId;
    private $articleId;
    private $quantity;

    public function __construct($id, $reservationId, $articleId, $quantity)
    {
        $this->id = $id;
        $this->reservationId = $reservationId;
        $this->articleId = $articleId;
        $this->quantity = $quantity;
    }

    public function create()
    {

        $db = Database::getInstance()->getConnection();

        $sql = "INSERT INTO article_reserve (id_reservation, id_article_variante, quantite) 
        VALUES (:reservationId, :articleId, :quantity)";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':reservationId' => $this->reservationId,
            ':articleId' => $this->articleId,
            ':quantity' => $this->quantity
        ]);

        $this->id = $db->lastInsertId();

        return $this->id;
    }

    public function updateStock()
    {
        $db = Database::getInstance()->getConnection();

        $sql = "UPDATE article_variante SET stock = stock - :quantity WHERE id = :articleId";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':quantity' => $this->quantity,
            ':articleId' => $this->articleId
        ]);

        return $stmt->rowCount();
    }

    public function findById()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
        SELECT id FROM article_reserve
        WHERE id_reservation = :id ");
        $stmt->execute([':id' => $this->reservationId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function retourner(): bool
    {
        $db = Database::getInstance()->getConnection();
        try {
            $db->beginTransaction();


            $stmt = $db->prepare("
            UPDATE article_reserve
            SET est_retourne = 1, date_retour = NOW()
            WHERE id = :id
            AND est_retourne = 0
        ");
            $stmt->execute([':id' => $this->id]);

            if ($stmt->rowCount() === 0) {
                $db->rollBack();
                return false;
            }


            $stmtRestock = $db->prepare("
            UPDATE article_variante
            SET stock = stock + :quantite
            WHERE id = :id_variante
        ");
            $stmtRestock->execute([
                ':quantite'    => $this->quantity,
                ':id_variante' => $this->articleId
            ]);

            $db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            return false;
        }
    }
}
