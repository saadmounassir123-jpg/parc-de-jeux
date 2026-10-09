-- Base de données : parc_jeux (à importer dans phpMyAdmin)
CREATE DATABASE IF NOT EXISTS parc_jeux CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE parc_jeux;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS maintenance, depense, recette, reservation_jeu, reservation, billet, achat,
                     type_billet, utilisateur, jeu, client, responsable;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE responsable (
  id_responsable INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(50) NOT NULL,
  prenom VARCHAR(50) NOT NULL,
  telephone VARCHAR(20),
  email VARCHAR(100),
  fonction VARCHAR(80),
  date_embauche DATE,
  statut ENUM('actif','inactif') NOT NULL DEFAULT 'actif'
);

CREATE TABLE client (
  id_client INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(50) NOT NULL,
  prenom VARCHAR(50) NOT NULL,
  telephone VARCHAR(20),
  email VARCHAR(100),
  type_client ENUM('individuel','famille','groupe') NOT NULL DEFAULT 'individuel'
);

CREATE TABLE utilisateur (
  id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(50) NOT NULL,
  prenom VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  mot_de_passe VARCHAR(255) NOT NULL,
  role ENUM('admin','responsable','agent','client') NOT NULL,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  id_responsable INT NULL,
  id_client INT NULL,
  FOREIGN KEY (id_responsable) REFERENCES responsable(id_responsable),
  FOREIGN KEY (id_client) REFERENCES client(id_client)
);

CREATE TABLE jeu (
  id_jeu INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(80) NOT NULL,
  description TEXT,
  categorie VARCHAR(50) NOT NULL,
  age_minimum INT NOT NULL DEFAULT 0,
  capacite INT NOT NULL,
  duree_session INT NOT NULL,
  tarif DECIMAL(8,2) NOT NULL,
  etat ENUM('disponible','en_fonctionnement','en_maintenance','hors_service','ferme_temporairement') NOT NULL DEFAULT 'disponible',
  date_mise_service DATE,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  id_responsable INT NULL,
  FOREIGN KEY (id_responsable) REFERENCES responsable(id_responsable)
);

CREATE TABLE type_billet (
  id_type_billet INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(50) NOT NULL,
  tarif DECIMAL(8,2) NOT NULL,
  duree_validite INT NOT NULL DEFAULT 1   -- en jours
);

CREATE TABLE achat (
  id_achat INT AUTO_INCREMENT PRIMARY KEY,
  numero_commande VARCHAR(30) NOT NULL UNIQUE,
  date_achat DATETIME NOT NULL,
  montant_total DECIMAL(10,2) NOT NULL,
  mode_paiement ENUM('especes','carte') NOT NULL,
  statut_paiement ENUM('en_attente','paye') NOT NULL DEFAULT 'en_attente',
  id_client INT NOT NULL,
  FOREIGN KEY (id_client) REFERENCES client(id_client)
);

CREATE TABLE billet (
  id_billet INT AUTO_INCREMENT PRIMARY KEY,
  code_unique VARCHAR(30) NOT NULL UNIQUE,
  prix DECIMAL(8,2) NOT NULL,
  statut ENUM('valide','utilise') NOT NULL DEFAULT 'valide',
  date_utilisation DATETIME NULL,
  id_achat INT NOT NULL,
  id_type_billet INT NOT NULL,
  FOREIGN KEY (id_achat) REFERENCES achat(id_achat),
  FOREIGN KEY (id_type_billet) REFERENCES type_billet(id_type_billet)
);

CREATE TABLE reservation (
  id_reservation INT AUTO_INCREMENT PRIMARY KEY,
  numero_reservation VARCHAR(30) NOT NULL UNIQUE,
  date_creation DATETIME NOT NULL,
  date_visite DATE NOT NULL,
  nombre_personnes INT NOT NULL,
  montant DECIMAL(10,2) NOT NULL,
  statut ENUM('en_attente','confirmee','payee','utilisee','annulee') NOT NULL DEFAULT 'en_attente',
  mode_paiement ENUM('especes','carte') NULL,
  id_client INT NOT NULL,
  FOREIGN KEY (id_client) REFERENCES client(id_client)
);

