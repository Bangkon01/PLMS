-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 31, 2026 at 11:27 AM
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
-- Database: `plms_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `author` varchar(255) NOT NULL,
  `YEAR` int(11) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `pages` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `available` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `category_id`, `author`, `YEAR`, `isbn`, `publisher`, `pages`, `description`, `location`, `quantity`, `available`, `created_at`) VALUES
(1, 'วิทยานิพนธ์เรื่องการพัฒนาเว็บไซต์', 1, 'สมชาย ใจดี', 2023, '978-616-123-456-1', 'สำนักพิมพ์มหาวิทยาลัย', 150, 'วิทยานิพนธ์เกี่ยวกับการพัฒนาเว็บไซต์ด้วยเทคโนโลยีสมัยใหม่', 'ชั้น 1 แถว A', 3, 3, '2026-01-18 16:43:44'),
(2, 'ประวัติศาสตร์สุโขทัย', 2, 'วิไลลักษณ์ สุวรรณ', 2020, '978-616-123-456-2', 'สำนักพิมพ์ประวัติศาสตร์', 200, 'บันทึกประวัติศาสตร์ราชอาณาจักรสุโขทัยอย่างละเอียด', 'ชั้น 2 แถว B', 2, 2, '2026-01-18 16:43:44'),
(3, 'จิตวิทยาการเรียนรู้', 3, 'ดร.วิชัย พัฒนะ', 2021, '978-616-123-456-3', 'สำนักพิมพ์จิตวิทยา', 180, 'หนังสือจิตวิทยาเกี่ยวกับกระบวนการเรียนรู้ของมนุษย์', 'ชั้น 3 แถว C', 4, 3, '2026-01-18 16:43:44'),
(4, 'การวิจัยเชิงคุณภาพ', 1, 'รศ.ดร.สุนีย์ วัฒนา', 2022, '978-616-123-456-4', 'สำนักพิมพ์วิจัย', 220, 'หนังสือเกี่ยวกับวิธีการวิจัยเชิงคุณภาพ', 'ชั้น 1 แถว D', 2, 2, '2026-01-18 16:43:44'),
(5, 'ประวัติศาสตร์ล้านนา', 2, 'ธีรพงศ์ ณ เชียงใหม่', 2019, '978-616-123-456-5', 'สำนักพิมพ์ล้านนา', 190, 'บันทึกประวัติศาสตร์อาณาจักรล้านนา', 'ชั้น 2 แถว E', 3, 3, '2026-01-18 16:43:44'),
(6, 'จิตวิทยาสังคม', 3, 'อ.ธนาพร ศรีสุข', 2020, '978-616-123-456-6', 'สำนักพิมพ์สังคม', 210, 'จิตวิทยาที่เกี่ยวข้องกับพฤติกรรมมนุษย์ในสังคม', 'ชั้น 3 แถว F', 2, 1, '2026-01-18 16:43:44'),
(7, 'PLMS-SYSTEM', 2, 'ฟุรกอน', 2026, 'BT1001', 'ฟุรกอน', 50, '', '', 1, 1, '2026-01-20 02:49:45'),
(10, 'เว็บไซต์แจ้งซ่อมและอุปกรณ์ต่อพ่วง', 1, 'นายแทนเทพ  แก้วทอง', 2026, 'BT1002', 'วิทยาลัยอาชีวศึกษาปัตตานี', 86, 'เกี่ยวกับการแจ้งซ่อมของวิทยาลัยอาชีวศึกษาปัตตานี', 'ชั้น 2 แถว B', 1, 1, '2026-01-28 08:14:56');

-- --------------------------------------------------------

--
-- Table structure for table `borrow_requests`
--

CREATE TABLE `borrow_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `request_date` datetime DEFAULT current_timestamp(),
  `borrow_date` date NOT NULL,
  `expected_return_date` date NOT NULL,
  `status` enum('pending','approved','rejected','cancelled') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `borrow_requests`
--

