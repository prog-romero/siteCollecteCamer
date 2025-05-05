-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 01, 2023 at 04:54 PM
-- Server version: 10.1.24-MariaDB
-- PHP Version: 7.0.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_collecte`
--
CREATE DATABASE IF NOT EXISTS `db_collecte` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `db_collecte`;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `text` varchar(300) NOT NULL,
  `haineux` int(11) DEFAULT '0',
  `non_haineux` int(11) DEFAULT '0',
  `hesite` int(11) NOT NULL DEFAULT '0',
  `vote_final` varchar(100) DEFAULT NULL,
  `valide` int(11) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL,
  `id_source` int(11) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `categorie` varchar(200) DEFAULT NULL,
  `total_votes` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `adm` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `username`, `password`, `adm`, `type`) VALUES
(2, 'Ridano', '$2y$10$9Ub4xdLuGINDmcCSvNQ./udfbcEFN5Hj62.V8s8hLmda2/pOkkFrW', 0, 'Approvisioneur'),
(3, 'Noe', '$2y$10$7u1yd8C3SEe53OD0l1DQv.cIULNUsLdOciAU1zTWYY/8WjJ92aD8.', 0, 'Votant'),
(4, 'Neymar', '$2y$10$TS5xdYox6JlCu760.JQ.muIE/T75oFXLN08VD7jQfAe5l28.kla5K', 0, 'Votant'),
(5, 'Messi', '$2y$10$vebADvS55DLNkbFRV319SOx3fORVHSvURrsOSLia1YGviIq4xHthm', 0, 'Votant'),
(7, 'Dr.Messi', '$2y$10$vbbtw8.z/XiPrpWIPHYeKOMmRP6XMEfmhkUG7RSq4ZBz5NjmtsvOm', 1, 'Approvisioneur'),
(8, 'Mr.Onana', '$2y$10$A2l6pPPju/KuKEfu2Uu6MeWNcFbLQGuty6HspZnX8jgHTrRWbjtEG', 1, 'Approvisioneur'),
(9, 'Ronaldo', '$2y$10$4ZVSqBa/HlCCeYfAnJOASu1FNnJWvUPVFBduzZQntZYHfLlUxL1tm', 0, 'Approvisioneur'),
(10, 'Special_User', '$2y$10$BQ/.sEjIMNB0Cr/jB26r3.pcE9wj.QyXb1f3g2EogBLGbxgXb97XK', 0, 'Votant');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id_message` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `vote` varchar(255) NOT NULL,
  `explication` varchar(255) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id_message`, `id_user`, `vote`, `explication`, `id`) VALUES
(18, 3, 'haineux', 'nbvgngvbvb', 16),
(20, 3, 'non_haineux', 'il voulait juste du café', 17),
(17, 4, 'haineux', 'wway c\'est haineux', 18),
(22, 3, 'non_haineux', 'car c\'est une salutation', 19),
(29, 3, 'non_haineux', 'Yess Ohh', 20);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
