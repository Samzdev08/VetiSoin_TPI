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

    public function __construct($id, $soignant, $patient_id, $date_retrait, $statut, $commentaires)
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
            p.id As id_patient,
            p.nom AS patient_nom,
            p.prenom AS patient_prenom
        FROM reservation r
        JOIN patient p ON p.id = r.id_patient
        WHERE r.id_soignant = :soignantId
        AND r.is_archived = FALSE
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

    public function getReservationById()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
        SELECT 
            r.id,
            r.date_reservation,
            r.date_retrait_effective,
            r.statut,
            r.commentaire,
            r.is_archived,
            p.id As id_patient,
            p.nom AS patient_nom,
            p.prenom AS patient_prenom,
            p.numero_dossier,
            p.chambre,
            p.service AS patient_service,
            a.nom AS article_nom,
            a.marque,
            a.matiere,
            av.taille,
            av.couleur,
            av.stock,
            ar.id AS article_reserve_id,
            ar.quantite,
            ar.est_retourne,
            ar.date_retour
        FROM reservation r
        JOIN patient p           ON p.id  = r.id_patient
        JOIN article_reserve ar  ON ar.id_reservation = r.id
        JOIN article_variante av ON av.id = ar.id_article_variante
        JOIN article a           ON a.id  = av.id_article
        WHERE r.id = :reservationId
    ");
        $stmt->execute([':reservationId' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancel()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
        UPDATE reservation
        SET statut = 'Annulée'
        WHERE id = :id
        AND statut = 'En attente'
    ");
        return $stmt->execute([':id' => $this->id]);
    }

    public function update()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "
        UPDATE reservation
        SET id_patient = :id_patient,
            date_retrait_effective = :date_retrait,
            commentaire = :commentaire
        WHERE id = :id
        AND statut = 'En attente'
    ";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':id_patient'   => $this->patient_id,
            ':date_retrait' => $this->date_retrait,
            ':commentaire'  => $this->commentaires,
            ':id'           => $this->id,
        ]);
    }


    public function confirmer()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
        UPDATE reservation 
        SET statut = 'Confirmée'
        WHERE id     = :id
        AND   statut = 'En attente'
    ");
        return $stmt->execute([':id' => $this->id]);
    }
}
