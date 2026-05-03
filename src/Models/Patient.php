<?php

/**
 * Fichier : Patient.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Entite Patient
 */

namespace App\Models;

use App\Outils\Database;
use PDO;

class Patient
{
    public $id;
    public $nom;
    public $prenom;
    public $date_naissance;
    public $genre;
    public $numeroDossier;
    public $service;
    public $chambre;
    public $statut;

    public function __construct($id, $nom, $prenom, $date_naissance, $genre, $numeroDossier, $service, $chambre, $statut)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->date_naissance = $date_naissance;
        $this->genre = $genre;
        $this->numeroDossier = $numeroDossier;
        $this->service = $service;
        $this->chambre = $chambre;
        $this->statut = $statut;
    }

    public function getAll()
    {

        $params = [];

        $db = Database::getInstance()->getConnection();

        $sql = "SELECT DISTINCT
            p.id,
            p.nom, 
            p.prenom, 
            p.date_naissance, 
            p.genre, 
            p.numero_dossier, 
            p.service, 
            p.chambre, 
            p.statut 
        FROM patient p
        LEFT JOIN reservation r 
            ON p.id = r.id_patient
        WHERE 1 = 1";


        if ($this->nom) {

            $sql .= ' AND (nom LIKE :nom1 OR prenom LIKE :nom OR numero_dossier LIKE :nom2)';
            $params[':nom1'] = "%$this->nom%";
            $params[':nom'] = "%$this->nom%";
            $params[':nom2'] = "%$this->nom%";  
        }
        
        if ($this->service) {
            $sql .= ' AND service = :service';
            $params[':service'] = $this->service;
        }


        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById()
    {


        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
        SELECT 
            p.id, 
            p.nom, 
            p.prenom, 
            p.date_naissance, 
            p.genre,
            p.service,
            p.chambre,
            p.numero_dossier, 
            p.statut,

            s.nom AS soignant_nom,
            s.prenom AS soignant_prenom,
            r.date_reservation, 
            r.statut AS reservation_statut, 
            r.is_archived AS reservation_archived, 
            r.commentaire

            FROM patient p  
            INNER JOIN reservation r 
                ON p.id = r.id_patient
            INNER JOIN soignant s 
                ON r.id_soignant = s.id
            WHERE p.id = ?
            ORDER BY r.date_reservation DESC
        ");
        $stmt->execute([$this->id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $patient = [
            "id" => $rows[0]['id'],
            "nom" => $rows[0]['nom'],
            "prenom" => $rows[0]['prenom'],
            "date_naissance" => $rows[0]['date_naissance'],
            "genre" => $rows[0]['genre'],
            "service" => $rows[0]['service'],
            "chambre" => $rows[0]['chambre'],
            "numero_dossier" => $rows[0]['numero_dossier'],
            "statut" => $rows[0]['statut'],
            "reservations" => []
        ];

        foreach ($rows as $row) {
            $patient['reservations'][] = [
                "soignant_nom" => $row['soignant_nom'],
                "soignant_prenom" => $row['soignant_prenom'],
                "date_reservation" => $row['date_reservation'],
                "reservation_statut" => $row['reservation_statut'],
                "reservation_archived" => $row['reservation_archived'],
                "reservation_commentaire" => $row['commentaire']
            ];
        }
        return $patient;
    }

    public function create()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('INSERT INTO patient (nom, prenom, date_naissance, genre, numero_dossier, service, chambre, statut) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([
            $this->nom,
            $this->prenom,
            $this->date_naissance,
            $this->genre,
            $this->numeroDossier,
            $this->service,
            $this->chambre,
            'Hospitalisé'
        ]);
    }

    public function update()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('UPDATE patient SET nom = ?, prenom = ?, date_naissance = ?, genre = ?, numero_dossier = ?, service = ?, chambre = ?, statut = ? WHERE id = ?');
        return $stmt->execute([
            $this->nom,
            $this->prenom,
            $this->date_naissance,
            $this->genre,
            $this->numeroDossier,
            $this->service,
            $this->chambre,
            $this->statut,
            $this->id
        ]);
    }

    public function delete()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('DELETE FROM patient WHERE id = ?');
        return $stmt->execute([$this->id]);
    }

    public function hasReservations()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT id FROM reservation WHERE id_patient = ?');
        $stmt->execute([$this->id]);
        return $stmt->fetch() !== false;
    }

    public function isNumeroDossierUnique()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT id FROM patient WHERE numero_dossier = ? AND id != ?');
        $stmt->execute([$this->numeroDossier, $this->id]);
        return $stmt->fetch() === false;
    }

   
}
