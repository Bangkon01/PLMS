-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2026 at 05:31 PM
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
(6, 'จิตวิทยาสังคม', 3, 'อ.ธนาพร ศรีสุข', 2020, '978-616-123-456-6', 'สำนักพิมพ์สังคม', 210, 'จิตวิทยาที่เกี่ยวข้องกับพฤติกรรมมนุษย์ในสังคม', 'ชั้น 3 แถว F', 2, 1, '2026-01-18 16:43:44');

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
(6, 'วรรณกรรม', 'นวนิยายและงานวรรณกรรม', '2026-01-18 16:43:44');

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
  `role` enum('admin','student','teacher','staff') DEFAULT 'student',
  `student_id` varchar(20) DEFAULT NULL,
  `teacher_id` varchar(20) DEFAULT NULL,
  `staff_id` varchar(20) DEFAULT NULL,
  `YEAR` int(11) DEFAULT NULL,
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
(2, 'student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'สมชาย เรียนดี', 'student1@example.com', 'student', 'ST001', NULL, NULL, 3, 'วิศวกรรมคอมพิวเตอร์', NULL, NULL, '081-111-1111', '2026-01-18 16:43:44'),
(3, 'teacher1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ดร.สมศรี สอนดี', 'teacher1@example.com', 'teacher', NULL, 'TC001', NULL, NULL, NULL, 'คณะวิศวกรรมศาสตร์', 'ผู้ช่วยศาสตราจารย์', '082-222-2222', '2026-01-18 16:43:44'),
(4, 'staff1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'นางสมหวัง ทำงาน', 'staff1@example.com', 'staff', NULL, NULL, 'SF001', NULL, NULL, 'งานทะเบียน', 'เจ้าหน้าที่ทะเบียน', '083-333-3333', '2026-01-18 16:43:44');

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
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

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
