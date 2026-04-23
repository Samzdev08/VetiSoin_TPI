-- Fichier : seeds.sql
-- Auteur  : Samuel Tido Kaze
-- Date    : 22.04.2026
-- Projet  : TPI VetiSoin
-- Role    : Jeu de donnees initial


-- Fichier : seeds.sql
-- Auteur  : Samuel Tido Kaze
-- Date    : 23.04.2026
-- Projet  : TPI VetiSoin
-- Role    : Donnees de test pour la base de donnees
--
-- NOTE : Les mots de passe sont des placeholders bcrypt.
-- A remplacer par de vrais hashs generes avec password_hash() en PHP
-- avant toute utilisation reelle.

USE vetisoin;




-- =====================================================================
-- SOIGNANTS
-- =====================================================================
INSERT INTO soignant (nom, prenom, email, mot_de_passe, service, telephone, role, statut) VALUES
('Admin',    'Principal', 'admin@vetisoin.ch',            '$2y$10$PLACEHOLDER_A_REMPLACER_PAR_HASH_BCRYPT_REEL_____', 'Urgences',         '41 22 123 45 67', 'Administrateur', 'Actif'),
('Dupont',   'Marie',     'marie.dupont@vetisoin.ch',     '$2y$10$PLACEHOLDER_A_REMPLACER_PAR_HASH_BCRYPT_REEL_____', 'Urgences',         '41 22 234 56 78', 'Soignant',       'Actif'),
('Martin',   'Jean',      'jean.martin@vetisoin.ch',      '$2y$10$PLACEHOLDER_A_REMPLACER_PAR_HASH_BCRYPT_REEL_____', 'Chirurgie',        '41 22 345 67 89', 'Soignant',       'Actif'),
('Favre',    'Sophie',    'sophie.favre@vetisoin.ch',     '$2y$10$PLACEHOLDER_A_REMPLACER_PAR_HASH_BCRYPT_REEL_____', 'Médecine interne', '41 22 456 78 90', 'Soignant',       'Actif'),
('Rochat',   'Pierre',    'pierre.rochat@vetisoin.ch',    '$2y$10$PLACEHOLDER_A_REMPLACER_PAR_HASH_BCRYPT_REEL_____', 'Chirurgie',         NULL,              'Soignant',       'Actif'),
('Schneider','Laura',    'laura.schneider@vetisoin.ch',   '$2y$10$PLACEHOLDER_A_REMPLACER_PAR_HASH_BCRYPT_REEL_____', 'Médecine interne', '41 22 567 89 01', 'Soignant',       'Inactif');


-- =====================================================================
-- PATIENTS
-- =====================================================================
INSERT INTO patient (nom, prenom, date_naissance, genre, numero_dossier, service, chambre, statut) VALUES
('Bernard',  'Luc',     '1952-03-14', 'Homme', 'DOS-2026-0001', 'Médecine interne', '102', 'Hospitalisé'),
('Rey',      'Anne',    '1978-11-02', 'Femme', 'DOS-2026-0002', 'Chirurgie',        '205', 'Hospitalisé'),
('Meier',    'Thomas',  '1965-07-21', 'Homme', 'DOS-2026-0003', 'Urgences',         '001', 'Hospitalisé'),
('Roulin',   'Camille', '1990-01-09', 'Femme', 'DOS-2026-0004', 'Chirurgie',        '207', 'Hospitalisé'),
('Perrin',   'Jacques', '1948-05-30', 'Homme', 'DOS-2026-0005', 'Médecine interne', '110', 'Sorti'),
('Keller',   'Eva',     '1985-09-17', 'Femme', 'DOS-2026-0006', 'Urgences',         '003', 'Hospitalisé'),
('Berger',   'Paul',    '1971-12-25', 'Homme', 'DOS-2026-0007', 'Chirurgie',        '210', 'Sorti');


-- =====================================================================
-- CATEGORIES
-- =====================================================================
INSERT INTO categorie (nom, description) VALUES
('Blouses',            'Blouses medicales pour le personnel soignant'),
('Pantalons',          'Pantalons medicaux de toutes coupes'),
('Tuniques',           'Tuniques et hauts medicaux'),
('Casaques',           'Casaques et tenues de bloc operatoire'),
('Chaussures',         'Sabots et chaussures de travail hospitalier'),
('Coiffes',            'Calots, charlottes et coiffes de protection'),
('Vestes & Polaires',  'Vestes polaires et sur-vestes');


