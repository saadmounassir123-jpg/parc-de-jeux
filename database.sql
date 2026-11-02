-- LocaOuvriers : location d'ouvriers à la journée
CREATE DATABASE IF NOT EXISTS locaouvriers CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE locaouvriers;
SET NAMES utf8mb4;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  mot_de_passe VARCHAR(255) NOT NULL,
  telephone VARCHAR(30) DEFAULT NULL,
  adresse VARCHAR(255) DEFAULT NULL,
  role ENUM('client','ouvrier','admin') NOT NULL DEFAULT 'client',
  statut ENUM('actif','suspendu') NOT NULL DEFAULT 'actif',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE trades (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  tarif_journalier DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE workers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  trade_id INT NOT NULL,
  specialites VARCHAR(255) DEFAULT NULL,
  salaire_mensuel DECIMAL(10,2) NOT NULL,
  statut ENUM('actif','inactif') NOT NULL DEFAULT 'actif',
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (trade_id) REFERENCES trades(id)
) ENGINE=InnoDB;

CREATE TABLE reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  trade_id INT NOT NULL,
  nombre_ouvriers INT NOT NULL DEFAULT 1,
  date_debut DATE NOT NULL,
  date_fin DATE NOT NULL,
  adresse VARCHAR(255) NOT NULL,
  description TEXT,
  montant DECIMAL(10,2) NOT NULL,
  statut ENUM('en_attente','acceptee','affectee','en_cours','terminee','refusee','annulee') NOT NULL DEFAULT 'en_attente',
  paye TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (trade_id) REFERENCES trades(id)
) ENGINE=InnoDB;

CREATE TABLE assignments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reservation_id INT NOT NULL,
  worker_id INT NOT NULL,
  date_mission DATE NOT NULL,
  statut ENUM('planifiee','en_cours','terminee','a_remplacer') NOT NULL DEFAULT 'planifiee',
  remplace TINYINT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY un_ouvrier_par_jour (worker_id, date_mission),
  FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
  FOREIGN KEY (worker_id) REFERENCES workers(id)
) ENGINE=InnoDB;

CREATE TABLE absences (
  id INT AUTO_INCREMENT PRIMARY KEY,
  worker_id INT NOT NULL,
  date_absence DATE NOT NULL,
  motif VARCHAR(255) DEFAULT NULL,
  justifiee TINYINT(1) NOT NULL DEFAULT 0,
  retenue DECIMAL(10,2) NOT NULL DEFAULT 0,
  assignment_id INT DEFAULT NULL,
  remplacant_id INT DEFAULT NULL,
  UNIQUE KEY une_absence_par_jour (worker_id, date_absence),
  FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE,
  FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE SET NULL,
  FOREIGN KEY (remplacant_id) REFERENCES workers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE salaries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  worker_id INT NOT NULL,
  mois TINYINT NOT NULL,
  annee SMALLINT NOT NULL,
  salaire_base DECIMAL(10,2) NOT NULL,
  jours_absence INT NOT NULL DEFAULT 0,
  retenues DECIMAL(10,2) NOT NULL DEFAULT 0,
  salaire_net DECIMAL(10,2) NOT NULL,
  statut ENUM('a_payer','paye') NOT NULL DEFAULT 'a_payer',
  UNIQUE KEY une_fiche_par_mois (worker_id, mois, annee),
  FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reservation_id INT NOT NULL UNIQUE,
  client_id INT NOT NULL,
  note TINYINT NOT NULL,
  commentaire TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
  FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Données de test (mot de passe de tous les comptes : admin123)
INSERT INTO users (nom,email,mot_de_passe,telephone,role) VALUES
 ('Administrateur','admin@site.com','$2b$12$Kh0Ty8ubka/u4rmQxC7CaOoFWBs0fQzY54rQ//BvBFGjCi1sXEijC','0600000000','admin'),
 ('Client Test','client@site.com','$2b$12$Kh0Ty8ubka/u4rmQxC7CaOoFWBs0fQzY54rQ//BvBFGjCi1sXEijC','0611111111','client'),
 ('Ahmed Jardinier','ouvrier@site.com','$2b$12$Kh0Ty8ubka/u4rmQxC7CaOoFWBs0fQzY54rQ//BvBFGjCi1sXEijC','0622222222','ouvrier'),
 ('Karim Jardinier','karim@site.com','$2b$12$Kh0Ty8ubka/u4rmQxC7CaOoFWBs0fQzY54rQ//BvBFGjCi1sXEijC','0633333333','ouvrier'),
 ('Youssef Plombier','plombier@site.com','$2b$12$Kh0Ty8ubka/u4rmQxC7CaOoFWBs0fQzY54rQ//BvBFGjCi1sXEijC','0644444444','ouvrier'),
 ('Hamza Électricien','electricien@site.com','$2b$12$Kh0Ty8ubka/u4rmQxC7CaOoFWBs0fQzY54rQ//BvBFGjCi1sXEijC','0655555555','ouvrier');

INSERT INTO trades (nom,tarif_journalier) VALUES
 ('Jardinier',300),('Plombier',450),('Agent de sécurité',350),('Électricien',450),('Peintre',400);

INSERT INTO workers (user_id,trade_id,specialites,salaire_mensuel) VALUES
 (3,1,'Taille, tonte, plantation',4000),
 (4,1,'Entretien de jardin',4000),
 (5,2,'Fuites, installation sanitaire',4500),
 (6,4,'Câblage, tableaux électriques',4500);
