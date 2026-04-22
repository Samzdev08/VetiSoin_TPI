-- Fichier : schema.sql
-- Auteur  : Samuel Tido Kaze
-- Date    : 22.04.2026
-- Projet  : TPI VetiSoin
-- Role    : Structure de la base de donnees


DROP DATABASE IF EXISTS vetisoin;
CREATE DATABASE vetisoin
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE vetisoin;


-- =====================================================================
-- 1. SOIGNANT
-- =====================================================================
CREATE TABLE soignant (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(80)  NOT NULL,
    prenom          VARCHAR(80)  NOT NULL,
    email           VARCHAR(150) NOT NULL,
    mot_de_passe    VARCHAR(255) NOT NULL, 
    service         ENUM('Urgences', 'Chirurgie', 'Médecine interne') NOT NULL,
    telephone       VARCHAR(20)  NULL,
    role            ENUM('Soignant', 'Administrateur') NOT NULL DEFAULT 'Soignant',
    statut          ENUM('Actif', 'Inactif') NOT NULL DEFAULT 'Actif',
    date_creation   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uk_soignant_email UNIQUE (email)
) ENGINE=InnoDB;


-- =====================================================================
-- 2. PATIENT
-- =====================================================================
CREATE TABLE patient (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom              VARCHAR(80) NOT NULL,
    prenom           VARCHAR(80) NOT NULL,
    date_naissance   DATE        NOT NULL,
    genre            ENUM('Homme', 'Femme') NOT NULL,
    numero_dossier   VARCHAR(30) NOT NULL,
    service          ENUM('Urgences', 'Chirurgie', 'Médecine interne') NOT NULL,
    chambre          VARCHAR(10) NOT NULL,
    statut           ENUM('Hospitalisé', 'Sorti') NOT NULL DEFAULT 'Hospitalisé',
    date_creation    DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uk_patient_numero_dossier UNIQUE (numero_dossier)
) ENGINE=InnoDB;


-- =====================================================================
-- 3. CATEGORIE
-- =====================================================================
CREATE TABLE categorie (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(80) NOT NULL,
    description TEXT        NULL,

    CONSTRAINT uk_categorie_nom UNIQUE (nom)
) ENGINE=InnoDB;


-- =====================================================================
-- 4. ARTICLE 
-- =====================================================================
CREATE TABLE article (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_categorie   INT UNSIGNED NOT NULL,
    nom            VARCHAR(120) NOT NULL,
    genre          ENUM('Homme', 'Femme', 'Mixte') NOT NULL,
    matiere        VARCHAR(80)  NULL,
    marque         VARCHAR(80)  NULL,
    date_creation  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_article_categorie
        FOREIGN KEY (id_categorie) REFERENCES categorie(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;


-- =====================================================================
-- 5. ARTICLE_VARIANTE  (déclinaison couleur + taille + stock)
-- =====================================================================
CREATE TABLE article_variante (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_article  INT UNSIGNED NOT NULL,
    taille      VARCHAR(10)  NOT NULL,
    couleur     VARCHAR(40)  NOT NULL,
    photo       VARCHAR(255) NULL,    
    stock       INT UNSIGNED NOT NULL DEFAULT 0, 

    CONSTRAINT fk_variante_article
        FOREIGN KEY (id_article) REFERENCES article(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT uk_variante_article_taille_couleur
        UNIQUE (id_article, taille, couleur)
) ENGINE=InnoDB;


-- =====================================================================
-- 6. RESERVATION

-- =====================================================================
CREATE TABLE reservation (
    id                       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_soignant              INT UNSIGNED NOT NULL,
    id_patient               INT UNSIGNED NOT NULL,
    date_reservation         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_retrait_effective   DATETIME     NULL, 
    statut                   ENUM('En attente', 'Confirmée', 'Clôturée', 'Annulée')
                             NOT NULL DEFAULT 'En attente',
    is_archived              BOOLEAN      NOT NULL DEFAULT FALSE,
    commentaire              TEXT         NULL,

    CONSTRAINT fk_reservation_soignant
        FOREIGN KEY (id_soignant) REFERENCES soignant(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_reservation_patient
        FOREIGN KEY (id_patient) REFERENCES patient(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;


-- =====================================================================
-- 7. ARTICLE_RESERVE  (table d'association Réservation <-> Variante)
-- =====================================================================
CREATE TABLE article_reserve (
    id                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_reservation        INT UNSIGNED NOT NULL,
    id_article_variante   INT UNSIGNED NOT NULL,
    quantite              INT UNSIGNED NOT NULL,
    est_retourne          BOOLEAN      NOT NULL DEFAULT FALSE,
    date_retour           DATETIME     NULL,

    CONSTRAINT fk_ar_reservation
        FOREIGN KEY (id_reservation) REFERENCES reservation(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_ar_variante
        FOREIGN KEY (id_article_variante) REFERENCES article_variante(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT uk_ar_reservation_variante
        UNIQUE (id_reservation, id_article_variante)
) ENGINE=InnoDB;


-- =====================================================================
-- 8. RENDEZ_VOUS 
-- =====================================================================
CREATE TABLE rendez_vous (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_reservation  INT UNSIGNED NOT NULL,
    date_rdv        DATE         NOT NULL,
    heure_rdv       TIME         NOT NULL,
    lieu            ENUM('Vestiaire principal', 'Secrétariat') NOT NULL,
    statut          ENUM('Planifié', 'Réalisé', 'Annulé', 'Non honoré')
                    NOT NULL DEFAULT 'Planifié',

    CONSTRAINT fk_rdv_reservation
        FOREIGN KEY (id_reservation) REFERENCES reservation(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT uk_rdv_reservation UNIQUE (id_reservation),

    CONSTRAINT uk_rdv_creneau UNIQUE (date_rdv, heure_rdv, lieu)
    
) ENGINE=InnoDB;


-- =====================================================================
-- 9. NOTIFICATION
-- =====================================================================
CREATE TABLE notification (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_soignant  INT UNSIGNED NOT NULL,
    type         ENUM('Réservation confirmée', 'Rappel rendez-vous',
                      'Retour attendu', 'Stock bas') NOT NULL,
    titre        VARCHAR(150) NOT NULL,
    message      TEXT         NOT NULL,
    date_envoi   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    lu           BOOLEAN      NOT NULL DEFAULT FALSE,

    CONSTRAINT fk_notification_soignant
        FOREIGN KEY (id_soignant) REFERENCES soignant(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


