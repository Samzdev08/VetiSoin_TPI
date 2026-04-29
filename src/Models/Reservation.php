<?php

/**
 * Fichier : Reservation.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Entite Reservation
 */

namespace App\Models;

use App\Outils\Database;
use PDO;

class Reservation
{
    public $id;
    public $patient_id;
    public $soignant;
    public $date_retrait;
    public $statut;
    public $commentaires;

    public function __construct($id, $patient_id, $soignant, $date_retrait, $statut, $commentaires)
    {
        $this->id = $id;
        $this->soignant = $soignant;
        $this->patient_id = $patient_id;
        $this->date_retrait = $date_retrait;
        $this->statut = $statut;
        $this->commentaires = $commentaires;
    }

    public function create()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO reservation (id_soignant, id_patient, date_retrait_effective, commentaire) 
        VALUES (:soignant, :patient_id, :date_retrait, :commentaires)");
        $stmt->execute([

            ':soignant' => $this->soignant,
            ':patient_id' => $this->patient_id,
            ':date_retrait' => $this->date_retrait,
            ':commentaires' => $this->commentaires

        ]);

        $this->id = $db->lastInsertId();

        return $this->id;
    }
    public function getReservationsBySoignantId()
    {
        $db = Database::getInstance()->getConnection();

        $sql = "
        SELECT 
            r.id,
            r.date_retrait_effective,
            r.commentaire,
            r.statut,
            r.is_archived,
            p.nom AS patient_nom,
            p.prenom AS patient_prenom
        FROM reservation r
        JOIN patient p ON p.id = r.id_patient
        WHERE r.id_soignant = :soignantId
        ";

        $params = [':soignantId' => $this->soignant];

        if ($this->statut) {
            $sql .= " AND r.statut = :statut";
            $params[':statut'] = $this->statut;
        }

        $sql .= " ORDER BY r.date_retrait_effective DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

 
}
