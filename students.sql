-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 24, 2024 at 03:16 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `students`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` int(11) NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` datetime NOT NULL,
  `max_score` decimal(5,2) DEFAULT 100.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`assignment_id`, `subject_id`, `teacher_id`, `title`, `description`, `due_date`, `max_score`, `created_at`) VALUES
(1, 13, 14, 'Assignment 1', NULL, '2024-03-01 00:00:00', 100.00, '2024-11-24 08:41:07'),
(2, 14, 15, 'Assignment 2', NULL, '2024-03-01 00:00:00', 100.00, '2024-11-24 08:41:07'),
(3, 15, 16, 'Assignment 3', NULL, '2024-03-01 00:00:00', 100.00, '2024-11-24 08:41:07'),
(4, 16, 17, 'Assignment 4', NULL, '2024-03-01 00:00:00', 100.00, '2024-11-24 08:41:07'),
(5, 17, 18, 'Assignment 5', NULL, '2024-03-01 00:00:00', 100.00, '2024-11-24 08:41:07');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('assignment','grade','announcement','other') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 3, 'Welcome to the new semester!', 'Welcome to the 2024 academic year. Please check your course schedule.', 'announcement', 0, '2024-11-23 12:45:30'),
(2, 4, 'Welcome to the new semester!', 'Welcome to the 2024 academic year. Please check your course schedule.', 'announcement', 0, '2024-11-23 12:45:30'),
(3, 5, 'Welcome to the new semester!', 'Welcome to the 2024 academic year. Please check your course schedule.', 'announcement', 0, '2024-11-23 12:45:30');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `enrollment_date` date NOT NULL,
  `graduation_year` year(4) DEFAULT NULL,
  `status` enum('active','inactive','graduated') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `enrollment_date`, `graduation_year`, `status`) VALUES
