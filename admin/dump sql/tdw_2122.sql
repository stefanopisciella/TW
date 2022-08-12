-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Mag 25, 2022 alle 15:12
-- Versione del server: 5.7.26
-- Versione PHP: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tdw_2122`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `news`
--

CREATE TABLE `news` (
  `id` int(10) UNSIGNED NOT NULL,
  `owner_username` varchar(50) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `date` varchar(10) DEFAULT NULL,
  `body` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `news`
--

INSERT INTO `news` (`id`, `owner_username`, `title`, `date`, `body`) VALUES
(1, '', 'title 1', '2022-05-09', 'body 1'),
(2, '', 'title 1', '2022-05-09', 'body 1'),
(3, '', 'title 2', '2022-05-10', 'body 2'),
(4, '', 'title 3', '2022-05-11', 'body 3'),
(5, '', 'title 3', '2022-05-11', 'body 3'),
(6, '', 'title 3', '2022-05-11', 'body 3'),
(7, '', 'title 3', '2022-05-11', 'body 3'),
(8, '', 'title 3', '2022-05-11', 'body 3'),
(9, '', 'title 3', '2022-05-11', 'body 3'),
(10, '', 'Notizia di oggi', '2022-05-16', 'Si so magnati la pecora'),
(11, '', 'title 3', '2022-05-11', 'body 3'),
(12, '', 'title 3', '2022-05-11', 'body 3'),
(13, '', 'title 3', '2022-05-11', 'body 3'),
(14, '', 'title 3', '2022-05-11', 'body 3'),
(15, '', 'title 3', '2022-05-11', 'body 3'),
(16, '', 'aaaa', '2022-05-12', 'aaaa'),
(17, '', 'aaaa', '2022-05-12', 'aaaa'),
(18, '', 'aaaa', '2022-05-12', 'aaaa'),
(19, '', 'aaaa', '2022-05-12', 'aaaa'),
(20, '', 'aaaa', '2022-05-12', 'aaaa'),
(21, '', 'sss', '2022-05-26', 'sss'),
(22, '', 'sss', '2022-05-26', 'sss'),
(27, '', 'sss', '2022-05-27', 'ssss'),
(28, '', 'sss', '2022-05-27', 'ssss'),
(29, '', 'sss', '2022-05-27', 'ssss'),
(30, '', 'sss', '2022-05-27', 'ssss'),
(31, '', 'sss', '2022-05-27', 'ssss'),
(32, 'admin', 'BBBB', '2022-05-27', 'BBBBBB'),
(33, 'alfonso', 'AAAAAAA', '2022-05-27', 'AAAAAAAA');

-- --------------------------------------------------------

--
-- Struttura della tabella `service`
--

CREATE TABLE `service` (
  `id` int(10) UNSIGNED NOT NULL,
  `script` varchar(100) DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `service`
--

INSERT INTO `service` (`id`, `script`, `description`) VALUES
(1, 'dashboard.php', NULL),
(2, 'logout.php', NULL),
(3, 'news-add.php', NULL),
(4, 'news-edit.php', NULL),
(5, 'news-edit2.php', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `slider`
--

CREATE TABLE `slider` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `type` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `slider`
--

INSERT INTO `slider` (`id`, `title`, `subtitle`, `type`) VALUES
(1, 'title 1', 'subtitle 1', 1),
(2, 'title 2', 'subtitle 2', 2),
(3, 'title 3', 'subtitle 3', 3),
(4, 'title 4', 'subtitle 4', 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `ugroup`
--

CREATE TABLE `ugroup` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ugroup`
--

INSERT INTO `ugroup` (`id`, `name`, `description`) VALUES
(1, 'Administration', NULL),
(2, 'Editor', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `ugroup_has_service`
--

CREATE TABLE `ugroup_has_service` (
  `ugroup_id` int(10) UNSIGNED DEFAULT NULL,
  `service_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `ugroup_has_service`
--

INSERT INTO `ugroup_has_service` (`ugroup_id`, `service_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 5),
(2, 4),
(2, 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `user`
--

CREATE TABLE `user` (
  `username` varchar(50) NOT NULL,
  `password` varchar(32) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `surname` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `user`
--

INSERT INTO `user` (`username`, `password`, `name`, `surname`, `email`) VALUES
('admin', '1b12fefbe423632ed041dd56f48ba47c', 'Amministratore', NULL, 'admin@boh.com'),
('alfonso', '1b12fefbe423632ed041dd56f48ba47c', 'Alfonso', 'Pierantonio', 'admin@boh.com');

-- --------------------------------------------------------

--
-- Struttura della tabella `user_has_ugroup`
--

CREATE TABLE `user_has_ugroup` (
  `user_username` varchar(50) DEFAULT NULL,
  `ugroup_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `user_has_ugroup`
--

INSERT INTO `user_has_ugroup` (`user_username`, `ugroup_id`) VALUES
('admin', 1),
('alfonso', 2),
('admin', 2);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `ugroup`
--
ALTER TABLE `ugroup`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `news`
--
ALTER TABLE `news`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT per la tabella `service`
--
ALTER TABLE `service`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `slider`
--
ALTER TABLE `slider`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `ugroup`
--
ALTER TABLE `ugroup`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
