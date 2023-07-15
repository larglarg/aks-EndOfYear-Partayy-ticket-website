-- Datenbank `aks-endofyear-partayy-tickets`
CREATE DATABASE `aks-EndOfYear-Partayy-tickets`;

-- Tabelle 'menschen' erstelle`aks-endofyear-partayy-tickets`n
USE `aks-EndOfYear-Partayy-tickets`;
CREATE TABLE `menschen` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100),
  `vorname` VARCHAR(100),
  `gb_datum` DATE,
  `schule_id` INT,
  `email` VARCHAR(100),
  `email_verified` BOOLEAN DEFAULT FALSE,
  `hash` VARCHAR(1000)
);

-- Tabelle 'schulen' erstellemainn
CREATE TABLE `schulen` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100),
  `mks` VARCHAR(100)
);

-- Tabelle 'bestellung' erstellen`aks-einformation_schemando`aks-endofyear-partayy-tickets`fyear-partayy-tickets`bestellung`aks-endofyear-partayy-tickets`bestellung
CREATE TABLE `bestellung` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `Anzahl_tickets` INT,
  `besteller_id` INT,
  `gast1_id` INT,
  `gast2_id` INT,
  `gast3_id` INT,
  `gast4_id` INT,
  `status` ENUM('reserviert', 'besteatigt', 'gekauft', 'abgelaufen', 'storno'),
  `wann_erstellt` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `einzeld_oder_zusammen` BOOLEAN,
  `hash` VARCHAR(1000),
  `besteller_storniert` BOOLEAN,
  `gast1_storniert` BOOLEAN,
  `gast2_storniert` BOOLEAN,
  `gast3_storniert` BOOLEAN,
  `gast4_storniert` BOOLEAN
);

CREATE TABLE `password` (
	`id` INT PRIMARY KEY AUTO_INCREMENT,
	`PwHash` VARCHAR(100)
);

-- Tabelle 'main' erstellen
CREATE TABLE `main` (
  `id` INT PRIMARY KEY,
  `status` ENUM('frei', 'reserviert', 'verkauft', 'besteatigt'),
  `mensch_id` INT,
  `reservierung_id` INT
);

-- Standardwerte für die Tabelle 'main' einfügen
INSERT INTO `main` (`id`, `status`) VALUES
  (1, 'frei'), (2, 'frei'), (3, 'frei'), (4, 'frei'), (5, 'frei'), (6, 'frei'), (7, 'frei'), (8, 'frei'), (9, 'frei'), (10, 'frei'),
  (11, 'frei'), (12, 'frei'), (13, 'frei'), (14, 'frei'), (15, 'frei'), (16, 'frei'), (17, 'frei'), (18, 'frei'), (19, 'frei'), (20, 'frei'),
  (21, 'frei'), (22, 'frei'), (23, 'frei'), (24, 'frei'), (25, 'frei'), (26, 'frei'), (27, 'frei'), (28, 'frei'), (29, 'frei'), (30, 'frei'),
  (31, 'frei'), (32, 'frei'), (33, 'frei'), (34, 'frei'), (35, 'frei'), (36, 'frei'), (37, 'frei'), (38, 'frei'), (39, 'frei'), (40, 'frei'),
  (41, 'frei'), (42, 'frei'), (43, 'frei'), (44, 'frei'), (45, 'frei'), (46, 'frei'), (47, 'frei'), (48, 'frei'), (49, 'frei'), (50, 'frei'),
  (51, 'frei'), (52, 'frei'), (53, 'frei'), (54, 'frei'), (55, 'frei'), (56, 'frei'), (57, 'frei'), (58, 'frei'), (59, 'frei'), (60, 'frei'),
  (61, 'frei'), (62, 'frei'), (63, 'frei'), (64, 'frei'), (65, 'frei'), (66, 'frei'), (67, 'frei'), (68, 'frei'), (69, 'frei'), (70, 'frei'),
  (71, 'frei'), (72, 'frei'), (73, 'frei'), (74, 'frei'), (75, 'frei'), (76, 'frei'), (77, 'frei'), (78, 'frei'), (79, 'frei'), (80, 'frei'),
  (81, 'frei'), (82, 'frei'), (83, 'frei'), (84, 'frei'), (85, 'frei'), (86, 'frei'), (87, 'frei'), (88, 'frei'), (89, 'frei'), (90, 'frei');
