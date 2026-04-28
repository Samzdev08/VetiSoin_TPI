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
    public $commentaires;

    public function __construct($id, $patient_id, $soignant, $date_retrait, $commentaires )
    {
        $this->id = $id;
        $this->soignant = $soignant;
        $this->patient_id = $patient_id;
        $this->date_retrait = $date_retrait;
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
}