-- =====================================================================
-- ARTICLES
-- (On suppose que les ID categories sont : 1=Blouses, 2=Pantalons,
--  3=Tuniques, 4=Casaques, 5=Chaussures, 6=Coiffes, 7=Vestes)
-- =====================================================================
INSERT INTO article (id_categorie, nom, genre, matiere, marque) VALUES
-- Blouses (1)
(1, 'Blouse medicale manches longues',  'Mixte',  'Coton/Polyester',   'Clinic+'),
(1, 'Blouse medicale manches courtes',  'Mixte',  'Polyester',         'MediaWear'),
(1, 'Blouse cintree medecin',           'Femme',  'Coton',             'Clinic+'),

-- Pantalons (2)
(2, 'Pantalon medical classique',       'Mixte',  'Coton/Polyester',   'ProMed'),
(2, 'Pantalon jogger medical',          'Mixte',  'Polyester elastique','MediaWear'),

-- Tuniques (3)
(3, 'Tunique col V',                    'Mixte',  'Coton/Polyester',   'ProMed'),
(3, 'Tunique col rond femme',           'Femme',  'Coton',             'Clinic+'),
(3, 'Tunique homme col V',              'Homme',  'Coton/Polyester',   'ProMed'),

-- Casaques (4)
(4, 'Casaque chirurgicale',             'Mixte',  'Polyester',         'SteriLine'),

-- Chaussures (5)
(5, 'Sabot medical antiderapant',       'Mixte',  'EVA',               'ClogMed'),

-- Coiffes (6)
(6, 'Calot en coton reutilisable',      'Mixte',  'Coton',             'Clinic+'),
(6, 'Charlotte jetable',                'Mixte',  'Non-tisse',         'SteriLine'),

-- Vestes (7)
(7, 'Veste polaire zippee',             'Mixte',  'Polaire',           'ProMed');


-- =====================================================================
-- ARTICLE_VARIANTE  (tailles x couleurs x stock)
-- ID articles: 1..13 dans l'ordre ci-dessus
-- =====================================================================

-- Article 1 : Blouse manches longues
INSERT INTO article_variante (id_article, taille, couleur, photo, stock) VALUES
(1, 'S',   'Blanc',       'img/blouse_ml_blanc_s.jpg',   12),
(1, 'M',   'Blanc',       'img/blouse_ml_blanc_m.jpg',   25),
(1, 'L',   'Blanc',       'img/blouse_ml_blanc_l.jpg',   18),
(1, 'XL',  'Blanc',       'img/blouse_ml_blanc_xl.jpg',   8),
(1, 'M',   'Bleu ciel',   'img/blouse_ml_bleu_m.jpg',    10),
(1, 'L',   'Bleu ciel',   'img/blouse_ml_bleu_l.jpg',     6);

-- Article 2 : Blouse manches courtes
INSERT INTO article_variante (id_article, taille, couleur, photo, stock) VALUES
(2, 'S',   'Blanc',       'img/blouse_mc_blanc_s.jpg',   15),
(2, 'M',   'Blanc',       'img/blouse_mc_blanc_m.jpg',   22),
(2, 'L',   'Blanc',       'img/blouse_mc_blanc_l.jpg',   14),
(2, 'XL',  'Blanc',       'img/blouse_mc_blanc_xl.jpg',   5);

-- Article 3 : Blouse cintree femme
INSERT INTO article_variante (id_article, taille, couleur, photo, stock) VALUES
(3, 'XS',  'Blanc',       'img/blouse_cint_blanc_xs.jpg', 8),
(3, 'S',   'Blanc',       'img/blouse_cint_blanc_s.jpg', 14),
(3, 'M',   'Blanc',       'img/blouse_cint_blanc_m.jpg', 20),
(3, 'L',   'Blanc',       'img/blouse_cint_blanc_l.jpg',  9),
(3, 'S',   'Rose',        'img/blouse_cint_rose_s.jpg',   4),
(3, 'M',   'Rose',        'img/blouse_cint_rose_m.jpg',   6);

-- Article 4 : Pantalon classique
INSERT INTO article_variante (id_article, taille, couleur, photo, stock) VALUES
(4, 'S',   'Blanc',       'img/pantalon_class_blanc_s.jpg',  10),
(4, 'M',   'Blanc',       'img/pantalon_class_blanc_m.jpg',  28),
(4, 'L',   'Blanc',       'img/pantalon_class_blanc_l.jpg',  22),
(4, 'XL',  'Blanc',       'img/pantalon_class_blanc_xl.jpg', 11),
(4, 'M',   'Bleu marine', 'img/pantalon_class_marine_m.jpg', 15),
(4, 'L',   'Bleu marine', 'img/pantalon_class_marine_l.jpg', 12);

