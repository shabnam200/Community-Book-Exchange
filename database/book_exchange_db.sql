-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 13, 2025 at 03:16 PM
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
-- Database: `book_exchange_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `condition_status` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) NOT NULL,
  `owner_id` int(11) UNSIGNED NOT NULL,
  `is_available` smallint(6) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `genre`, `condition_status`, `description`, `cover_image`, `owner_id`, `is_available`, `created_at`) VALUES
(1, 'The Catcher in the Rye', 'J. D. (Jerome David) Salinger', 'Fiction', 'Used', 'The beautiful parts and lines are highlighted', 'assets/book_covers/692c943b3f583.png', 1, 1, '2025-11-30 19:00:11'),
(2, 'শিকড়ের সন্ধানে', 'হামিদা মুবাশ্বেরা', 'Islamic', 'New', 'Talks about roots of islam', 'assets/book_covers/692e8a86599a7.png', 2, 1, '2025-12-02 06:43:18'),
(3, 'Purification of the Heart', 'Hamza Yusuf', 'Spirituality / Tazkiyah', 'Used', '', 'assets/book_covers/693028d6ca597.png', 1, 1, '2025-12-03 12:11:02'),
(4, 'Riyad as-Salihin (Gardens of the Righteous)', 'Imam an-Nawawi', 'Hadith Collection', 'Used', '', 'assets/book_covers/6930294b0fc7f.png', 1, 1, '2025-12-03 12:12:59'),
(5, 'Inner Dimensions of Islamic Worship', 'Al-Ghazali', 'Fiqh / Spirituality', 'Used', '', 'assets/book_covers/693029e71ffd4.png', 2, 1, '2025-12-03 12:15:35'),
(6, 'The Masnavi', 'Jalāl ad-Dīn Muhammad Rūmī', 'Sufism, Divine Love, Mystic Allegories', 'Used', 'Language of origin is Persian', 'assets/book_covers/69302aac83e72.png', 2, 1, '2025-12-03 12:18:52'),
(7, 'The Burda (Qasīdat al-Burda)', 'Imam al-Busiri', 'Praise of the Prophet Muhammad (PBUH)', 'Good', 'Language of origin is Arabic', 'assets/book_covers/69302b4c4ed31.png', 2, 1, '2025-12-03 12:21:32'),
(8, 'Inner Dimensions of Islamic Worship', 'Al-Ghazali', 'Spirituality / Tazkiyah', 'Used', '', 'assets/book_covers/693672b68701c.png', 2, 1, '2025-12-08 06:39:50');

-- --------------------------------------------------------

--
-- Table structure for table `exchange_messages`
--

CREATE TABLE `exchange_messages` (
  `message_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `sent_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exchange_requests`
--

CREATE TABLE `exchange_requests` (
  `request_id` int(11) NOT NULL,
  `requested_book_id` int(11) NOT NULL,
  `requester_user_id` int(11) UNSIGNED NOT NULL,
  `owner_user_id` int(11) UNSIGNED NOT NULL,
  `status` enum('pending','accepted','rejected','completed') DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exchange_requests`
--

INSERT INTO `exchange_requests` (`request_id`, `requested_book_id`, `requester_user_id`, `owner_user_id`, `status`, `requested_at`) VALUES
(1, 1, 2, 1, 'accepted', '2025-12-02 06:39:26'),
(2, 2, 1, 2, 'accepted', '2025-12-02 07:02:14'),
(3, 1, 2, 1, 'accepted', '2025-12-03 12:04:34'),
(4, 5, 1, 2, 'accepted', '2025-12-03 12:40:41'),
(5, 4, 2, 1, 'accepted', '2025-12-07 02:47:53'),
(6, 3, 2, 1, 'accepted', '2025-12-07 03:22:58'),
(7, 5, 1, 2, 'pending', '2025-12-07 10:18:26'),
(8, 2, 1, 2, 'accepted', '2025-12-07 10:18:31');

-- --------------------------------------------------------
--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password_hash`, `created_at`) VALUES
(1, 'shabnam edris', 'shabnammasuma674@gmail.com', '$2y$10$3F/tH.H5D6hmR72v3Axig..25bW.FQZoPgBvRfTx80DLJg3VM9eA2', '2025-11-30 18:29:13'),
(2, 'abc', 'abc123@gmail.com', '$2y$10$4yo.jMX3DAskrWPquPh/z.NPgkl9Hk.8apdhpz4jOMLC1b.NHiEn.', '2025-12-02 06:39:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `exchange_messages`
--
ALTER TABLE `exchange_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `exchange_requests`
--
ALTER TABLE `exchange_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `requested_book_id` (`requested_book_id`),
  ADD KEY `requester_user_id` (`requester_user_id`),
  ADD KEY `owner_user_id` (`owner_user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `exchange_messages`
--
ALTER TABLE `exchange_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exchange_requests`
--
ALTER TABLE `exchange_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exchange_requests`
--
ALTER TABLE `exchange_requests`
  ADD CONSTRAINT `exchange_requests_ibfk_1` FOREIGN KEY (`requested_book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exchange_requests_ibfk_2` FOREIGN KEY (`requester_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exchange_requests_ibfk_3` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