CREATE TABLE reservation_jeu (
  id_reservation INT NOT NULL,
  id_jeu INT NOT NULL,
  PRIMARY KEY (id_reservation, id_jeu),
  FOREIGN KEY (id_reservation) REFERENCES reservation(id_reservation),
  FOREIGN KEY (id_jeu) REFERENCES jeu(id_jeu)
);

CREATE TABLE recette (
  id_recette INT AUTO_INCREMENT PRIMARY KEY,
  date_recette DATETIME NOT NULL,
  montant DECIMAL(10,2) NOT NULL,
  source ENUM('billet','reservation','abonnement','autre') NOT NULL,
  id_achat INT NULL,
  id_reservation INT NULL,
  FOREIGN KEY (id_achat) REFERENCES achat(id_achat),
  FOREIGN KEY (id_reservation) REFERENCES reservation(id_reservation)
);

CREATE TABLE depense (
  id_depense INT AUTO_INCREMENT PRIMARY KEY,
  categorie VARCHAR(50) NOT NULL,
  description VARCHAR(255) NOT NULL,
  montant DECIMAL(10,2) NOT NULL,
  date_depense DATE NOT NULL,
  type ENUM('jeu','general') NOT NULL,
  justificatif VARCHAR(100) NULL,
  id_jeu INT NULL,
  id_utilisateur INT NOT NULL,
  FOREIGN KEY (id_jeu) REFERENCES jeu(id_jeu),
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE maintenance (
  id_maintenance INT AUTO_INCREMENT PRIMARY KEY,
  type_intervention VARCHAR(80) NOT NULL,
  description TEXT,
  date_intervention DATE NOT NULL,
  cout DECIMAL(10,2) NOT NULL DEFAULT 0,
  etat ENUM('signalee','planifiee','en_cours','terminee') NOT NULL DEFAULT 'planifiee',
  date_prochaine DATE NULL,
  id_jeu INT NOT NULL,
  id_responsable INT NULL,
  FOREIGN KEY (id_jeu) REFERENCES jeu(id_jeu),
  FOREIGN KEY (id_responsable) REFERENCES responsable(id_responsable)
);

-- ===================== DONNÉES FICTIVES =====================
INSERT INTO responsable (nom, prenom, telephone, email, fonction, date_embauche, statut) VALUES
('Alaoui','Karim','0600000001','karim@parcjeux.test','Chef d''équipe','2023-03-01','actif'),
('Benali','Salma','0600000002','salma@parcjeux.test','Technicienne','2024-01-15','actif');

INSERT INTO client (nom, prenom, telephone, email, type_client) VALUES
('Idrissi','Youssef','0610000001','youssef@exemple.test','famille'),
('Fassi','Nadia','0610000002','nadia@exemple.test','individuel'),
('Groupe','École Démo','0610000003','ecole@exemple.test','groupe');

-- Mot de passe de tous les comptes de démonstration : password
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role, id_responsable, id_client) VALUES
('Admin','Parc','admin@parcjeux.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin',NULL,NULL),
('Alaoui','Karim','karim@parcjeux.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','responsable',1,NULL),
('Agent','Accueil','agent@parcjeux.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','agent',NULL,NULL),
('Idrissi','Youssef','youssef@exemple.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','client',NULL,1);

INSERT INTO jeu (nom, description, categorie, age_minimum, capacite, duree_session, tarif, etat, date_mise_service, id_responsable) VALUES
('Grande Roue','Vue panoramique sur le parc.','Manège',0,40,10,30.00,'disponible','2022-05-01',1),
('Montagnes Russes','Parcours à sensations fortes.','Sensations',12,24,3,50.00,'en_fonctionnement','2023-06-15',1),
('Auto-tamponneuses','Amusement en famille.','Famille',6,20,5,25.00,'disponible','2021-04-10',2),
('Carrousel','Manège classique pour enfants.','Enfants',3,16,4,15.00,'en_maintenance','2020-03-20',2),
('Piscine à balles','Espace de jeux pour les tout-petits.','Enfants',2,30,30,20.00,'disponible','2024-02-01',NULL);