-- Article 5 : Pantalon jogger
INSERT INTO article_variante (id_article, taille, couleur, photo, stock) VALUES
(5, 'S',   'Bleu marine', 'img/jogger_marine_s.jpg',  7),
(5, 'M',   'Bleu marine', 'img/jogger_marine_m.jpg', 16),
(5, 'L',   'Bleu marine', 'img/jogger_marine_l.jpg', 12),
(5, 'XL',  'Bleu marine', 'img/jogger_marine_xl.jpg', 4);

-- Article 6 : Tunique col V
INSERT INTO article_variante (id_article, taille, couleur, photo, stock) VALUES
(6, 'S',   'Vert hopital','img/tunique_v_vert_s.jpg', 18),
(6, 'M',   'Vert hopital','img/tunique_v_vert_m.jpg', 30),
(6, 'L',   'Vert hopital','img/tunique_v_vert_l.jpg', 24),
(6, 'XL',  'Vert hopital','img/tunique_v_vert_xl.jpg',10),
(6, 'M',   'Bleu ciel',   'img/tunique_v_bleu_m.jpg', 12),
(6, 'L',   'Bleu ciel',   'img/tunique_v_bleu_l.jpg',  9);

-- Article 7 : Tunique col rond femme
INSERT INTO article_variante (id_article, taille, couleur, photo, stock) VALUES
(7, 'XS',  'Blanc',       'img/tunique_r_blanc_xs.jpg', 6),
(7, 'S',   'Blanc',       'img/tunique_r_blanc_s.jpg', 16),
(7, 'M',   'Blanc',       'img/tunique_r_blanc_m.jpg', 20),
(7, 'L',   'Blanc',       'img/tunique_r_blanc_l.jpg',  8),
(7, 'S',   'Violet',      'img/tunique_r_violet_s.jpg', 5),
(7, 'M',   'Violet',      'img/tunique_r_violet_m.jpg', 7);

-- Article 8 : Tunique homme col V
INSERT INTO article_variante (id_article, taille, couleur, photo, stock) VALUES
(8, 'M',   'Bleu marine', 'img/tunique_h_marine_m.jpg', 14),
(8, 'L',   'Bleu marine', 'img/tunique_h_marine_l.jpg', 18),
(8, 'XL',  'Bleu marine', 'img/tunique_h_marine_xl.jpg',10),
(8, 'XXL', 'Bleu marine', 'img/tunique_h_marine_xxl.jpg',3);

-- Article 9 : Casaque chirurgicale
INSERT INTO article_variante (id_article, taille, couleur, photo, stock) VALUES
(9, 'S',   'Vert hopital','img/casaque_vert_s.jpg',  20),
(9, 'M',   'Vert hopital','img/casaque_vert_m.jpg',  35),
(9, 'L',   'Vert hopital','img/casaque_vert_l.jpg',  28),
(9, 'XL',  'Vert hopital','img/casaque_vert_xl.jpg', 12);

-- Article 10 : Sabot medical
INSERT INTO article_variante (id_article, taille, couleur, photo, stock) VALUES
(10, '36', 'Blanc',       'img/sabot_blanc_36.jpg', 4),
(10, '38', 'Blanc',       'img/sabot_blanc_38.jpg', 8),
(10, '40', 'Blanc',       'img/sabot_blanc_40.jpg',10),
(10, '42', 'Blanc',       'img/sabot_blanc_42.jpg', 7),
(10, '44', 'Blanc',       'img/sabot_blanc_44.jpg', 3);

-- Article 11 : Calot coton
INSERT INTO article_variante (id_article, taille, couleur, photo, stock) VALUES
(11, 'Unique', 'Bleu ciel', 'img/calot_bleu.jpg',  30),
(11, 'Unique', 'Vert hopital','img/calot_vert.jpg',25),
(11, 'Unique', 'Motif',     'img/calot_motif.jpg', 12);

-- Article 12 : Charlotte jetable (stock bas volontairement)
INSERT INTO article_variante (id_article, taille, couleur, photo, stock) VALUES
(12, 'Unique', 'Blanc', 'img/charlotte_blanc.jpg', 2);

