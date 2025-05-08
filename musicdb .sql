-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 04 mai 2025 à 13:26
-- Version du serveur : 8.0.31
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `musicdb`
--

-- --------------------------------------------------------

--
-- Structure de la table `likes_dislikes`
--

DROP TABLE IF EXISTS `likes_dislikes`;
CREATE TABLE IF NOT EXISTS `likes_dislikes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_u` int NOT NULL,
  `id_s` int NOT NULL,
  `statut` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_u` (`id_u`),
  KEY `id_s` (`id_s`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `likes_dislikes`
--

INSERT INTO `likes_dislikes` (`id`, `id_u`, `id_s`, `statut`, `created_at`) VALUES
(1, 9, 6, 1, '2025-05-03 21:12:31'),
(2, 9, 7, 1, '2025-05-03 21:12:33'),
(3, 9, 5, 0, '2025-05-03 21:12:39'),
(4, 9, 1, 0, '2025-05-04 08:21:44'),
(5, 9, 2, 0, '2025-05-04 08:21:45'),
(6, 9, 3, 0, '2025-05-04 08:21:47'),
(7, 9, 4, 0, '2025-05-04 08:21:49'),
(8, 14, 2, 0, '2025-05-04 12:43:57'),
(9, 14, 4, 1, '2025-05-04 12:44:00');

-- --------------------------------------------------------

--
-- Structure de la table `playlists`
--

DROP TABLE IF EXISTS `playlists`;
CREATE TABLE IF NOT EXISTS `playlists` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name_p` varchar(50) DEFAULT NULL,
  `id_u` int DEFAULT NULL,
  `cover` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_u` (`id_u`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `playlists`
--

INSERT INTO `playlists` (`id`, `name_p`, `id_u`, `cover`) VALUES
(2, 'Playlist de Kyllian', 3, ''),
(3, 'Playlist de Tom', 2, ''),
(11, 'Playlist de Falvio', 15, 'default-cover.jpg'),
(10, 'Playlist de Arthur', 13, 'default-cover.jpg'),
(9, 'Playlist de Antoine', 12, 'default-cover.jpg'),
(8, 'Playlist de Mathieu', 14, 'default-cover.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `playlist_songs`
--

DROP TABLE IF EXISTS `playlist_songs`;
CREATE TABLE IF NOT EXISTS `playlist_songs` (
  `id_ps` int NOT NULL AUTO_INCREMENT,
  `id_p` int DEFAULT NULL,
  `id_s` int DEFAULT NULL,
  PRIMARY KEY (`id_ps`),
  KEY `id_p` (`id_p`),
  KEY `id_s` (`id_s`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `playlist_songs`
--

INSERT INTO `playlist_songs` (`id_ps`, `id_p`, `id_s`) VALUES
(1, 1, 1),
(2, 1, 3),
(3, 1, 5),
(4, 2, 2),
(5, 2, 4),
(6, 2, 6),
(7, 3, 7),
(8, 3, 8),
(9, 3, 9),
(10, 1, 10),
(11, 2, 1),
(12, 3, 2),
(13, 1, 9),
(14, 1, 7),
(15, 1, 8),
(16, 3, 1);

-- --------------------------------------------------------

--
-- Structure de la table `songs`
--

DROP TABLE IF EXISTS `songs`;
CREATE TABLE IF NOT EXISTS `songs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `singer` varchar(100) DEFAULT NULL,
  `genre` varchar(50) DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `cover` varchar(50) NOT NULL,
  `fic_audio` varchar(100) DEFAULT NULL,
  `plays` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `songs`
--

