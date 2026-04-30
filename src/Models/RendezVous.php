<?php
/**
 * Fichier : RendezVous.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Entité Rendez-vous
 */

namespace App\Models;

use App\Outils\Database;
use PDO;

class RendezVous
{
    private $id;
    private $idReservation;
    private $dateRdv;
    private $heureRdv;
    private $lieu;

    public function __construct($id, $idReservation, $dateRdv, $heureRdv, $lieu)
    {
        $this->id            = $id;
        $this->idReservation = $idReservation;
        $this->dateRdv       = $dateRdv;
        $this->heureRdv      = $heureRdv;
        $this->lieu          = $lieu;
    }

    public function create()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "
            INSERT INTO rendez_vous (id_reservation, date_rdv, heure_rdv, lieu)
            VALUES (:id_reservation, :date_rdv, :heure_rdv, :lieu)
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':id_reservation' => $this->idReservation,
            ':date_rdv'       => $this->dateRdv,
            ':heure_rdv'      => $this->heureRdv,
            ':lieu'           => $this->lieu,
        ]);
        $this->id = $db->lastInsertId();
        return $this->id;
    }

    public function existsForReservation()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT id FROM rendez_vous WHERE id_reservation = :id
        ");
        $stmt->execute([':id' => $this->idReservation]);
        return $stmt->fetchColumn() !== false;
    }

    public function isCreneauPris()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT id FROM rendez_vous 
            WHERE date_rdv  = :date_rdv 
            AND heure_rdv = :heure_rdv 
            AND lieu = :lieu
        ");
        $stmt->execute([
            ':date_rdv'  => $this->dateRdv,
            ':heure_rdv' => $this->heureRdv,
            ':lieu'      => $this->lieu,
        ]);
        return $stmt->fetchColumn() !== false;
    }
}