INSERT INTO `borrow_requests` (`id`, `user_id`, `book_id`, `request_date`, `borrow_date`, `expected_return_date`, `status`, `approved_by`, `approved_date`, `rejection_reason`, `created_at`) VALUES
(1, 2, 4, '2026-01-20 05:30:25', '2026-01-19', '2026-01-26', 'pending', NULL, NULL, NULL, '2026-01-19 22:30:25'),
(2, 2, 6, '2026-01-20 09:42:15', '2026-01-20', '2026-01-27', 'pending', NULL, NULL, NULL, '2026-01-20 02:42:15'),
(3, 2, 1, '2026-01-20 09:46:09', '2026-01-20', '2026-01-27', 'pending', NULL, NULL, NULL, '2026-01-20 02:46:09'),
(4, 2, 7, '2026-01-28 11:51:20', '2026-01-28', '2026-02-04', 'pending', NULL, NULL, NULL, '2026-01-28 04:51:20'),
(6, 2, 3, '2026-01-30 14:27:49', '2026-01-30', '2026-02-06', 'pending', NULL, NULL, NULL, '2026-01-30 07:27:49'),
(7, 2, 5, '2026-01-31 02:21:15', '2026-01-30', '2026-02-06', 'pending', NULL, NULL, NULL, '2026-01-30 19:21:15');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `NAME` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `NAME`, `description`, `created_at`) VALUES
(1, 'วิทยานิพนธ์วิจัย', 'วิทยานิพนธ์และงานวิจัยทางวิชาการ', '2026-01-18 16:43:44'),
(2, 'พงศาวดาร', 'หนังสือประวัติศาสตร์และพงศาวดาร', '2026-01-18 16:43:44'),
(3, 'จิตวิทยา', 'หนังสือจิตวิทยาและการพัฒนาตนเอง', '2026-01-18 16:43:44'),
(4, 'วิทยาศาสตร์', 'หนังสือวิทยาศาสตร์ทั่วไป', '2026-01-18 16:43:44'),
(5, 'เทคโนโลยี', 'หนังสือเทคโนโลยีและคอมพิวเตอร์', '2026-01-18 16:43:44'),
(6, 'วรรณกรรม', 'นวนิยายและงานวรรณกรรม', '2026-01-18 16:43:44'),
(7, 'นิทาน', 'หนังสือนิทานทั่วไป', '2026-01-28 08:27:09');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`) VALUES
(1, 'library_name', 'วิทยาลัยเทคนิคแห่งหนึ่ง', '2026-01-18 16:43:44'),
(2, 'max_borrow_days', '7', '2026-01-18 16:43:44'),
(3, 'max_books_per_user', '5', '2026-01-18 16:43:44'),
(4, 'late_fee_per_day', '10', '2026-01-18 16:43:44'),
(5, 'opening_time', '08:30', '2026-01-18 16:43:44'),
(6, 'closing_time', '16:30', '2026-01-18 16:43:44');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` varchar(20) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `borrow_date` date NOT NULL,
  `expected_return_date` date NOT NULL,
  `actual_return_date` date DEFAULT NULL,
  `STATUS` enum('borrowed','returned') DEFAULT 'borrowed',
  `late_fee` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `book_id`, `user_id`, `borrow_date`, `expected_return_date`, `actual_return_date`, `STATUS`, `late_fee`, `created_at`) VALUES
('TR0001000', 3, 2, '2023-10-01', '2023-10-08', NULL, 'borrowed', 0.00, '2026-01-18 16:43:44'),
('TR0001001', 6, 3, '2023-10-05', '2023-10-12', NULL, 'borrowed', 0.00, '2026-01-18 16:43:44'),
('TR0001002', 1, 4, '2023-09-20', '2023-09-27', NULL, 'returned', 0.00, '2026-01-18 16:43:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','librarian','student','teacher','staff') DEFAULT 'student',
  `student_id` varchar(20) DEFAULT NULL,
  `teacher_id` varchar(20) DEFAULT NULL,
  `staff_id` varchar(20) DEFAULT NULL,
  `YEAR` varchar(5) DEFAULT NULL,
  `major` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `POSITION` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `NAME`, `email`, `role`, `student_id`, `teacher_id`, `staff_id`, `YEAR`, `major`, `department`, `POSITION`, `phone`, `created_at`) VALUES
