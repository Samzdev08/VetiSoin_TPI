<?php

/**
 * Fichier : Stats.php
 * Auteur  : Samuel Tido Kaze
 * Date    : 22.04.2026
 * Projet  : TPI VetiSoin
 * Role    : Statistiques pour le dashboard admin
 */

namespace App\Models;

use App\Outils\Database;
use PDO;

class Stats
{
    private $dateDebut;
    private $dateFin;

    public function __construct($dateDebut, $dateFin)
    {
        $this->dateDebut = $dateDebut;
        $this->dateFin   = $dateFin;
    }

    
    public function getNbReservations()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT COUNT(*) 
            FROM reservation
            WHERE DATE(date_reservation) BETWEEN :debut AND :fin
        ");
        $stmt->execute([':debut' => $this->dateDebut, ':fin' => $this->dateFin]);
        return (int)$stmt->fetchColumn();
    }

    
    public function getArticlesTop()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT a.nom, SUM(ar.quantite) AS total
            FROM article_reserve ar
            JOIN reservation r       ON r.id  = ar.id_reservation
            JOIN article_variante av ON av.id = ar.id_article_variante
            JOIN article a           ON a.id  = av.id_article
            WHERE DATE(r.date_reservation) BETWEEN :debut AND :fin
            GROUP BY a.id, a.nom
            ORDER BY total DESC
            LIMIT 5
        ");
        $stmt->execute([':debut' => $this->dateDebut, ':fin' => $this->dateFin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function getCategoriesTop()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT c.nom, SUM(ar.quantite) AS total
            FROM article_reserve ar
            JOIN reservation r       ON r.id  = ar.id_reservation
            JOIN article_variante av ON av.id = ar.id_article_variante
            JOIN article a           ON a.id  = av.id_article
            JOIN categorie c         ON c.id  = a.id_categorie
            WHERE DATE(r.date_reservation) BETWEEN :debut AND :fin
            GROUP BY c.id, c.nom
            ORDER BY total DESC
            LIMIT 5
        ");
        $stmt->execute([':debut' => $this->dateDebut, ':fin' => $this->dateFin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}