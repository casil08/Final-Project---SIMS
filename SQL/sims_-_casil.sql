-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2025 at 08:27 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sims - casil`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_code`, `course_name`, `department`) VALUES
('BSA', 'Bachelor of Science in Accountancy', 'School of Business (SOB)'),
('BSTM', 'Bachelor of Science in Tourism Management', 'School of Business (SOB)'),
('BSHM', 'Bachelor of Science in Hospitality Management', 'School of Business (SOB)'),
('BSOA', 'Bachelor of Science in Office Administration', 'School of Business (SOB)'),
('BSBA-MM', 'Bachelor of Science in Business Administration major in Marketing Management', 'School of Business (SOB)'),
('BSBA-HRM', 'Bachelor of Science in Business Administration major in Human Resources Management', 'School of Business (SOB)'),
('BSBA - OM', 'Bachelor of Science in Business Administration major in Operations Management', 'School of Business (SOB)'),
('BSBA - FM', 'Bachelor of Science in Business Administration major in Financial Management', 'School of Business (SOB)'),
('BSP', 'Bachelor of Science in Psychology', 'School of Arts, Sciences, & Technology (SOAST)'),
('BLIS', 'Bachelor of Library and Information Science', 'School of Arts, Sciences, & Technology (SOAST)'),
('CPTP', 'Certificate in Professional Teaching Program', 'School of Teacher Education (SOTE)'),
('BSEd - English', 'Bachelor of Secondary Education major in English', 'School of Teacher Education (SOTE)'),
('BSEd - Filipino', 'Bachelor of Secondary Education major in Filipino', 'School of Teacher Education (SOTE)'),
('BSEd - Math', 'Bachelor of Secondary Education major in Mathematics', 'School of Teacher Education (SOTE)'),
('BSEd - Science', 'Bachelor of Secondary Education major in Science', 'School of Teacher Education (SOTE)'),
('BSEd - SocStud', 'Bachelor of Secondary Education major in Social Studies', 'School of Teacher Education (SOTE)'),
('BSEd - VE', 'Bachelor of Secondary Education major in Values Education', 'School of Teacher Education (SOTE)'),
('BECEd', 'Bachelor of Early Childhood Education', 'School of Teacher Education (SOTE)'),
('BSNEd', 'Bachelor of Special Needs Education', 'School of Teacher Education (SOTE)'),
('BPEd', 'Bachelor of Physical Education', 'School of Teacher Education (SOTE)'),
('BTLEd', 'Bachelor of Technical and Livelihood Education major in HE', 'School of Teacher Education (SOTE)'),
('BCAE', 'Bachelor of Culture and Arts Education', 'School of Arts, Sciences, & Technology (SOAST)'),
('BEEd', 'Bachelor of Elementary Education', 'School of Teacher Education (SOTE)'),
('BSIT', 'Bachelor of Science in Information Technology', 'School of Arts, Sciences, & Technology (SOAST)');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` varchar(9) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `year_level` varchar(20) DEFAULT NULL,
  `actions` text DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `full_name`, `email_address`, `contact_no`, `course`, `year_level`, `actions`, `gender`) VALUES
('000000001', 'Rose Lyn Casil', 'casilroselyn08@gmail.com', '09536406772', 'BSIT', '3rd Year', NULL, 'Female'),
('000000002', 'Regina Carla Brosas', 'yhukibrosas09@gmail.com', '09952645899', 'BECEd', '1st Year', NULL, 'Female'),
('000000003', 'a.d.jaslkdjqslk', 'alsdjqpowuepowq@gmail.com', '56154984984', 'BLIS', '2nd Year', NULL, 'Female'),
('000000004', 'dasdasdasd', 'dasdmaskdjaks@gmail.com', '159489746516', 'BECEd', '2nd Year', NULL, 'Female'),
('000000005', 'd1o2u490332jelkq', 'djaoidjiwdjwlkand@gmail.com', '487965423', 'BSA', '2nd Year', NULL, 'Male'),
('000000006', 'damsnhdajhd', 'kdansdoiahsdjah@gmail.com', '778781', 'BSNEd', '2nd Year', NULL, 'Female'),
('000000007', 'daksnndalkshdio', 'anslkdhaisdhaiu@gmail.com', '7895646', 'BSNEd', '2nd Year', NULL, 'Female'),
('000000008', 'dasdhgauigdqiwub', 'dhasyudgashdban@gmail.com', '789456123', 'BECEd', '1st Year', NULL, 'Male'),
('000000009', 'asdkjkagduiygwkdwb', 'bdakjshdgyqwgmnb@gmail.com', '7894531254', 'BSEd - SocStud', '2nd Year', NULL, 'Female'),
('000000010', 'adkjashdkjqhwu', 'dasudytquiwbd@gmail.com', '789456123', 'BSP', '3rd Year', NULL, 'Female'),
('000000011', 'dasndbhqiwuh', 'dabjksdghaiudg@gmail.com', '123123123', 'BSEd - VE', '2nd Year', NULL, 'Female'),
('000000012', 'Len Casil', 'lencasil08@gmail.com', '09536406772', 'BECEd', '3rd Year', NULL, 'Female');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;