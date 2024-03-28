-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Mar 28, 2024 at 01:56 PM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mindscapes`
--

-- --------------------------------------------------------

--
-- Table structure for table `assessment`
--

DROP TABLE IF EXISTS `assessment`;
CREATE TABLE IF NOT EXISTS `assessment` (
  `idassessment` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `idStudent` int UNSIGNED DEFAULT NULL,
  `idSession` int UNSIGNED DEFAULT NULL,
  `idIndicator` int UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL,
  `rate` int DEFAULT '0',
  PRIMARY KEY (`idassessment`),
  UNIQUE KEY `idassessment_UNIQUE` (`idassessment`),
  KEY `studentss_idx` (`idStudent`),
  KEY `sessionsss_idx` (`idSession`),
  KEY `indicator_idx` (`idIndicator`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `idattendance` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `idStudent` int UNSIGNED DEFAULT NULL,
  `idSession` int UNSIGNED DEFAULT NULL,
  `present` tinyint DEFAULT '0',
  PRIMARY KEY (`idattendance`),
  KEY `schedule_idx` (`idSession`),
  KEY `student_idx` (`idStudent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
CREATE TABLE IF NOT EXISTS `course` (
  `idcourse` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` int UNSIGNED DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `coordinator` int UNSIGNED DEFAULT NULL,
  `startAge` int DEFAULT NULL,
  `endAge` int DEFAULT NULL,
  PRIMARY KEY (`idcourse`),
  KEY `coordiantor_idx` (`coordinator`),
  KEY `type_idx` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `course_type`
--

DROP TABLE IF EXISTS `course_type`;
CREATE TABLE IF NOT EXISTS `course_type` (
  `idcourseType` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(45) DEFAULT NULL,
  `idDomain` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`idcourseType`),
  KEY `domain_idx1` (`idDomain`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `domain`
--

DROP TABLE IF EXISTS `domain`;
CREATE TABLE IF NOT EXISTS `domain` (
  `iddomain` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`iddomain`),
  UNIQUE KEY `iddomain_UNIQUE` (`iddomain`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

DROP TABLE IF EXISTS `education`;
CREATE TABLE IF NOT EXISTS `education` (
  `ideducation` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `idEmployee` int UNSIGNED DEFAULT NULL,
  `idInstitution` int UNSIGNED DEFAULT NULL,
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  PRIMARY KEY (`ideducation`),
  KEY `employee_idx` (`idEmployee`),
  KEY `institution_idx` (`idInstitution`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `educational_institution`
--

DROP TABLE IF EXISTS `educational_institution`;
CREATE TABLE IF NOT EXISTS `educational_institution` (
  `idInstitution` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `institution_Name` varchar(45) DEFAULT NULL,
  `address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idInstitution`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
CREATE TABLE IF NOT EXISTS `employee` (
  `idemployee` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `address` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `role` int UNSIGNED NOT NULL,
  `experience` float NOT NULL,
  `dateOfBirth` date DEFAULT NULL,
  `status` varchar(45) DEFAULT 'working',
  PRIMARY KEY (`idemployee`),
  UNIQUE KEY `idemployee_UNIQUE` (`idemployee`),
  KEY `role_idx` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_role`
--

DROP TABLE IF EXISTS `employee_role`;
CREATE TABLE IF NOT EXISTS `employee_role` (
  `idemployee_role` int UNSIGNED NOT NULL,
  `role` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idemployee_role`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `experience`
--

DROP TABLE IF EXISTS `experience`;
CREATE TABLE IF NOT EXISTS `experience` (
  `idexperience` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `idemployee` int UNSIGNED DEFAULT NULL,
  `description` varchar(445) DEFAULT NULL,
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `companyName` varchar(45) DEFAULT NULL,
  `companyAddress` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idexperience`),
  KEY `idEmployeee_idx` (`idemployee`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `guardians_students`
--

DROP TABLE IF EXISTS `guardians_students`;
CREATE TABLE IF NOT EXISTS `guardians_students` (
  `idguardians_students` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `idLegalGuardians` int UNSIGNED DEFAULT NULL,
  `relationShip` varchar(45) DEFAULT NULL,
  `idStudent` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`idguardians_students`),
  UNIQUE KEY `idguardians_students_UNIQUE` (`idguardians_students`),
  KEY `idLegalGuardian_idx` (`idLegalGuardians`),
  KEY `idStudent_idx` (`idStudent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `indicators`
--

DROP TABLE IF EXISTS `indicators`;
CREATE TABLE IF NOT EXISTS `indicators` (
  `idindicators` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` int UNSIGNED DEFAULT NULL,
  `title` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idindicators`),
  UNIQUE KEY `idindicators_UNIQUE` (`idindicators`),
  KEY `type_idx` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `indicator_types`
--

DROP TABLE IF EXISTS `indicator_types`;
CREATE TABLE IF NOT EXISTS `indicator_types` (
  `idindicator_types` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idindicator_types`),
  UNIQUE KEY `idindicator_types_UNIQUE` (`idindicator_types`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `legal_guardians`
--

DROP TABLE IF EXISTS `legal_guardians`;
CREATE TABLE IF NOT EXISTS `legal_guardians` (
  `idLegalGuardians` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nameLegalGuardians` varchar(45) NOT NULL,
  `phoneNumber` varchar(45) NOT NULL,
  `address` varchar(45) NOT NULL,
  PRIMARY KEY (`idLegalGuardians`),
  UNIQUE KEY `idContact_UNIQUE` (`idLegalGuardians`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `material`
--

DROP TABLE IF EXISTS `material`;
CREATE TABLE IF NOT EXISTS `material` (
  `idmaterial` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` varchar(455) DEFAULT NULL,
  `session_nb` int DEFAULT NULL,
  `course` int UNSIGNED DEFAULT NULL,
  `duration` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idmaterial`),
  KEY `course_idx` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
CREATE TABLE IF NOT EXISTS `payment` (
  `idpayment` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `amount` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `idRegistration` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`idpayment`),
  KEY `idRegistartion_idx` (`idRegistration`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

DROP TABLE IF EXISTS `registration`;
CREATE TABLE IF NOT EXISTS `registration` (
  `idregistration` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `idSection` int UNSIGNED DEFAULT NULL,
  `status` varchar(45) DEFAULT 'pending',
  `registrationDate` date DEFAULT NULL,
  `fee` int DEFAULT NULL,
  `idStudent` int UNSIGNED DEFAULT NULL,
  `completionDate` date DEFAULT NULL,
  PRIMARY KEY (`idregistration`),
  KEY `section_idx` (`idSection`),
  KEY `idStudent_idx` (`idStudent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `salary`
--

DROP TABLE IF EXISTS `salary`;
CREATE TABLE IF NOT EXISTS `salary` (
  `idSalary` int(10) UNSIGNED ZEROFILL NOT NULL,
  `IdEmployee` int UNSIGNED NOT NULL,
  `amount` decimal(10,0) DEFAULT NULL,
  `currency` varchar(45) DEFAULT '$',
  PRIMARY KEY (`idSalary`),
  KEY `employee_idx` (`IdEmployee`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

DROP TABLE IF EXISTS `schedule`;
CREATE TABLE IF NOT EXISTS `schedule` (
  `idschedule` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `idSession` int UNSIGNED DEFAULT NULL,
  `idSection` int UNSIGNED DEFAULT NULL,
  `time` varchar(45) DEFAULT NULL,
  `date` date DEFAULT NULL,
  PRIMARY KEY (`idschedule`),
  KEY `session_idx` (`idSession`),
  KEY `section_idx` (`idSection`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `school`
--

DROP TABLE IF EXISTS `school`;
CREATE TABLE IF NOT EXISTS `school` (
  `idSchool` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nameSchool` varchar(45) NOT NULL,
  `Location` varchar(45) NOT NULL,
  PRIMARY KEY (`idSchool`),
  UNIQUE KEY `idSchool_UNIQUE` (`idSchool`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

DROP TABLE IF EXISTS `section`;
CREATE TABLE IF NOT EXISTS `section` (
  `idsection` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `instructor` int UNSIGNED DEFAULT NULL,
  `idCourse` int UNSIGNED DEFAULT NULL,
  `room` varchar(45) DEFAULT NULL,
  `idSemester` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`idsection`),
  KEY `instructor_idx` (`instructor`),
  KEY `course_idx` (`idCourse`),
  KEY `semester_idx` (`idSemester`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

DROP TABLE IF EXISTS `semester`;
CREATE TABLE IF NOT EXISTS `semester` (
  `idsemester` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `semesterName` varchar(45) DEFAULT NULL,
  `satrtDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  PRIMARY KEY (`idsemester`),
  UNIQUE KEY `idsemester_UNIQUE` (`idsemester`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `idStudent` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `studentFirstName` varchar(45) NOT NULL,
  `studentLastName` varchar(45) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `schoolID` int UNSIGNED NOT NULL,
  `grade` varchar(45) NOT NULL,
  `phoneNb` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idStudent`),
  KEY `school_id_idx` (`schoolID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessment`
--
ALTER TABLE `assessment`
  ADD CONSTRAINT `inds` FOREIGN KEY (`idIndicator`) REFERENCES `indicators` (`idindicators`),
  ADD CONSTRAINT `indSessipon` FOREIGN KEY (`idSession`) REFERENCES `schedule` (`idschedule`),
  ADD CONSTRAINT `indstuident` FOREIGN KEY (`idStudent`) REFERENCES `student` (`idStudent`);

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `session` FOREIGN KEY (`idSession`) REFERENCES `schedule` (`idschedule`),
  ADD CONSTRAINT `student` FOREIGN KEY (`idStudent`) REFERENCES `student` (`idStudent`);

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `coordinator` FOREIGN KEY (`coordinator`) REFERENCES `employee` (`idemployee`),
  ADD CONSTRAINT `type` FOREIGN KEY (`type`) REFERENCES `course_type` (`idcourseType`);

--
-- Constraints for table `course_type`
--
ALTER TABLE `course_type`
  ADD CONSTRAINT `domain` FOREIGN KEY (`idDomain`) REFERENCES `domain` (`iddomain`);

--
-- Constraints for table `education`
--
ALTER TABLE `education`
  ADD CONSTRAINT `employee` FOREIGN KEY (`idEmployee`) REFERENCES `employee` (`idemployee`),
  ADD CONSTRAINT `institution` FOREIGN KEY (`idInstitution`) REFERENCES `educational_institution` (`idInstitution`);

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `role` FOREIGN KEY (`role`) REFERENCES `employee_role` (`idemployee_role`);

--
-- Constraints for table `experience`
--
ALTER TABLE `experience`
  ADD CONSTRAINT `idEmployeee` FOREIGN KEY (`idemployee`) REFERENCES `employee` (`idemployee`);

--
-- Constraints for table `guardians_students`
--
ALTER TABLE `guardians_students`
  ADD CONSTRAINT `idStudent` FOREIGN KEY (`idStudent`) REFERENCES `student` (`idStudent`),
  ADD CONSTRAINT `legalGuardian` FOREIGN KEY (`idLegalGuardians`) REFERENCES `legal_guardians` (`idLegalGuardians`);

--
-- Constraints for table `indicators`
--
ALTER TABLE `indicators`
  ADD CONSTRAINT `typeInd` FOREIGN KEY (`type`) REFERENCES `indicator_types` (`idindicator_types`);

--
-- Constraints for table `material`
--
ALTER TABLE `material`
  ADD CONSTRAINT `course` FOREIGN KEY (`course`) REFERENCES `course` (`idcourse`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `idRegistartion` FOREIGN KEY (`idRegistration`) REFERENCES `registration` (`idregistration`);

--
-- Constraints for table `registration`
--
ALTER TABLE `registration`
  ADD CONSTRAINT `section` FOREIGN KEY (`idSection`) REFERENCES `section` (`idsection`),
  ADD CONSTRAINT `sts` FOREIGN KEY (`idStudent`) REFERENCES `student` (`idStudent`);

--
-- Constraints for table `salary`
--
ALTER TABLE `salary`
  ADD CONSTRAINT `idEmployee` FOREIGN KEY (`IdEmployee`) REFERENCES `employee` (`idemployee`);

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `idSection` FOREIGN KEY (`idSection`) REFERENCES `section` (`idsection`),
  ADD CONSTRAINT `idSession` FOREIGN KEY (`idSession`) REFERENCES `material` (`idmaterial`);

--
-- Constraints for table `section`
--
ALTER TABLE `section`
  ADD CONSTRAINT `idCourse` FOREIGN KEY (`idCourse`) REFERENCES `course` (`idcourse`),
  ADD CONSTRAINT `instructor` FOREIGN KEY (`instructor`) REFERENCES `employee` (`idemployee`),
  ADD CONSTRAINT `semester` FOREIGN KEY (`idSemester`) REFERENCES `semester` (`idsemester`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `school` FOREIGN KEY (`schoolID`) REFERENCES `school` (`idSchool`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