-- Article 13 : Veste polaire
INSERT INTO article_variante (id_article, taille, couleur, photo, stock) VALUES
(13, 'S',   'Bleu marine', 'img/polaire_marine_s.jpg',  5),
(13, 'M',   'Bleu marine', 'img/polaire_marine_m.jpg', 11),
(13, 'L',   'Bleu marine', 'img/polaire_marine_l.jpg',  9),
(13, 'XL',  'Bleu marine', 'img/polaire_marine_xl.jpg', 4);


-- =====================================================================
-- RESERVATIONS
-- =====================================================================
INSERT INTO reservation (id_soignant, id_patient, date_reservation, date_retrait_effective, statut, commentaire) VALUES
(2, 1, '2026-04-15 09:12:00', '2026-04-15 14:30:00', 'Clôturée',  'Retrait effectue au vestiaire principal'),
(3, 2, '2026-04-18 10:05:00', NULL,                  'Confirmée', 'Retrait prevu le 24.04'),
(4, 3, '2026-04-20 08:40:00', NULL,                  'En attente',NULL),
(2, 6, '2026-04-22 11:15:00', NULL,                  'Confirmée', 'Urgence - reservation rapide'),
(5, 4, '2026-04-10 13:00:00', NULL,                  'Annulée',   'Patient transfere dans un autre service'),
(3, 7, '2026-04-08 09:00:00', '2026-04-08 16:00:00', 'Clôturée',  NULL);


-- =====================================================================
-- ARTICLE_RESERVE  (contenu des reservations)
-- =====================================================================
-- Reservation 1 (clôturée, tout retourne)
INSERT INTO article_reserve (id_reservation, id_article_variante, quantite, est_retourne, date_retour) VALUES
(1,  2, 2, TRUE, '2026-04-17 10:00:00'),   -- Blouse ML Blanc M
(1, 17, 1, TRUE, '2026-04-17 10:00:00');   -- Pantalon classique Blanc M

-- Reservation 2 (confirmee, non retirée)
INSERT INTO article_reserve (id_reservation, id_article_variante, quantite) VALUES
(2, 25, 1),   -- Tunique col V vert M
(2, 18, 1);   -- Pantalon classique Blanc L

-- Reservation 3 (en attente)
INSERT INTO article_reserve (id_reservation, id_article_variante, quantite) VALUES
(3, 40, 1),   -- Casaque vert M
(3, 44, 1);   -- Sabot 40 blanc

-- Reservation 4 (urgence)
INSERT INTO article_reserve (id_reservation, id_article_variante, quantite) VALUES
(4,  7, 1),   -- Blouse MC Blanc S
(4, 48, 1);   -- Calot bleu

-- Reservation 6 (cloturee avec retour partiel)
INSERT INTO article_reserve (id_reservation, id_article_variante, quantite, est_retourne, date_retour) VALUES
(6, 41, 1, TRUE,  '2026-04-09 09:00:00'),
(6, 51, 1, FALSE, NULL);   -- Polaire pas encore retournee


-- =====================================================================
-- RENDEZ_VOUS  (1 rdv par reservation active)
-- =====================================================================
INSERT INTO rendez_vous (id_reservation, date_rdv, heure_rdv, lieu, statut) VALUES
(1, '2026-04-15', '14:30:00', 'Vestiaire principal', 'Réalisé'),
(2, '2026-04-24', '10:00:00', 'Vestiaire principal', 'Planifié'),
(3, '2026-04-25', '09:30:00', 'Secrétariat',         'Planifié'),
(4, '2026-04-23', '16:00:00', 'Vestiaire principal', 'Planifié'),
(6, '2026-04-08', '16:00:00', 'Vestiaire principal', 'Réalisé');


-- =====================================================================
-- NOTIFICATIONS
-- =====================================================================
INSERT INTO notification (id_soignant, type, titre, message, lu) VALUES
(2, 'Réservation confirmée', 'Votre reservation #2 est confirmee',
    'Votre reservation du 18.04 a ete confirmee. Retrait prevu le 24.04 a 10h00 au vestiaire principal.', TRUE),
(3, 'Rappel rendez-vous', 'Rappel : rendez-vous demain',
    'N oubliez pas votre rendez-vous du 25.04 a 09h30 au secretariat.', FALSE),
(4, 'Retour attendu', 'Retour d articles en attente',
    'Vous avez encore 1 article non restitue suite a la reservation #4.', FALSE),
(1, 'Stock bas', 'Alerte stock : Charlotte jetable',
    'Le stock de "Charlotte jetable - Blanc" est descendu a 2 unites.', FALSE),
(2, 'Réservation confirmée', 'Reservation urgente confirmee',
    'Reservation #4 creee pour le patient Eva Keller - confirmee automatiquement.', TRUE);