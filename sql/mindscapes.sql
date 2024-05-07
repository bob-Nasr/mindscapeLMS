-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: May 07, 2024 at 09:15 PM
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
  `fullname` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `shortname` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `coordinator` int UNSIGNED DEFAULT NULL,
  `startAge` int DEFAULT NULL,
  `endAge` int DEFAULT NULL,
  PRIMARY KEY (`idcourse`),
  KEY `coordiantor_idx` (`coordinator`),
  KEY `type_idx` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`idcourse`, `type`, `fullname`, `shortname`, `coordinator`, `startAge`, `endAge`) VALUES
(1, 2, 'Exploration of Curiosity', 'Level 1', NULL, 6, 8),
(2, 2, 'The World Around Us', 'Level 2', NULL, 6, 8),
(3, 2, 'Beyond Boundaries', 'Level 3', NULL, 6, 8),
(4, 3, 'The Art of Discovery', 'Level 1', NULL, 7, 9),
(5, 3, 'The Discovery Chronicles', 'Level 2', NULL, 7, 9),
(6, 3, 'A Symphony of Discovery', 'Level 3', NULL, 7, 9);

-- --------------------------------------------------------

--
-- Table structure for table `course_type`
--

DROP TABLE IF EXISTS `course_type`;
CREATE TABLE IF NOT EXISTS `course_type` (
  `idcourseType` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(45) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `idDomain` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`idcourseType`),
  KEY `domain_idx1` (`idDomain`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `course_type`
--

INSERT INTO `course_type` (`idcourseType`, `type`, `description`, `idDomain`) VALUES
(1, 'KinderBot', 'Learn Robotics concepts, Psychomotor Skills, and Cognitive Development.', 1),
(2, 'Tiny Adventurers', 'Developing abilities to think critically and exploring the block-based programming.', 1),
(3, 'Robo Builders', 'Developing abilities to think critically, find innovative solutions in a fun and engaging way.', 1),
(4, 'Knowledge Scouts', 'In an engaging and enjoyable manner, they explore engineering and programming skills.', 1),
(5, 'World Explorers', 'Integrate engineering and programming seamlessly into interdisciplinary projects.', 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `domain`
--

INSERT INTO `domain` (`iddomain`, `type`) VALUES
(1, 'Level'),
(2, 'Activity'),
(3, 'Camp');

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

DROP TABLE IF EXISTS `education`;
CREATE TABLE IF NOT EXISTS `education` (
  `ideducation` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `Education_Type_idEdType` int UNSIGNED NOT NULL,
  `student_idStudent` int UNSIGNED NOT NULL,
  PRIMARY KEY (`ideducation`),
  KEY `fk_education_Education_Type1_idx` (`Education_Type_idEdType`),
  KEY `fk_education_student1_idx` (`student_idStudent`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`ideducation`, `startDate`, `endDate`, `Education_Type_idEdType`, `student_idStudent`) VALUES
(1, NULL, NULL, 1, 1),
(2, '2020-09-28', NULL, 5, 2),
(3, NULL, NULL, 3, 3),
(4, NULL, NULL, 5, 4);

-- --------------------------------------------------------

--
-- Table structure for table `education_type`
--

DROP TABLE IF EXISTS `education_type`;
CREATE TABLE IF NOT EXISTS `education_type` (
  `idEdType` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `EductaionType_name` varchar(45) NOT NULL,
  `location_idlocation` int UNSIGNED NOT NULL,
  `Institution_idInstitution` int UNSIGNED NOT NULL,
  PRIMARY KEY (`idEdType`),
  UNIQUE KEY `idSchool_UNIQUE` (`idEdType`),
  KEY `fk_school_location1_idx` (`location_idlocation`),
  KEY `fk_Education_Type_Institution1_idx` (`Institution_idInstitution`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `education_type`
--

INSERT INTO `education_type` (`idEdType`, `EductaionType_name`, `location_idlocation`, `Institution_idInstitution`) VALUES
(1, 'Beit Hebbak', 1, 1),
(2, 'College des Soeurs du Rosaire', 1, 1),
(3, 'Freres Maristes', 1, 1),
(4, 'Saint Joseph', 1, 1),
(5, 'Monsif International School', 7, 1);

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
  `dateOfBirth` date DEFAULT NULL,
  `experience` float NOT NULL,
  `status` varchar(45) DEFAULT 'working',
  `role` int UNSIGNED NOT NULL,
  PRIMARY KEY (`idemployee`),
  UNIQUE KEY `idemployee_UNIQUE` (`idemployee`),
  KEY `role_idx` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`idemployee`, `first_name`, `last_name`, `address`, `phone`, `email`, `dateOfBirth`, `experience`, `status`, `role`) VALUES
(1, 'Perla', 'Arif', 'Jbeil', '70286149', 'perla.arif02@gmail.com', '2002-06-11', 0, 'working', 4);

-- --------------------------------------------------------

--
-- Table structure for table `employee_role`
--

DROP TABLE IF EXISTS `employee_role`;
CREATE TABLE IF NOT EXISTS `employee_role` (
  `idemployee_role` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `role` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idemployee_role`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employee_role`
--

INSERT INTO `employee_role` (`idemployee_role`, `role`) VALUES
(1, 'manager'),
(2, 'coursecreator'),
(3, 'editingteacher'),
(4, 'teacher');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `guardians_students`
--

INSERT INTO `guardians_students` (`idguardians_students`, `idLegalGuardians`, `relationShip`, `idStudent`) VALUES
(1, 1, 'Mother', 1),
(2, 2, 'Mother', 2),
(3, 3, 'Mother', 3),
(4, 4, 'Mother', 4);

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
-- Table structure for table `institution`
--

DROP TABLE IF EXISTS `institution`;
CREATE TABLE IF NOT EXISTS `institution` (
  `idInstitution` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `Institution_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idInstitution`),
  UNIQUE KEY `idInstitution_UNIQUE` (`idInstitution`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `institution`
--

INSERT INTO `institution` (`idInstitution`, `Institution_name`) VALUES
(1, 'School'),
(2, 'University');

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
  `Emergency` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idLegalGuardians`),
  UNIQUE KEY `idContact_UNIQUE` (`idLegalGuardians`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `legal_guardians`
--

INSERT INTO `legal_guardians` (`idLegalGuardians`, `nameLegalGuardians`, `phoneNumber`, `address`, `Emergency`) VALUES
(1, 'Reine', '70216053', '', NULL),
(2, 'Tamara', '03468483', '', NULL),
(3, 'Elise', '03944521', '', '70934575'),
(4, 'Eveline', '03533405', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
CREATE TABLE IF NOT EXISTS `location` (
  `idlocation` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `locationName` varchar(45) NOT NULL,
  PRIMARY KEY (`idlocation`),
  UNIQUE KEY `idlocation_UNIQUE` (`idlocation`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`idlocation`, `locationName`) VALUES
(1, 'Jbeil'),
(2, 'Amshit'),
(3, 'Jounieh'),
(4, 'Batroun'),
(5, 'Achrafieh'),
(6, 'Keserwan'),
(7, 'Monsif');

-- --------------------------------------------------------

--
-- Table structure for table `material`
--

DROP TABLE IF EXISTS `material`;
CREATE TABLE IF NOT EXISTS `material` (
  `idmaterial` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` varchar(455) DEFAULT NULL,
  `session_nb` int DEFAULT NULL,
  `courseID` int UNSIGNED DEFAULT NULL,
  `duration` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idmaterial`),
  KEY `course_idx` (`courseID`)
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`idsemester`, `semesterName`, `satrtDate`, `endDate`) VALUES
(1, 'Spring', '2024-01-19', '2024-05-17');

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
  `grade` varchar(45) NOT NULL,
  `phoneNb` varchar(45) DEFAULT NULL,
  `email` varchar(45) NOT NULL,
  PRIMARY KEY (`idStudent`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`idStudent`, `studentFirstName`, `studentLastName`, `DateOfBirth`, `grade`, `phoneNb`, `email`) VALUES
(1, 'Chris', 'Houwayek', '2018-08-30', '1', NULL, ''),
(2, 'Luke', 'Hawat', '2017-07-07', '', NULL, ''),
(3, 'Cesar', 'Ghanem', '2018-07-03', '', NULL, ''),
(4, 'Ghadi', 'Mouawad', '2017-03-08', '', NULL, ''),
(5, 'Angy', 'Daou', '2017-07-24', '', NULL, ''),
(6, 'Simon', 'Abi Younes', '2018-03-14', '', NULL, 'abiyounes.elie@gmail.com'),
(7, 'Lucas', 'Tarabay', '2016-09-11', '', NULL, ''),
(8, 'Thomas', 'Tarabay', '2017-12-21', '', NULL, '');

--
-- Constraints for dumped tables
--

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
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `fk_employee_role` FOREIGN KEY (`role`) REFERENCES `employee_role` (`idemployee_role`);

--
-- Constraints for table `material`
--
ALTER TABLE `material`
  ADD CONSTRAINT `course` FOREIGN KEY (`courseID`) REFERENCES `course` (`idcourse`);

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;