INSERT INTO type_billet (nom, tarif, duree_validite) VALUES
('Enfant',40.00,1),('Adulte',80.00,1),('Famille',250.00,1),('Pass journée',120.00,1),('Abonnement',600.00,30);

-- Achat 1 (il y a 3 jours, billets expirés sauf abonnement) : 2 adultes + 2 enfants = 240 DH
-- Achat 2 (aujourd'hui) : 1 pass journée = 120 DH -> billet BIL-DEMO0005 valide pour tester la vérification
INSERT INTO achat (numero_commande, date_achat, montant_total, mode_paiement, statut_paiement, id_client) VALUES
('CMD-DEMO-0001', DATE_SUB(NOW(), INTERVAL 3 DAY), 240.00, 'carte', 'paye', 1),
('CMD-DEMO-0002', NOW(), 120.00, 'especes', 'paye', 2);
INSERT INTO billet (code_unique, prix, statut, date_utilisation, id_achat, id_type_billet) VALUES
('BIL-DEMO0001',80.00,'utilise',DATE_SUB(NOW(), INTERVAL 3 DAY),1,2),
('BIL-DEMO0002',80.00,'valide',NULL,1,2),
('BIL-DEMO0003',40.00,'valide',NULL,1,1),
('BIL-DEMO0004',40.00,'valide',NULL,1,1),
('BIL-DEMO0005',120.00,'valide',NULL,2,4);

-- Réservations : 1 payée (groupe, 20 pers., jeux 1 et 2 = 1600 DH), 1 en attente
INSERT INTO reservation (numero_reservation, date_creation, date_visite, nombre_personnes, montant, statut, mode_paiement, id_client) VALUES
('RES-DEMO-0001', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 2 DAY), 20, 1600.00, 'payee', 'carte', 3),
('RES-DEMO-0002', NOW(), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 4, 200.00, 'en_attente', NULL, 1);
INSERT INTO reservation_jeu (id_reservation, id_jeu) VALUES (1,1),(1,2),(2,2);

INSERT INTO recette (date_recette, montant, source, id_achat, id_reservation) VALUES
(DATE_SUB(NOW(), INTERVAL 3 DAY), 240.00, 'billet', 1, NULL),
(NOW(), 120.00, 'billet', 2, NULL),
(DATE_SUB(NOW(), INTERVAL 2 DAY), 1600.00, 'reservation', NULL, 1);

INSERT INTO depense (categorie, description, montant, date_depense, type, justificatif, id_jeu, id_utilisateur) VALUES
('électricité','Facture électricité du mois',900.00,DATE_SUB(CURDATE(), INTERVAL 10 DAY),'general','FAC-ELEC-01',NULL,1),
('pièces de rechange','Moteur du carrousel',450.00,DATE_SUB(CURDATE(), INTERVAL 4 DAY),'jeu','FAC-PIECE-07',4,1),
('nettoyage','Nettoyage hebdomadaire',200.00,DATE_SUB(CURDATE(), INTERVAL 2 DAY),'general',NULL,NULL,1);

INSERT INTO maintenance (type_intervention, description, date_intervention, cout, etat, date_prochaine, id_jeu, id_responsable) VALUES
('Réparation','Remplacement du moteur',DATE_SUB(CURDATE(), INTERVAL 4 DAY),450.00,'en_cours',DATE_ADD(CURDATE(), INTERVAL 60 DAY),4,2),
('Contrôle sécurité','Contrôle annuel des harnais',DATE_SUB(CURDATE(), INTERVAL 30 DAY),300.00,'terminee',DATE_ADD(CURDATE(), INTERVAL 335 DAY),2,1);
