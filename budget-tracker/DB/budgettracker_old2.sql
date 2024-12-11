-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Dic 11, 2024 alle 02:34
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `budgettracker`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `admin`
--

CREATE TABLE `admin` (
  `ADMIN_ID` int(11) NOT NULL,
  `ADMIN_Username` varchar(25) NOT NULL,
  `ADMIN_Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `admin`
--

INSERT INTO `admin` (`ADMIN_ID`, `ADMIN_Username`, `ADMIN_Password`) VALUES
(1, 'admin', '123');

-- --------------------------------------------------------

--
-- Struttura della tabella `categoria`
--

CREATE TABLE `categoria` (
  `CATEGORIA_ID` int(11) NOT NULL,
  `CATEGORIA_Nome` varchar(99) NOT NULL,
  `CATEGORIA_Descrizione` text DEFAULT NULL,
  `CATEGORIA_Budget` double NOT NULL,
  `UTENTE_FK_ID` int(11) DEFAULT NULL,
  `TIPOLOGIA_FK_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `categoria`
--

INSERT INTO `categoria` (`CATEGORIA_ID`, `CATEGORIA_Nome`, `CATEGORIA_Descrizione`, `CATEGORIA_Budget`, `UTENTE_FK_ID`, `TIPOLOGIA_FK_ID`) VALUES
(1, 'Verdura', 'Verdurereeerere', -3145.067, 1, 1),
(14, 'test', 'test', 1643, 8, 1),
(17, 'tettwetw', 'test', 4386, 8, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `milestone`
--

CREATE TABLE `milestone` (
  `MILESTONE_ID` int(11) NOT NULL,
  `MILESTONE_DataInizio` date NOT NULL DEFAULT curdate(),
  `MILESTONE_DataFine` date DEFAULT NULL,
  `MILESTONE_Nome` varchar(20) DEFAULT NULL,
  `MILESTONE_Descrizione` text DEFAULT NULL,
  `MILESTONE_Completata` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = completata\r\n0 = non completata',
  `UTENTE_FK_ID` int(11) DEFAULT NULL,
  `CATEGORIA_FK_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `milestone`
--

INSERT INTO `milestone` (`MILESTONE_ID`, `MILESTONE_DataInizio`, `MILESTONE_DataFine`, `MILESTONE_Nome`, `MILESTONE_Descrizione`, `MILESTONE_Completata`, `UTENTE_FK_ID`, `CATEGORIA_FK_ID`) VALUES
(11, '2020-12-12', '2021-02-12', 'test', 'tet', 1, 1, 1),
(12, '2021-12-12', '2022-12-12', 'test', 'ttt', 0, 1, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `report`
--

CREATE TABLE `report` (
  `REPORT_ID` int(11) NOT NULL,
  `REPORT_Nome` varchar(100) NOT NULL,
  `REPORT_Descrizione` text DEFAULT NULL,
  `REPORT_DataGenerazione` datetime DEFAULT current_timestamp(),
  `REPORT_FileExport` varchar(1000) DEFAULT NULL,
  `UTENTE_FK_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `report`
--

INSERT INTO `report` (`REPORT_ID`, `REPORT_Nome`, `REPORT_Descrizione`, `REPORT_DataGenerazione`, `REPORT_FileExport`, `UTENTE_FK_ID`) VALUES
(17, 'test', 'test', '2024-12-11 00:00:00', 'DB/DB_Reports/6758e200d01d98.52619219.csv', 1),
(18, 'testte', 'testte', '2024-12-11 00:00:00', 'DB/DB_Reports/6758e43e07e912.74287310.csv', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `tipologia`
--

CREATE TABLE `tipologia` (
  `TIPOLOGIA_ID` int(11) NOT NULL,
  `TIPOLOGIA_Nome` varchar(100) NOT NULL,
  `TIPOLOGIA_Descrizione` text DEFAULT NULL,
  `ADMIN_FK_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `tipologia`
--

INSERT INTO `tipologia` (`TIPOLOGIA_ID`, `TIPOLOGIA_Nome`, `TIPOLOGIA_Descrizione`, `ADMIN_FK_ID`) VALUES
(1, 'Spese', ' ', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `transizione`
--

CREATE TABLE `transizione` (
  `TRANSIZIONE_ID` int(11) NOT NULL,
  `TRANSIZIONE_Nome` varchar(100) NOT NULL,
  `TRANSIZIONE_Data` datetime NOT NULL,
  `TRANSIZIONE_DataGenerazione` datetime DEFAULT current_timestamp(),
  `TRANSIZIONE_QTA` double NOT NULL,
  `TRANSIZIONE_Tipo` varchar(50) NOT NULL,
  `UTENTE_FK_ID` int(11) DEFAULT NULL,
  `CATEGORIA_FK_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `transizione`
--

INSERT INTO `transizione` (`TRANSIZIONE_ID`, `TRANSIZIONE_Nome`, `TRANSIZIONE_Data`, `TRANSIZIONE_DataGenerazione`, `TRANSIZIONE_QTA`, `TRANSIZIONE_Tipo`, `UTENTE_FK_ID`, `CATEGORIA_FK_ID`) VALUES
(63, 'aa', '2020-12-12 00:00:00', '2024-12-10 01:05:58', 3232, 'SPESA', 1, 1),
(64, 'test', '2021-12-12 00:00:00', '2024-12-10 01:19:10', 34.5, 'SPESA', 1, 1),
(65, 'tester', '2022-12-12 00:00:00', '2024-12-10 01:19:33', 66.433, 'ENTRATA', 1, 1),
(66, 'giovanni', '2024-01-12 00:00:00', '2024-12-10 10:39:27', 55, 'ENTRATA', 1, 1),
(68, 'tst', '2020-02-12 00:00:00', '2024-12-10 23:06:01', 1231, 'ENTRATA', 8, 14),
(69, 'wtetew', '2020-12-12 00:00:00', '2024-12-11 02:24:41', 4343, 'ENTRATA', 8, 17);

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
  `UTENTE_ID` int(11) NOT NULL,
  `UTENTE_Username` varchar(25) NOT NULL,
  `UTENTE_Mail` varchar(100) NOT NULL,
  `UTENTE_Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`UTENTE_ID`, `UTENTE_Username`, `UTENTE_Mail`, `UTENTE_Password`) VALUES
(1, 'tester', 'tester@gmail.com', 'giovanni11'),
(8, 'test2', 'test2@gmail.com', 'test2test2');

-- --------------------------------------------------------

--
-- Aggiunta delle chiavi esterne con ON DELETE CASCADE
--

ALTER TABLE `categoria`
  ADD CONSTRAINT `fk_categoria_tipologia` FOREIGN KEY (`TIPOLOGIA_FK_ID`) REFERENCES `tipologia` (`TIPOLOGIA_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_categoria_utente` FOREIGN KEY (`UTENTE_FK_ID`) REFERENCES `utente` (`UTENTE_ID`) ON DELETE CASCADE;

ALTER TABLE `milestone`
  ADD CONSTRAINT `fk_milestone_categoria` FOREIGN KEY (`CATEGORIA_FK_ID`) REFERENCES `categoria` (`CATEGORIA_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `milestone_ibfk_1` FOREIGN KEY (`UTENTE_FK_ID`) REFERENCES `utente` (`UTENTE_ID`) ON DELETE CASCADE;

ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`UTENTE_FK_ID`) REFERENCES `utente` (`UTENTE_ID`) ON DELETE CASCADE;

ALTER TABLE `tipologia`
  ADD CONSTRAINT `tipologia_ibfk_1` FOREIGN KEY (`ADMIN_FK_ID`) REFERENCES `admin` (`ADMIN_ID`) ON DELETE CASCADE;

ALTER TABLE `transizione`
  ADD CONSTRAINT `fk_transizione_categoria` FOREIGN KEY (`CATEGORIA_FK_ID`) REFERENCES `categoria` (`CATEGORIA_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `transizione_ibfk_1` FOREIGN KEY (`UTENTE_FK_ID`) REFERENCES `utente` (`UTENTE_ID`) ON DELETE CASCADE;

COMMIT;