(1, 'admin', '$2y$10$IzX7et2BWWZOXXVIyeRJIOSlr5120eSRO2lmHEf8YYAmFbWGtJJPe', 'ผู้ดูแลระบบ', 'admin@plms.local', 'admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-18 16:43:44'),
(2, 'student1', '$2y$10$M8T5oFNSOlqeAcj8J5BLf.8MPJ6pu2KkW2hS7aR2gGL5ly10JN4oi', 'แทนเทพ  แก้วทอง', 'student1@example.com', 'student', '67319010014', NULL, NULL, '0', 'วิศวกรรมคอมพิวเตอร์', NULL, NULL, '081-111-1111', '2026-01-18 16:43:44'),
(3, 'teacher1', '$2y$10$9OkiSU1pWTKtgrUxxFmQHuxi3NkrApPabPi3Z08HVCsb1cDo3vsh2', 'นางสาวโนรลี มะ', 'teacher1@example.com', 'teacher', NULL, 'TC001', NULL, NULL, NULL, 'คณะอุตสาหกรรมเทคโนโลยีและธุรกิจดิจิทัล', 'อาจารย์', '082-222-2222', '2026-01-18 16:43:44'),
(4, 'Alfar007x', '$2y$10$2yJTMBMPd2a.XLnN4NOMgObAI7kAaie9v8uSy/lkyJCAMSeiYiGF2', 'นายนิอัลฟาร์  สันตาบัน', 'Alfar007x@example.com', 'staff', NULL, NULL, 'SF001', NULL, NULL, 'งานวิชาการ', 'หัวหน้าแผนก', '083-333-3333', '2026-01-18 16:43:44'),
(7, 'นายฟุรกอน  นิหมะ', '$2y$10$q.Fzg2QVdxMxLKmxmfMIz.3FzzulnG4NxZD3vYSat/.uUZxjnXlzm', 'นายฟุรกอน  นิหมะ', 'Frukon321@gmail.com', 'student', '67319010020', NULL, NULL, '0', 'เทคโนโลยีสารสนเทศและการสื่อสาร', NULL, NULL, '0991856930', '2026-01-30 15:22:51'),
(8, 'KruBank', '$2y$10$Uu7Bw6FiUHUjCXjs/OzQ5ewyWSacg1cv8lOLOdvR74drposw4duLu', 'พศิณ รัตนอุบลธนา', 'Krubankvr@gmail.com', 'student', '67319010026', NULL, NULL, '0', 'เทคโนโลยีสารสนเทศและการสื่อสาร', NULL, NULL, '0826951452', '2026-01-30 15:37:43'),
(9, 'Chonnapat', '$2y$10$IuJklkV//kWOJejjvJApY.Ixxwc2ilnqc6J18gLw8qgjbO.9mZ/Vq', 'นายชนพัฒน์  ขวัญศรีเมือง', 'Chonnapat609@gmail.com', 'staff', NULL, NULL, 'SF002', NULL, NULL, 'งานกิจการนักศึกษา', 'หัวหน้าแผนก', '0893412859', '2026-01-30 18:01:00'),
(10, 'Sunwanee', '$2y$10$8eALRWuyAe.ZJh3ppSxYm.wKJSYUEcT/VBsf1bGC28gRDmJj2RdQW', 'นางสาวซัลวานี  หมัดมะ', 'Sunwanee609@gmail.com', 'staff', NULL, NULL, 'SF003', NULL, NULL, 'งานอาคารสถานที่', 'นักจัดการงานทั่วไป', '0984152034', '2026-01-30 18:18:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `NAME` (`NAME`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  ADD CONSTRAINT `borrow_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `borrow_requests_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `borrow_requests_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
