-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Nov 26, 2024 alle 16:42
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
  `UTENTE_FK_ID` int(11) DEFAULT NULL,
  `CATEGORIA_FK_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `report`
--

CREATE TABLE `report` (
  `REPORT_ID` int(11) NOT NULL,
  `REPORT_Nome` varchar(100) NOT NULL,
  `REPORT_DataGenerazione` datetime DEFAULT current_timestamp(),
  `REPORT_FileExport` varchar(255) DEFAULT NULL,
  `UTENTE_FK_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Struttura della tabella `transizione`
--

CREATE TABLE `transizione` (
  `TRANSIZIONE_ID` int(11) NOT NULL,
  `TRANSIZIONE_Nome` varchar(100) NOT NULL,
  `TRANSIZIONE_Data` datetime NOT NULL,
  `TRANSIZIONE_DataGenerazione` datetime DEFAULT current_timestamp(),
  `TRANSIZIONE_QTA` int(11) NOT NULL,
  `TRANSIZIONE_Tipo` varchar(50) NOT NULL,
  `UTENTE_FK_ID` int(11) DEFAULT NULL,
  `CATEGORIA_FK_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ADMIN_ID`);

--
-- Indici per le tabelle `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`CATEGORIA_ID`),
  ADD KEY `fk_categoria_utente` (`UTENTE_FK_ID`),
  ADD KEY `fk_categoria_tipologia` (`TIPOLOGIA_FK_ID`);

--
-- Indici per le tabelle `milestone`
--
ALTER TABLE `milestone`
  ADD PRIMARY KEY (`MILESTONE_ID`),
  ADD KEY `UTENTE_FK_ID` (`UTENTE_FK_ID`),
  ADD KEY `fk_milestone_categoria` (`CATEGORIA_FK_ID`);

--
-- Indici per le tabelle `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`REPORT_ID`),
  ADD KEY `UTENTE_FK_ID` (`UTENTE_FK_ID`);

--
-- Indici per le tabelle `tipologia`
--
ALTER TABLE `tipologia`
  ADD PRIMARY KEY (`TIPOLOGIA_ID`),
  ADD KEY `ADMIN_FK_ID` (`ADMIN_FK_ID`);

--
-- Indici per le tabelle `transizione`
--
ALTER TABLE `transizione`
  ADD PRIMARY KEY (`TRANSIZIONE_ID`),
  ADD KEY `UTENTE_FK_ID` (`UTENTE_FK_ID`),
  ADD KEY `fk_transizione_categoria` (`CATEGORIA_FK_ID`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`UTENTE_ID`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `admin`
--
ALTER TABLE `admin`
  MODIFY `ADMIN_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `categoria`
--
ALTER TABLE `categoria`
  MODIFY `CATEGORIA_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `report`
--
ALTER TABLE `report`
  MODIFY `REPORT_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `tipologia`
--
ALTER TABLE `tipologia`
  MODIFY `TIPOLOGIA_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `transizione`
--
ALTER TABLE `transizione`
  MODIFY `TRANSIZIONE_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `utente`
--
ALTER TABLE `utente`
  MODIFY `UTENTE_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `categoria`
--
ALTER TABLE `categoria`
  ADD CONSTRAINT `fk_categoria_tipologia` FOREIGN KEY (`TIPOLOGIA_FK_ID`) REFERENCES `tipologia` (`TIPOLOGIA_ID`),
  ADD CONSTRAINT `fk_categoria_utente` FOREIGN KEY (`UTENTE_FK_ID`) REFERENCES `utente` (`UTENTE_ID`);

--
-- Limiti per la tabella `milestone`
--
ALTER TABLE `milestone`
  ADD CONSTRAINT `fk_milestone_categoria` FOREIGN KEY (`CATEGORIA_FK_ID`) REFERENCES `categoria` (`CATEGORIA_ID`),
  ADD CONSTRAINT `milestone_ibfk_1` FOREIGN KEY (`UTENTE_FK_ID`) REFERENCES `utente` (`UTENTE_ID`);

--
-- Limiti per la tabella `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`UTENTE_FK_ID`) REFERENCES `utente` (`UTENTE_ID`);

--
-- Limiti per la tabella `tipologia`
--
ALTER TABLE `tipologia`
  ADD CONSTRAINT `tipologia_ibfk_1` FOREIGN KEY (`ADMIN_FK_ID`) REFERENCES `admin` (`ADMIN_ID`);

--
-- Limiti per la tabella `transizione`
--
ALTER TABLE `transizione`
  ADD CONSTRAINT `fk_transizione_categoria` FOREIGN KEY (`CATEGORIA_FK_ID`) REFERENCES `categoria` (`CATEGORIA_ID`),
  ADD CONSTRAINT `transizione_ibfk_1` FOREIGN KEY (`UTENTE_FK_ID`) REFERENCES `utente` (`UTENTE_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
