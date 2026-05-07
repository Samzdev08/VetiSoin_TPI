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
    private $statut;
    private $idSoignant;

    public function __construct($id, $idReservation, $dateRdv, $heureRdv, $lieu, $statut = null, $idSoignant = null)
    {
        $this->id  = $id;
        $this->idReservation = $idReservation;
        $this->dateRdv = $dateRdv;
        $this->heureRdv = $heureRdv;
        $this->lieu  = $lieu;
        $this->statut = $statut;
        $this->idSoignant = $idSoignant;
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

    public function getIdByReservation()
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
        SELECT id 
        FROM rendez_vous 
        WHERE id_reservation = :id ");

        $stmt->execute([
            ':id' => $this->idReservation
        ]);

        return $stmt->fetchColumn();
    }
    public function getIdReservation()
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
        SELECT id_reservation
        FROM rendez_vous 
        WHERE id = :id ");

        $stmt->execute([
            ':id' => $this->id
        ]);

        return $stmt->fetchColumn();
    }

    public function isCreneauPris()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT id FROM rendez_vous 
            WHERE date_rdv = :date_rdv 
            AND heure_rdv = :heure_rdv 
            AND lieu = :lieu
        ");
        $stmt->execute([
            ':date_rdv' => $this->dateRdv,
            ':heure_rdv' => $this->heureRdv,
            ':lieu' => $this->lieu,
        ]);
        return $stmt->fetchColumn() !== false;
    }

    public function getRendezVousBySoignantId()
    {
        $db = Database::getInstance()->getConnection();

        $sql = "
            SELECT 
                rv.id,
                rv.date_rdv,
                rv.heure_rdv,
                rv.lieu,
                rv.statut,
                r.id  AS id_reservation,
                r.statut AS statut_reservation,
                p.nom AS patient_nom,
                p.prenom AS patient_prenom,
                p.numero_dossier,
                p.chambre
            FROM rendez_vous rv
            JOIN reservation r ON r.id  = rv.id_reservation
            JOIN patient p     ON p.id  = r.id_patient
            WHERE r.id_soignant = :idSoignant
        ";

        $params = [':idSoignant' => $this->idSoignant];

        if ($this->statut) {
            $sql .= " AND rv.statut = :statut";
            $params[':statut'] = $this->statut;
        }

        $sql .= " ORDER BY rv.date_rdv DESC, rv.heure_rdv DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getRendezVousById()
    {
        $db = Database::getInstance()->getConnection();

        $sql = "
            SELECT 
                rv.id,
                rv.date_rdv,
                rv.heure_rdv,
                rv.lieu,
                rv.statut,
                r.id AS id_reservation,
                r.id_soignant AS id_soignant,
                r.date_reservation,
                r.statut AS statut_reservation,
                r.commentaire,
                p.nom AS patient_nom,
                p.prenom AS patient_prenom,
                p.numero_dossier,
                p.chambre,
                p.service AS patient_service,
                s.nom AS soignant_nom,
                s.prenom AS soignant_prenom
            FROM rendez_vous rv
            JOIN reservation r ON r.id = rv.id_reservation
            JOIN patient p ON p.id = r.id_patient
            JOIN soignant s ON s.id = r.id_soignant
            WHERE rv.id = :id
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $this->id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getAllAdmin($service = null)
    {
        $db = Database::getInstance()->getConnection();

        $sql = "
            SELECT 
                rv.id,
                rv.date_rdv,
                rv.heure_rdv,
                rv.lieu,
                rv.statut,
                r.id AS id_reservation,
                r.statut AS statut_reservation,
                p.nom AS patient_nom,
                p.prenom AS patient_prenom,
                p.service AS patient_service,
                p.chambre,
                s.id AS id_soignant,
                s.nom AS soignant_nom,
                s.prenom AS soignant_prenom
            FROM rendez_vous rv
            JOIN reservation r ON r.id = rv.id_reservation
            JOIN patient p ON p.id = r.id_patient
            JOIN soignant s ON s.id = r.id_soignant
            WHERE 1 = 1
        ";

        $params = [];

        if ($this->statut) {
            $sql .= " AND rv.statut = :statut";
            $params[':statut'] = $this->statut;
        }

        if ($this->idSoignant) {
            $sql .= " AND r.id_soignant = :id_soignant";
            $params[':id_soignant'] = $this->idSoignant;
        }

        if ($service) {
            $sql .= " AND p.service = :service";
            $params[':service'] = $service;
        }

        if ($this->dateRdv) {
            $sql .= " AND rv.date_rdv = :date";
            $params[':date'] = $this->dateRdv;
        }

        $sql .= " ORDER BY rv.date_rdv DESC, rv.heure_rdv DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    public function update()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "
            UPDATE rendez_vous
            SET date_rdv  = :date_rdv,
                heure_rdv = :heure_rdv,
                lieu  = :lieu
            WHERE id  = :id
            AND   statut = 'Planifié'
        ";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':date_rdv'  => $this->dateRdv,
            ':heure_rdv' => $this->heureRdv,
            ':lieu'      => $this->lieu,
            ':id'        => $this->id,
        ]);
    }


    public function cancel($id)
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE rendez_vous 
                SET statut = 'Annulé' 
                WHERE id = :id AND statut != 'Annulé'";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }


    public function marquerRealise()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE rendez_vous 
                SET statut = 'Réalisé' 
                WHERE id = :id AND statut = 'Planifié'";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }

    public function marquerNonHonore()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE rendez_vous 
                SET statut = 'Non honoré' 
                WHERE id = :id AND statut = 'Planifié'";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }
}
