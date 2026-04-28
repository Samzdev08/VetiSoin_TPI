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

class ReservationItem{

    private $id;
    private $reservationId;
    private $articleId;
    private $quantity;

    public function __construct($id, $reservationId, $articleId, $quantity) {
        $this->id = $id;
        $this->reservationId = $reservationId;
        $this->articleId = $articleId;
        $this->quantity = $quantity;
    }

    public function create(){

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

    public function updateStock() {
        $db = Database::getInstance()->getConnection();

        $sql = "UPDATE article_variante SET stock = stock - :quantity WHERE id = :articleId";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':quantity' => $this->quantity,
            ':articleId' => $this->articleId
        ]);

        return $stmt->rowCount();
        
    }


}