INSERT INTO `songs` (`id`, `title`, `singer`, `genre`, `duration`, `cover`, `fic_audio`, `plays`) VALUES
(1, 'Shape of You', 'Ed Sheeran', 'Pop', '00:03:53', 'images/shape_of_you.jpg', 'music/Ed Sheeran - Shape of You .mp3', 29),
(2, 'Blinding Lights', 'The Weeknd', 'R&B', '00:03:20', 'images/blinding_lights.jpg', 'music/The Weeknd - Blinding Lights.mp3', 3),
(3, 'Bohemian Rhapsody', 'Queen', 'Rock', '00:05:55', 'images/bohemian_rhapsody.jpg', 'music/Queen  Bohemian Rhapsody.mp3', 2),
(4, 'Someone Like You', 'Adele', 'Pop', '00:04:45', 'images/someone_like_you.jpg', 'music/Adele - Someone Like You.mp3', 1),
(5, 'Bad Guy', 'Billie Eilish', 'Alternative', '00:03:14', 'images/bad_guy.jpg', 'music/Billie Eilish - bad guy.mp3', 2),
(6, 'Smells Like Teen Spirit', 'Nirvana', 'Grunge', '00:05:01', 'images/nirvana.jpg', 'music/Nirvana - Smells Like Teen Spirit.mp3', 18),
(7, 'Uptown Funk', 'Mark Ronson ft. Bruno Mars', 'Funk', '00:04:30', 'images/uptown_funk.jpg', 'music/Mark Ronson - Uptown Funk.mp3', 2),
(8, 'Despacito', 'Luis Fonsi ft. Daddy Yankee', 'Reggaeton', '00:03:48', 'images/despacito.jpg', 'music/Luis Fonsi - Despacito ft. Daddy Yankee.mp3', 1),
(9, 'Billie Jean', 'Michael Jackson', 'Pop', '00:04:54', 'images/billie_jean.jpg', 'music/Michael Jackson - Billie Jean.mp3', 2),
(10, 'Rolling in the Deep', 'Adele', 'Pop', '00:03:48', 'images/rolling_in_the_deep.jpg', 'music/Adele - Rolling in the Deep.mp3', 1),
(11, 'Next summer', 'Damiano David', 'Pop', '00:05:52', 'images/next_summer.jpg', 'music/Damiano David  Next Summer.mp3', 1),
(13, 'Lose yourself', 'Eminem', 'rap', '00:05:28', 'images/lose_yourself.jpg', 'music/Eminem  Lose Yourself.mp3', 2);

-- --------------------------------------------------------

--
-- Structure de la table `subscribe`
--

DROP TABLE IF EXISTS `subscribe`;
CREATE TABLE IF NOT EXISTS `subscribe` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name_s` varchar(50) DEFAULT NULL,
  `price` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `subscribe`
--

INSERT INTO `subscribe` (`id`, `name_s`, `price`) VALUES
(1, 'free', 0),
(2, 'student', 5.5),
(3, 'premium', 10),
(4, 'duo premium', 12.5),
(5, 'family', 15);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name_u` varchar(50) DEFAULT NULL,
  `surname` varchar(50) DEFAULT NULL,
  `mail` varchar(50) DEFAULT NULL,
  `mdp` varchar(50) DEFAULT NULL,
  `id_s` int DEFAULT NULL,
  `mail_academique` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_s` (`id_s`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name_u`, `surname`, `mail`, `mdp`, `id_s`, `mail_academique`) VALUES
(2, 'Cazin', 'Tom', 'totom.cazin8@gmail.com', '', 1, ''),
(3, 'Garnier', 'Kyllian', 'kylliangarnier2006@gmail.com', '', 1, ''),
(14, 'Dupressoir', 'Mathieu', 'mdinternet03@gmail.com', '5ac2390c610c28721c4c1d8d07f5f037', 2, 'mathieu.dupressoir@univ.fr'),
(12, 'Dupont', 'Antoine', 'antoine.dupont@gmail.com', '5ac2390c610c28721c4c1d8d07f5f037', NULL, ''),
(13, 'Gerlain', 'Arthur', 'gerlain.arthur@gmail.com', 'c4480afb781c8b55f7a64867e221a0f2', NULL, ''),
(15, 'Cunto', 'Falvio', 'cunto.falvio@gmail.com', '5ac2390c610c28721c4c1d8d07f5f037', NULL, '');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
