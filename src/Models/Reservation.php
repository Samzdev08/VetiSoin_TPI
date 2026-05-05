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
            r.id_soignant AS soignant_id,
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
            ar.retour_demande,
            ar.est_retourne,
            ar.date_retour
        FROM reservation r
        JOIN patient p ON p.id  = r.id_patient
        LEFT JOIN article_reserve ar  ON ar.id_reservation = r.id
        LEFT JOIN article_variante av ON av.id = ar.id_article_variante
        LEFT JOIN article a ON a.id  = av.id_article
        WHERE r.id = :reservationId
    ");
        $stmt->execute([':reservationId' => $this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllAdmin($service = null)
    {
        $db = Database::getInstance()->getConnection();

        $sql = "
            SELECT 
                r.id,
                r.date_reservation,
                r.date_retrait_effective,
                r.statut,
                r.is_archived,
                p.nom AS patient_nom,
                p.prenom AS patient_prenom,
                p.service AS patient_service,
                p.chambre,
                s.id AS id_soignant,
                s.nom AS soignant_nom,
                s.prenom AS soignant_prenom,
                s.service AS soignant_service
            FROM reservation r
            JOIN patient p   ON p.id = r.id_patient
            JOIN soignant s  ON s.id = r.id_soignant
            WHERE 1 = 1
        ";

        $params = [];

        if ($this->statut) {
            $sql .= " AND r.statut = :statut";
            $params[':statut'] = $this->statut;
        }

        if ($this->soignant) {
            $sql .= " AND r.id_soignant = :id_soignant";
            $params[':id_soignant'] = $this->soignant;
        }

        if ($service) {
            $sql .= " AND p.service = :service";
            $params[':service'] = $service;
        }

        if ($this->date_retrait) {
            $sql .= " AND DATE(r.date_retrait_effective) = :date";
            $params[':date'] = $this->date_retrait;
        }

        $sql .= " ORDER BY r.date_retrait_effective DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            ':id_patient' => $this->patient_id,
            ':date_retrait' => $this->date_retrait,
            ':commentaire'  => $this->commentaires,
            ':id' => $this->id,
        ]);
    }


    public function confirmer()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
        UPDATE reservation 
        SET statut = 'Confirmée'
        WHERE id = :id
        AND statut = 'En attente' ");
        return $stmt->execute([':id' => $this->id]);
    }


    public function cloturee()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
        UPDATE reservation 
        SET statut = 'Clôturée'
        WHERE id = :id
        AND statut = 'Confirmée' ");
        return $stmt->execute([':id' => $this->id]);
    }


    public function validerRetrait()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE reservation 
            SET statut = 'Clôturée', date_retrait_effective = NOW() 
            WHERE id = :id AND statut = 'Confirmée'";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }

    public function cancel()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE reservation 
        SET statut = 'Annulée', is_archived = 1 
        WHERE id = :id AND statut != 'Annulée'";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }

    public function demanderRetourItem(int $articleReserveId): bool
    {
        $db = Database::getInstance()->getConnection();


        $stmtCheck = $db->prepare("
            SELECT ar.id
            FROM article_reserve ar
            JOIN reservation r ON r.id = ar.id_reservation
            WHERE ar.id = :articleReserveId
            AND   r.id = :reservationId
            AND   r.id_soignant = :soignantId
            AND   r.statut = 'Clôturée'
            AND   ar.est_retourne = 0
            AND   ar.retour_demande = 0
        ");

        $stmtCheck->execute([
            ':articleReserveId' => $articleReserveId,
            ':reservationId'    => $this->id,
            ':soignantId'       => $this->soignant,
        ]);

        if (!$stmtCheck->fetch()) {
            return false;
        }

        $stmt = $db->prepare("
            UPDATE article_reserve
            SET retour_demande = 1
            WHERE id = :articleReserveId
        ");
        return $stmt->execute([':articleReserveId' => $articleReserveId]);
    }


    public function activer()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE reservation SET is_archived = 0 WHERE id_soignant = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $this->soignant]);
    }

    public function desactiver()
    {
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE reservation SET is_archived = 1 WHERE id_soignant = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $this->soignant]);
    }


    public function updateAdmin()
    {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare('
        UPDATE reservation
        SET statut = :statut,
            date_retrait_effective = :date_retrait,
            commentaire = :commentaire
        WHERE id = :id
    ');
        return $stmt->execute([
            ':statut'        => $this->statut,
            ':date_retrait'  => $this->date_retrait,
            ':commentaire'   => $this->commentaires,
            ':id'            => $this->id,
        ]);
    }
    public function expireOld()
    {
        $db   = Database::getInstance()->getConnection();


        $stmt = $db->prepare("
        SELECT id FROM reservation
        WHERE statut = 'En attente'
          AND is_archived = 0
          AND date_reservation <= NOW() - INTERVAL 48 HOUR
    ");
        $stmt->execute();
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $expireesIds = [];

        foreach ($reservations as $r) {

            $items = $db->prepare("
            SELECT id, id_article_variante, quantite 
            FROM article_reserve 
            WHERE id_reservation = :id
        ");
            $items->execute([':id' => $r['id']]);
            $lignes = $items->fetchAll(PDO::FETCH_ASSOC);

            foreach ($lignes as $ligne) {
                $db->prepare("
                UPDATE article_variante 
                SET stock = stock + :quantite 
                WHERE id = :id
            ")->execute([
                    ':quantite' => $ligne['quantite'],
                    ':id' => $ligne['id_article_variante'],
                ]);
            }


            $db->prepare("
            UPDATE reservation 
            SET statut = 'Annulée', is_archived = 1 
            WHERE id = :id ")->execute([':id' => $r['id']]);

            $expireesIds[] = $r['id'];
        }

        $soignantStmt = $db->prepare("SELECT id_soignant FROM reservation WHERE id = :id");
        $soignantStmt->execute([':id' => $r['id']]);
        $idSoignant = $soignantStmt->fetchColumn();

        if ($idSoignant) {
            (new Notification(
                null,
                $idSoignant,
                'Réservation confirmée',
                'Réservation expirée',
                "Votre réservation #{$r['id']} a été annulée automatiquement (délai 48h dépassé)."
            ))->create();
        }

        return $expireesIds;
    }
}
