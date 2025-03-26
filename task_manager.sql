-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 26, 2025 at 02:52 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `task_manager`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `status` enum('pending','completed') DEFAULT 'pending',
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `title`, `description`, `status`, `due_date`, `created_at`) VALUES
(56, 3, 'todays update', 'we can start class after eid', 'completed', '2025-03-31', '2025-03-23 18:03:49'),
(58, 3, 'Graph API', 'How Facebook Graph API Works\r\nFacebook Graph API is a powerful tool that allows developers to interact with Facebook\'s platform programmatically. It enables fetching data, posting content, and managing pages.\r\n\r\nBasic Workflow for Posting to a Page\r\nGet a Page Access Token\r\n\r\nUse Facebook\'s Graph API Explorer or OAuth to obtain a long-lived page access token.\r\n\r\nSet API Endpoint\r\n\r\nUse the URL:\r\n\r\nbash\r\nCopy\r\nEdit\r\nhttps://graph.facebook.com/v18.0/{PAGE_ID}/feed\r\nReplace {PAGE_ID} with your actual page ID.\r\n\r\nSend a POST Request\r\n\r\nInclude parameters like:\r\n\r\njson\r\nCopy\r\nEdit\r\n{\r\n  \"message\": \"Your post content\",\r\n  \"access_token\": \"YOUR_PAGE_ACCESS_TOKEN\"\r\n}\r\nYou can send this request using cURL, Postman, or a PHP script.\r\n\r\nHandle the Response\r\n\r\nIf successful, Facebook returns a post_id.\r\n\r\nIf there\'s an error, check for missing permissions or invalid tokens.\r\n\r\nKey Permissions Required\r\npages_manage_posts → Allows posting on pages.\r\n\r\npages_read_engagement → Enables reading page interactions.', 'completed', '2025-03-31', '2025-03-23 18:15:19'),
(60, 3, 'Fix Login Issue', 'Resolve the login bug for registered users.', 'pending', '2025-03-26', '2025-03-26 14:12:45'),
(61, 3, 'Add Reminder Feature', 'Implement a reminder for due tasks.', 'pending', '2025-03-27', '2025-03-26 14:13:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(3, 'Apel', 'apel@gmail.com', '$2y$10$IVpdDn6tZbHN8zdOB.P7I.CFUVq/geLxdyhgW.FgRthssi8ebrKG2', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
