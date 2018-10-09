-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2018 at 10:06 PM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 7.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `forum`
--
CREATE DATABASE IF NOT EXISTS `forum` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `forum`;

-- --------------------------------------------------------

--
-- Table structure for table `loginattempts`
--

CREATE TABLE IF NOT EXISTS `loginattempts` (
  `loginID` int(11) NOT NULL AUTO_INCREMENT,
  `loginUserName` varchar(50) NOT NULL,
  `loginTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `loginSuccessful` enum('no','yes') NOT NULL,
  `loginIP` varchar(15) NOT NULL,
  PRIMARY KEY (`loginID`),
  KEY `loginUserName` (`loginUserName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `pID` int(11) NOT NULL AUTO_INCREMENT,
  `pName` varchar(50) NOT NULL,
  `pContent` varchar(2500) DEFAULT NULL,
  `pTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pReplyTo` int(11) DEFAULT NULL,
  `pDeleted` tinyint(1) DEFAULT NULL,
  `pDeletedBy` varchar(50) DEFAULT NULL,
  `pAuthor` varchar(50) NOT NULL,
  `pThreadID` int(11) DEFAULT NULL,
  PRIMARY KEY (`pID`),
  KEY `pAuthor` (`pAuthor`),
  KEY `pThreadID` (`pThreadID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `rType` varchar(20) NOT NULL,
  PRIMARY KEY (`rType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subforums`
--

CREATE TABLE IF NOT EXISTS `subforums` (
  `sID` int(11) NOT NULL AUTO_INCREMENT,
  `sName` varchar(200) NOT NULL,
  PRIMARY KEY (`sID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `threads`
--

CREATE TABLE IF NOT EXISTS `threads` (
  `thID` int(11) NOT NULL AUTO_INCREMENT,
  `thName` varchar(200) NOT NULL,
  `thTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `thLock` tinyint(1) DEFAULT NULL,
  `thAuthor` varchar(50) NOT NULL,
  `thTopicID` int(11) DEFAULT NULL,
  PRIMARY KEY (`thID`),
  UNIQUE KEY `thName` (`thName`),
  KEY `thAuthor` (`thAuthor`),
  KEY `thTopicID` (`thTopicID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE IF NOT EXISTS `topics` (
  `tID` int(11) NOT NULL AUTO_INCREMENT,
  `tName` varchar(200) NOT NULL,
  `tSubForumID` int(11) DEFAULT NULL,
  PRIMARY KEY (`tID`),
  KEY `tSubForumID` (`tSubForumID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `urole`
--

CREATE TABLE IF NOT EXISTS `urole` (
  `urID` int(11) NOT NULL,
  `urType` varchar(50) NOT NULL,
  PRIMARY KEY (`urID`,`urType`),
  KEY `urType` (`urType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uuser`
--

CREATE TABLE IF NOT EXISTS `uuser` (
  `uID` int(11) NOT NULL AUTO_INCREMENT,
  `uEmail` varchar(50) NOT NULL,
  `uUsername` varchar(50) NOT NULL,
  `uPassword` varchar(60) NOT NULL,
  `uCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uID`),
  UNIQUE KEY `uEmail` (`uEmail`),
  UNIQUE KEY `uUsername` (`uUsername`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loginattempts`
--
ALTER TABLE `loginattempts`
  ADD CONSTRAINT `loginattempts_ibfk_1` FOREIGN KEY (`loginUserName`) REFERENCES `uuser` (`uUsername`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`pAuthor`) REFERENCES `uuser` (`uUsername`),
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`pThreadID`) REFERENCES `threads` (`thID`);

--
-- Constraints for table `threads`
--
ALTER TABLE `threads`
  ADD CONSTRAINT `threads_ibfk_1` FOREIGN KEY (`thAuthor`) REFERENCES `uuser` (`uUsername`),
  ADD CONSTRAINT `threads_ibfk_2` FOREIGN KEY (`thTopicID`) REFERENCES `topics` (`tID`);

--
-- Constraints for table `topics`
--
ALTER TABLE `topics`
  ADD CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`tSubForumID`) REFERENCES `subforums` (`sID`);

--
-- Constraints for table `urole`
--
ALTER TABLE `urole`
  ADD CONSTRAINT `urole_ibfk_1` FOREIGN KEY (`urID`) REFERENCES `uuser` (`uID`),
  ADD CONSTRAINT `urole_ibfk_2` FOREIGN KEY (`urType`) REFERENCES `roles` (`rType`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
