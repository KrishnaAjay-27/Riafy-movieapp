-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: sql12.freesqldatabase.com
-- Generation Time: Mar 17, 2025 at 07:56 AM
-- Server version: 5.5.62-0ubuntu0.14.04.1
-- PHP Version: 7.0.33-0ubuntu0.16.04.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sql12768077`
--

-- --------------------------------------------------------

--
-- Table structure for table `favorite_movies`
--

CREATE TABLE `favorite_movies` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` varchar(255) NOT NULL,
  `movie_title` varchar(255) NOT NULL,
  `poster_url` varchar(255) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `list_name` varchar(255) NOT NULL DEFAULT 'Favorites'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `favorite_movies`
--

INSERT INTO `favorite_movies` (`id`, `user_id`, `movie_id`, `movie_title`, `poster_url`, `added_at`, `list_name`) VALUES
(3, 1, 'tt0109830', 'Forrest Gump', 'https://m.media-amazon.com/images/M/MV5BNDYwNzVjMTItZmU5YS00YjQ5LTljYjgtMjY2NDVmYWMyNWFmXkEyXkFqcGc@._V1_SX300.jpg', '2025-03-17 05:37:09', 'horror'),
(4, 1, 'tt0816692', 'Interstellar', 'https://m.media-amazon.com/images/M/MV5BYzdjMDAxZGItMjI2My00ODA1LTlkNzItOWFjMDU5ZDJlYWY3XkEyXkFqcGc@._V1_SX300.jpg', '2025-03-17 05:40:47', 'My Favorites'),
(5, 1, 'tt0062759', 'The Diamond Arm', 'https://m.media-amazon.com/images/M/MV5BOGY3NjE0YmItNGQ4OS00OGY1LWI3MjItOWU5OTBkN2QyNTI2XkEyXkFqcGc@._V1_SX300.jpg', '2025-03-17 05:42:04', 'My Favorites');

-- --------------------------------------------------------

--
-- Table structure for table `movies_lists`
--

CREATE TABLE `movies_lists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'Krishna Ajay', 'krishna.ajaya2@gmail.com', '$2y$10$ZxCOrSgQdc3lzDxdmdJeN.PEIF.Xom/tABBaubRamwWtDvASCC4fi', '2025-03-17 04:12:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorite_movies`
--
ALTER TABLE `favorite_movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movies_lists`
--
ALTER TABLE `movies_lists`
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
-- AUTO_INCREMENT for table `favorite_movies`
--
ALTER TABLE `favorite_movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `movies_lists`
--
ALTER TABLE `movies_lists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