(3, '2023-09-01', '2027', 'active'),
(4, '2023-09-01', '2027', 'active'),
(5, '2023-09-01', '2027', 'active'),
(13, '2024-11-23', '2028', 'active'),
(19, '2024-11-24', '2028', 'active'),
(20, '2024-11-24', '2028', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `student_assignments`
--

CREATE TABLE `student_assignments` (
  `student_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `submission_date` datetime DEFAULT NULL,
  `submission_text` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `status` enum('pending','submitted','graded','late') DEFAULT 'pending',
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_assignments`
--

INSERT INTO `student_assignments` (`student_id`, `assignment_id`, `submission_date`, `submission_text`, `file_path`, `score`, `status`, `feedback`) VALUES
(13, 1, '2024-02-28 00:00:00', 'Submission text for Assignment 1', '/path/to/file1', 33.00, 'submitted', 'Good job'),
(13, 2, '2024-02-28 00:00:00', 'Submission text for Assignment 2', '/path/to/file2', 40.00, 'submitted', 'Well done'),
(13, 3, '2024-02-28 00:00:00', 'Submission text for Assignment 3', '/path/to/file3', 56.00, 'submitted', 'Excellent work'),
(13, 4, '2024-02-28 00:00:00', 'Submission text for Assignment 4', '/path/to/file4', 81.00, 'submitted', 'Nice effort'),
(13, 5, '2024-02-28 00:00:00', 'Submission text for Assignment 5', '/path/to/file5', 75.00, 'submitted', 'Great job');

-- --------------------------------------------------------

--
-- Table structure for table `student_subjects`
--

CREATE TABLE `student_subjects` (
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `academic_year` year(4) NOT NULL,
  `semester` enum('1','2','3','4') NOT NULL,
  `grade` decimal(5,2) DEFAULT NULL,
  `status` enum('enrolled','completed','withdrawn') DEFAULT 'enrolled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_subjects`
--

INSERT INTO `student_subjects` (`student_id`, `subject_id`, `teacher_id`, `academic_year`, `semester`, `grade`, `status`) VALUES
(3, 1, 1, '2024', '1', NULL, 'enrolled'),
(3, 2, 2, '2024', '1', NULL, 'enrolled'),
(3, 3, 1, '2024', '1', NULL, 'enrolled'),
(3, 4, 2, '2024', '1', NULL, 'enrolled'),
(4, 1, 1, '2024', '1', NULL, 'enrolled'),
(4, 2, 2, '2024', '1', NULL, 'enrolled'),
(4, 3, 1, '2024', '1', NULL, 'enrolled'),
(4, 4, 2, '2024', '1', NULL, 'enrolled'),
(5, 1, 1, '2024', '1', NULL, 'enrolled'),
(5, 2, 2, '2024', '1', NULL, 'enrolled'),
(5, 3, 1, '2024', '1', NULL, 'enrolled'),
(5, 4, 2, '2024', '1', NULL, 'enrolled'),
(13, 13, 14, '2024', '1', 52.00, 'enrolled'),
(13, 14, 15, '2024', '1', 44.00, 'enrolled'),
(13, 15, 16, '2024', '1', 71.00, 'enrolled'),
(13, 16, 17, '2024', '1', 65.00, 'enrolled'),
(13, 17, 18, '2024', '1', 100.00, 'enrolled');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `credits` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `description`, `credits`, `created_at`) VALUES
(1, 'MATH101', 'Mathematics', 'Basic mathematics and algebra', 3, '2024-11-23 12:45:30'),
(2, 'ENG101', 'English', 'English language and literature', 3, '2024-11-23 12:45:30'),
(3, 'CS101', 'Computer Science', 'Introduction to programming', 4, '2024-11-23 12:45:30'),
(4, 'PHY101', 'Physics', 'Basic physics concepts', 3, '2024-11-23 12:45:30'),
(13, 'WIN10', 'Windows 10', NULL, 3, '2024-11-24 08:19:26'),
(14, 'ITE', 'IT Essentials', NULL, 4, '2024-11-24 08:19:26'),
(15, 'MYSQL', 'MySQL', NULL, 3, '2024-11-24 08:19:26'),
(16, 'LINUX', 'Linux', NULL, 3, '2024-11-24 08:19:26'),
(17, 'WINSRV', 'Windows Server', NULL, 4, '2024-11-24 08:19:26');

-- --------------------------------------------------------

--
-- Table structure for table `subject_teachers`
--

CREATE TABLE `subject_teachers` (
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `academic_year` year(4) NOT NULL,
  `semester` enum('1','2','3','4') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject_teachers`
--

INSERT INTO `subject_teachers` (`subject_id`, `teacher_id`, `academic_year`, `semester`) VALUES
(1, 1, '2024', '1'),
(2, 2, '2024', '1'),
(3, 1, '2024', '1'),
(4, 2, '2024', '1'),
(13, 14, '2024', '1'),
(14, 15, '2024', '1'),
(15, 16, '2024', '1'),
(16, 17, '2024', '1'),
(17, 18, '2024', '1');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `department` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `department`) VALUES
(1, 'General Studies'),
(2, 'General Studies'),
(14, 'Information Technology'),
(15, 'Information Technology'),
(16, 'Information Technology'),
(17, 'Information Technology'),
(18, 'Information Technology');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `service_number` varchar(50) DEFAULT NULL,
  `rank` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','teacher','admin') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `service_number`, `rank`, `name`, `email`, `phone_number`, `password`, `role`, `created_at`) VALUES
(1, 'T101', 'Professor', 'John Smith', 'john.smith@school.com', '1234567890', '$2y$10$Im4nRDzDlKF2GkAZzH5b1ufmgh5ceYjeDv4pnteIqB3eUiEjJi8Nu', 'teacher', '2024-11-23 12:45:30'),
(2, 'T102', 'Dr', 'Sarah Johnson', 'sarah.j@school.com', '1234567891', '$2y$10$6sW0/iFSswsEZlXIMIIuj.RiuAEd/sDzso0CKF4fsI6D6rLz0ZHQ6', 'teacher', '2024-11-23 12:45:30'),
(3, 'S101', 'Student', 'Mike Wilson', 'mike.w@school.com', '1234567892', '$2y$10$Er//QJ4aqOOxryCiNYBq5OdocClSNccWrDzmq8K2oNGyEzEly3Ih.', 'student', '2024-11-23 12:45:30'),
(4, 'S102', 'Student', 'Emma Davis', 'emma.d@school.com', '1234567893', '$2y$10$XB7BH29sjOeHtBqbNExAKOh5R.2B1aj6xmSRWQnuHF.oOeb0MyG0S', 'student', '2024-11-23 12:45:30'),
(5, 'S103', 'Student', 'James Brown', 'james.b@school.com', '1234567894', '$2y$10$yOXKgOECyWv2k38UuvTDEOKMLdCkCY12KemzIAHc3.BOezLa/D4m.', 'student', '2024-11-23 12:45:30'),
(13, '163633', 'SPTE', 'Kennedy Mwaura', 'kemwaura@gmail.com', '0725643244', '$2y$10$I5uWNGfN9K5BPjgQbCO9teBRuTtyy3AFYOa84ogwaT./EqAqoESwe', 'student', '2024-11-23 13:48:59'),
(14, '113745', 'CPL', 'Okwakau', 'okwakau@example.com', '123-456-7890', 'password123', 'teacher', '2024-11-24 08:13:39'),
(15, '113548', 'CPL', 'Musoja', 'musoja@example.com', '123-456-7891', 'password123', 'teacher', '2024-11-24 08:13:39'),
(16, '75214', 'WOII', 'Analo', 'analo@example.com', '123-456-7892', 'password123', 'teacher', '2024-11-24 08:13:39'),
(17, '79854', 'WOII', 'Musa', 'musa@example.com', '123-456-7893', 'password123', 'teacher', '2024-11-24 08:13:39'),
(18, '101745', 'SSGT', 'Ongeri', 'ongeri@example.com', '123-456-7894', 'password123', 'teacher', '2024-11-24 08:13:39'),
(19, '167637', 'PTE', 'Brian Kolum', 'bk@gmail.com', '0704359457', '$2y$10$zRQG12Ar8yd6fkmbbK2up.m5N6jXxrHKELvKwgNIRv9pI0KVudGzG', 'student', '2024-11-24 09:50:53'),
(20, '114256', 'PTE', 'Hillary Kaunda', 'hk@gmail.com', '0791536850', '$2y$10$j5GDLsMUIiJv8Hp6TQ2E/.iJyf30bjE35S588RjPKlRdXUUQKKhCm', 'student', '2024-11-24 09:56:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `student_assignments`
--
ALTER TABLE `student_assignments`
  ADD PRIMARY KEY (`student_id`,`assignment_id`),
  ADD KEY `assignment_id` (`assignment_id`);

--
-- Indexes for table `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD PRIMARY KEY (`student_id`,`subject_id`,`academic_year`,`semester`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`);

--
-- Indexes for table `subject_teachers`
--
ALTER TABLE `subject_teachers`
  ADD PRIMARY KEY (`subject_id`,`teacher_id`,`academic_year`,`semester`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `service_number` (`service_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_assignments`
--
ALTER TABLE `student_assignments`
  ADD CONSTRAINT `student_assignments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_assignments_ibfk_2` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`assignment_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD CONSTRAINT `student_subjects_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_subjects_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_teachers`
--
ALTER TABLE `subject_teachers`
  ADD CONSTRAINT `subject_teachers_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_teachers_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
