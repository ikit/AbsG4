-- phpMyAdmin SQL Dump
-- version 4.6.5
-- https://www.phpmyadmin.net/
--
-- Client :  localhost
-- Généré le :  Lun 24 Avril 2017 à 23:45
-- Version du serveur :  5.5.53-MariaDB
-- Version de PHP :  5.6.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `absg`
--

-- --------------------------------------------------------

--
-- Structure de la table `absg_current_data`
--

CREATE TABLE `absg_current_data` (
  `key` varchar(50) NOT NULL,
  `value` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `absg_current_data`
--

INSERT INTO `absg_current_data` (`key`, `value`) VALUES
('agpa_phase_boundaries', '1/1-24/12-26/12-27/12-28/12-30/12'),
('log_last_autocheck', '1492989352'),
('site_offline', ''),
('stat_max_user_date', '1381760130'),
('stat_max_user_online', '8'),
('stat_max_visitor_by_day', '19'),
('stat_max_visitor_date', '1387926000');

-- --------------------------------------------------------

--
-- Structure de la table `absg_daily_presence`
--

CREATE TABLE `absg_daily_presence` (
  `date` int(8) NOT NULL,
  `user_id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `absg_logs`
--

CREATE TABLE `absg_logs` (
  `user_id` int(5) NOT NULL,
  `date` int(8) NOT NULL,
  `type` enum('error','warning','message','') NOT NULL,
  `module` enum('absg','citation','immt','forum','agpa','agenda','web3g','cultureg','gtheque','wikig','olympiages','grenier','birthday') NOT NULL,
  `message` varchar(255) NOT NULL,
  `url` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `absg_ranks`
--

CREATE TABLE `absg_ranks` (
  `code` varchar(2) NOT NULL,
  `title` varchar(20) NOT NULL,
  `g_note` int(3) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `absg_ranks`
--

INSERT INTO `absg_ranks` (`code`, `title`, `g_note`, `active`) VALUES
('00', 'Fi G', 0, 1),
('01', 'G nèse', 2, 1),
('02', 'G né', 4, 1),
('03', 'G volue', 6, 1),
('04', 'Miti G', 8, 1),
('05', 'Emer G', 10, 1),
('06', 'Boue G', 12, 1),
('07', 'G\'sperd', 14, 1),
('08', 'G zite', 16, 1),
('09', 'Soula G', 18, 1),
('10', 'G ponge', 20, 1),
('11', 'G xplore', 22, 1),
('12', 'G latine', 24, 1),
('13', 'G touffe', 26, 1),
('14', 'G trip', 28, 0),
('15', 'G ricanne', 30, 0),
('16', 'Ah ! L\'G ri !', 32, 0),
('17', 'New zehr G', 34, 0),
('18', 'G orgie', 36, 0),
('19', 'G néreux', 38, 0),
('20', 'G tset', 40, 0),
('21', 'T.G.V.', 42, 0),
('22', 'Herr G', 44, 0),
('23', 'G ronimo', 46, 0),
('24', 'Absolument G', 48, 0),
('25', 'G\'ssy James', 50, 0),
('26', 'Ma G llan', 52, 0),
('27', 'Fort G', 54, 0),
('28', 'Agré G', 56, 0),
('29', 'Enra G', 58, 0),
('30', 'Pur G', 60, 0),
('31', 'Super G', 62, 0),
('32', 'G ant', 64, 0),
('33', 'G néral', 66, 0),
('34', 'Ma G sté', 68, 0),
('35', 'Au sommet du G3', 70, 0),
('36', 'Apo G', 72, 0),
('37', 'Hé G monie', 74, 0),
('38', 'G zu', 76, 0),
('39', 'Saint G', 78, 0),
('40', 'Divinement G', 80, 0),
('41', 'G rare', 90, 0),
('A', 'Grand Gourou', 0, 1),
('B', 'Vice Gourou', 0, 1),
('C', 'Potes à G', 0, 1),
('D', 'Mascotte', 0, 1),
('E', 'Rang G ?', 0, 1);

-- --------------------------------------------------------

--
-- Structure de la table `absg_sessions`
--

CREATE TABLE `absg_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `absg_users`
--

CREATE TABLE `absg_users` (
  `user_id` int(5) NOT NULL,
  `people_id` int(10) DEFAULT NULL,
  `username` varchar(25) CHARACTER SET utf8 NOT NULL,
  `username_clean` varchar(25) NOT NULL,
  `password` varchar(255) NOT NULL,
  `auth` varchar(255) NOT NULL,
  `noteg` varchar(255) NOT NULL,
  `rank` varchar(2) NOT NULL,
  `last_activity` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `absg_users`
--

INSERT INTO `absg_users` (`user_id`, `people_id`, `username`, `username_clean`, `password`, `auth`, `noteg`, `rank`, `last_activity`) VALUES
(1, 1, 'Zaffa', 'zaffa', '0b9c2625dc21ef05f6ad4ddf47c5f203837aa32c', '*', '\'D\';0;0;1;0', 'D', ''),


-- --------------------------------------------------------

--
-- Structure de la table `absg_zaffaneries`
--

CREATE TABLE `absg_zaffaneries` (
  `zaff_id` int(5) NOT NULL,
  `date_selector` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `absg_zaffaneries`
--

INSERT INTO `absg_zaffaneries` (`zaff_id`, `date_selector`, `title`) VALUES
(1, ';16;4;*(1964|1616)', 'Anniversaire de la mort ou de la naissance de Shakespeare'),
(2, ';1;11;2013', 'Mise en ligne d\'Absolument G v4'),
(3, ';7;11;*(1963)', 'Anniversaire d\'Achile Talon'),
(4, ';22;11;*(2003)', 'Anniversaire du site Absolument G');

-- --------------------------------------------------------

--
-- Structure de la table `agenda_events`
--

CREATE TABLE `agenda_events` (
  `event_id` int(11) NOT NULL,
  `date_start_year` int(4) NOT NULL,
  `date_start_month` int(2) NOT NULL,
  `date_start_day` int(2) NOT NULL,
  `date_start_time` time DEFAULT NULL,
  `date_end_year` int(4) DEFAULT NULL,
  `date_end_month` int(2) DEFAULT NULL,
  `date_end_day` int(2) DEFAULT NULL,
  `date_end_time` time DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `location` varchar(100) NOT NULL,
  `type` set('absg','civil','custom') NOT NULL,
  `poster_id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `agenda_lk_events_people`
--

CREATE TABLE `agenda_lk_events_people` (
  `event_id` int(10) NOT NULL,
  `people_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `agenda_people`
--

CREATE TABLE `agenda_people` (
  `people_id` int(10) NOT NULL,
  `firstname` varchar(25) CHARACTER SET utf8 NOT NULL,
  `firstname2` varchar(100) DEFAULT NULL,
  `lastname` varchar(25) CHARACTER SET utf8 NOT NULL,
  `surname` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `sex` enum('M','F') CHARACTER SET utf8 NOT NULL,
  `birthday` int(11) DEFAULT NULL,
  `deathday` int(11) DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `city` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `country` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `mobilephone` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `skype` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `website` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `rootfamilly` enum('gueudelot','guibert','guyomard','létot') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `agpa_awards`
--

CREATE TABLE `agpa_awards` (
  `year` int(4) NOT NULL,
  `category_id` int(2) NOT NULL,
  `author_id` int(11) NOT NULL,
  `award` enum('diamant','or','argent','bronze','lice') NOT NULL,
  `photo_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Structure de la table `agpa_categories`
--

CREATE TABLE `agpa_categories` (
  `category_id` int(2) NOT NULL,
  `order` int(2) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` tinytext NOT NULL,
  `color` varchar(7) NOT NULL,
  `has_variants` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `agpa_categories`
--

INSERT INTO `agpa_categories` (`category_id`, `order`, `title`, `description`, `color`, `has_variants`) VALUES
(-2, 97, 'Meilleure photo', 'Il s\'agit de récompenser la photo, parmi toutes les catégories, ayant reçut la meilleur note.', '#AAA', 0),
(1, 1, 'Portrait', 'Photos mettant en évidence un personnage unique.', '#F99', 0),
(2, 2, 'Groupe et Evénement', 'Photos de groupes avec au minimum deux individus sur la photo.', '#F3DA80', 0),
(3, 4, 'Nature', 'Photos mettant en évidence un sujet principal floristique, faunistique ou naturel.', '#69C841', 0),
(4, 5, 'Grand angle', 'Tous types de paysages et panoramas.', '#FFA263', 0),
(5, 6, 'Manus Hominum', 'Photos dont le sujet principal est marqués par l\'emprunte de l\'Homme (villes, monuments, ouvrage d\'art, machinerie, objets confectionnés par l\'homme ...)', '#7BB9FF', 0),
(6, 8, 'Autre Regard', 'Photos humoristiques, artistiques, décalées ou retouchées, ainsi que toutes celles n’entrant pas dans les autres catégories.', '#E292FF', 0),
(-1, 99, 'Meilleur photographe', 'Chaque édition des AGPA récompense le meilleur photographe de l\'année. Il s\'agit du participant dont les quatre meilleurs photos auront récolté le plus de point.', '#AAA', 0),
(-3, 98, 'Meilleur titre', 'R&eacute;compense les meilleurs titres toutes cat&eacute;gories confondues.', '#AAA', 0),
(7, 3, 'Enfants', 'Photos mettant en évidence un enfant de moins de quinze ans au moment de la prise de vue.', '#77E0CC', 0),
(8, 7, 'Spéciale', 'Chaque année, un thème particulier est choisi.', '#CED99A', 1);

-- --------------------------------------------------------

--
-- Structure de la table `agpa_catvariants`
--

CREATE TABLE `agpa_catvariants` (
  `category_id` int(2) NOT NULL,
  `year` int(4) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` tinytext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `agpa_catvariants`
--

INSERT INTO `agpa_catvariants` (`category_id`, `year`, `title`, `description`) VALUES
(0, 0, '-', '-'),
(8, 2012, 'Les mariages', 'Thomas et Eugénie, puis Marceau et Fanny ! Ça mérité bien une catégorie à eux tout seul'),
(8, 2013, 'Montrieux', 'Cousinade 2013 à Montrieux en Sologne.'),
(8, 2014, 'Drôle D-day', 'Sujet libre, seule contrainte: la date. Les photos doivent être prisent le 1er mai ou le 3 décembre'),
(8, 2015, 'Autour du nombre 10', 'Pour fêter les 10 ans des AGPA ! Sujet libre.'),
(8, 2016, 'The Artist', 'Photo donnant l\'impression d\'avoir été prise à une époque que la notre.');

-- --------------------------------------------------------

--
-- Structure de la table `agpa_photos`
--

CREATE TABLE `agpa_photos` (
  `photo_id` int(11) NOT NULL,
  `user_id` int(8) NOT NULL DEFAULT '0',
  `category_id` int(2) NOT NULL DEFAULT '0',
  `year` int(4) NOT NULL DEFAULT '0',
  `filename` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `ranking` int(3) NOT NULL DEFAULT '0',
  `number` int(3) NOT NULL DEFAULT '0',
  `votes` int(3) NOT NULL DEFAULT '0',
  `votes_title` int(3) NOT NULL DEFAULT '0',
  `score` int(3) NOT NULL DEFAULT '0',
  `g_score` int(6) NOT NULL DEFAULT '0',
  `error` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Structure de la table `agpa_votes`
--

CREATE TABLE `agpa_votes` (
  `year` int(4) NOT NULL DEFAULT '0',
  `category_id` int(2) NOT NULL DEFAULT '0',
  `user_id` int(8) NOT NULL DEFAULT '0',
  `photo_id` int(8) NOT NULL DEFAULT '0',
  `score` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `citations`
--

CREATE TABLE `citations` (
  `citation_id` int(11) NOT NULL,
  `poster_id` int(5) NOT NULL,
  `citation` text COLLATE latin1_general_ci NOT NULL,
  `author_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cultureg`
--

CREATE TABLE `cultureg` (
  `id` int(11) NOT NULL,
  `date_start_year` int(4) NOT NULL,
  `date_start_month` int(2) NOT NULL,
  `date_start_day` int(2) NOT NULL,
  `date_start_time` time DEFAULT NULL,
  `date_end_year` int(4) DEFAULT NULL,
  `date_end_month` int(2) DEFAULT NULL,
  `date_end_day` int(2) DEFAULT NULL,
  `date_end_time` time DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `location` varchar(100) NOT NULL,
  `poster_id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `forum_forums`
--

CREATE TABLE `forum_forums` (
  `forum_id` int(4) NOT NULL,
  `parent_id` int(4) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `private` tinyint(1) NOT NULL,
  `archived` tinyint(1) NOT NULL,
  `last_post_id` int(11) DEFAULT NULL,
  `last_post_time` int(11) DEFAULT NULL,
  `last_poster_name` varchar(25) DEFAULT NULL,
  `last_poster_id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `forum_posts`
--

CREATE TABLE `forum_posts` (
  `post_id` mediumint(8) UNSIGNED NOT NULL,
  `topic_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `forum_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `poster_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `time` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `text` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `attachment` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `forum_topics`
--

CREATE TABLE `forum_topics` (
  `topic_id` mediumint(8) UNSIGNED NOT NULL,
  `forum_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `first_post_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `first_post_time` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `first_poster_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `first_poster_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `last_post_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `last_post_time` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `last_poster_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `last_poster_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `replies` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `type` set('thread','important','article','poll','planning') COLLATE utf8_bin NOT NULL DEFAULT 'thread',
  `old_type` tinyint(3) NOT NULL DEFAULT '0',
  `poll_title` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `gnereux`
--

CREATE TABLE `gnereux` (
  `id` int(8) NOT NULL,
  `title` varchar(100) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `tags` text,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `owner_id` int(5) DEFAULT NULL,
  `donor` text,
  `donors_chat` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `gtheque_elements`
--

CREATE TABLE `gtheque_elements` (
  `element_id` int(12) NOT NULL,
  `set_id` int(8) DEFAULT NULL,
  `number` int(3) DEFAULT NULL,
  `title` tinytext NOT NULL,
  `description` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `gtheque_registers`
--

CREATE TABLE `gtheque_registers` (
  `element_id` int(12) NOT NULL,
  `user_id` int(8) NOT NULL,
  `format` tinytext NOT NULL,
  `quantity` int(2) NOT NULL DEFAULT '1',
  `location` tinytext NOT NULL,
  `status` enum('standby','interesting','whish','ok') NOT NULL DEFAULT 'ok',
  `comment` tinytext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `gtheque_sets`
--

CREATE TABLE `gtheque_sets` (
  `set_id` int(8) NOT NULL,
  `title` tinytext,
  `authors` tinytext,
  `edition` tinytext,
  `type` enum('bd','manga','novel','book','movie','tvshow','videogame','boardgame','miscellaneous','custom','unknow') NOT NULL DEFAULT 'unknow',
  `description` mediumtext
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `immt`
--

CREATE TABLE `immt` (
  `year` int(4) UNSIGNED NOT NULL DEFAULT '0',
  `day` int(3) UNSIGNED ZEROFILL NOT NULL DEFAULT '000',
  `user_id` int(8) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `jog_delegations`
--

CREATE TABLE `jog_delegations` (
  `id` int(5) NOT NULL,
  `edition_id` int(4) NOT NULL,
  `name` varchar(50) NOT NULL,
  `color` varchar(6) NOT NULL,
  `jdata` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `jog_editions`
--

CREATE TABLE `jog_editions` (
  `id` int(4) NOT NULL,
  `closed` tinyint(1) NOT NULL,
  `name` varchar(50) NOT NULL,
  `start_date` int(11) DEFAULT NULL,
  `end_date` int(11) DEFAULT NULL,
  `master` int(5) NOT NULL,
  `jdata` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `jog_games`
--

CREATE TABLE `jog_games` (
  `id` int(6) NOT NULL,
  `edition_id` int(4) NOT NULL,
  `type` enum('100G','110GH','bastonG','') NOT NULL,
  `start_date` int(11) DEFAULT NULL,
  `end_date` int(11) DEFAULT NULL,
  `jdata` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `jog_history`
--

CREATE TABLE `jog_history` (
  `edition_id` int(4) NOT NULL,
  `type` enum('public','delegation','game','admin') NOT NULL,
  `date` int(11) NOT NULL,
  `game_id` int(6) NOT NULL,
  `player_id` int(5) NOT NULL,
  `msg` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `jog_pg`
--

CREATE TABLE `jog_pg` (
  `player_id` int(5) NOT NULL,
  `game_id` int(6) NOT NULL,
  `jdata` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `jog_players`
--

CREATE TABLE `jog_players` (
  `user_id` int(5) NOT NULL,
  `delegation_id` int(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `pawn` int(2) NOT NULL,
  `pawn_emoticon` varchar(20) NOT NULL,
  `jdata` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `jog_questions`
--

CREATE TABLE `jog_questions` (
  `id` int(11) NOT NULL,
  `type` enum('simple','qcm','mordu','modulo','image') COLLATE latin1_general_ci NOT NULL DEFAULT 'simple',
  `question` tinytext COLLATE latin1_general_ci NOT NULL,
  `answer` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `theme_id` mediumint(8) NOT NULL DEFAULT '0',
  `nb_asked` int(11) NOT NULL DEFAULT '0',
  `nb_good_answer` int(11) NOT NULL DEFAULT '0',
  `ratio` float NOT NULL,
  `author_id` mediumint(8) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `web3g`
--

CREATE TABLE `web3g` (
  `web_id` int(2) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `last_update` int(11) NOT NULL,
  `last_update_by` int(5) NOT NULL,
  `last_update_note` varchar(255) DEFAULT NULL,
  `clicks` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `absg_current_data`
--
ALTER TABLE `absg_current_data`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `absg_daily_presence`
--
ALTER TABLE `absg_daily_presence`
  ADD PRIMARY KEY (`date`);

--
-- Index pour la table `absg_logs`
--
ALTER TABLE `absg_logs`
  ADD PRIMARY KEY (`user_id`,`date`);

--
-- Index pour la table `absg_ranks`
--
ALTER TABLE `absg_ranks`
  ADD PRIMARY KEY (`code`);

--
-- Index pour la table `absg_sessions`
--
ALTER TABLE `absg_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `last_activity_idx` (`last_activity`);

--
-- Index pour la table `absg_users`
--
ALTER TABLE `absg_users`
  ADD PRIMARY KEY (`user_id`);

--
-- Index pour la table `absg_zaffaneries`
--
ALTER TABLE `absg_zaffaneries`
  ADD PRIMARY KEY (`zaff_id`);

--
-- Index pour la table `agenda_events`
--
ALTER TABLE `agenda_events`
  ADD PRIMARY KEY (`event_id`);

--
-- Index pour la table `agenda_lk_events_people`
--
ALTER TABLE `agenda_lk_events_people`
  ADD PRIMARY KEY (`event_id`,`people_id`);

--
-- Index pour la table `agenda_people`
--
ALTER TABLE `agenda_people`
  ADD PRIMARY KEY (`people_id`);

--
-- Index pour la table `agpa_awards`
--
ALTER TABLE `agpa_awards`
  ADD PRIMARY KEY (`year`,`category_id`,`author_id`,`award`,`photo_id`);

--
-- Index pour la table `agpa_categories`
--
ALTER TABLE `agpa_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Index pour la table `agpa_catvariants`
--
ALTER TABLE `agpa_catvariants`
  ADD PRIMARY KEY (`category_id`,`year`);

--
-- Index pour la table `agpa_photos`
--
ALTER TABLE `agpa_photos`
  ADD PRIMARY KEY (`photo_id`);

--
-- Index pour la table `agpa_votes`
--
ALTER TABLE `agpa_votes`
  ADD PRIMARY KEY (`year`,`category_id`,`user_id`,`photo_id`);

--
-- Index pour la table `citations`
--
ALTER TABLE `citations`
  ADD PRIMARY KEY (`citation_id`);

--
-- Index pour la table `cultureg`
--
ALTER TABLE `cultureg`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `forum_forums`
--
ALTER TABLE `forum_forums`
  ADD PRIMARY KEY (`forum_id`);

--
-- Index pour la table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `forum_id` (`forum_id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `poster_id` (`poster_id`),
  ADD KEY `tid_post_time` (`topic_id`,`time`);
ALTER TABLE `forum_posts` ADD FULLTEXT KEY `post_text` (`text`);
ALTER TABLE `forum_posts` ADD FULLTEXT KEY `post_content` (`text`);

--
-- Index pour la table `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD PRIMARY KEY (`topic_id`),
  ADD KEY `forum_id` (`forum_id`),
  ADD KEY `forum_id_type` (`forum_id`,`old_type`),
  ADD KEY `last_post_time` (`last_post_time`),
  ADD KEY `forum_appr_last` (`forum_id`,`last_post_id`),
  ADD KEY `fid_time_moved` (`forum_id`,`last_post_time`);

--
-- Index pour la table `gnereux`
--
ALTER TABLE `gnereux`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `gtheque_elements`
--
ALTER TABLE `gtheque_elements`
  ADD PRIMARY KEY (`element_id`);

--
-- Index pour la table `gtheque_registers`
--
ALTER TABLE `gtheque_registers`
  ADD PRIMARY KEY (`element_id`,`user_id`);

--
-- Index pour la table `gtheque_sets`
--
ALTER TABLE `gtheque_sets`
  ADD PRIMARY KEY (`set_id`);

--
-- Index pour la table `immt`
--
ALTER TABLE `immt`
  ADD PRIMARY KEY (`year`,`day`);

--
-- Index pour la table `jog_delegations`
--
ALTER TABLE `jog_delegations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `jog_editions`
--
ALTER TABLE `jog_editions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `jog_games`
--
ALTER TABLE `jog_games`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `jog_questions`
--
ALTER TABLE `jog_questions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `web3g`
--
ALTER TABLE `web3g`
  ADD PRIMARY KEY (`web_id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `absg_logs`
--
ALTER TABLE `absg_logs`
  MODIFY `user_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT pour la table `absg_users`
--
ALTER TABLE `absg_users`
  MODIFY `user_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT pour la table `absg_zaffaneries`
--
ALTER TABLE `absg_zaffaneries`
  MODIFY `zaff_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `agenda_events`
--
ALTER TABLE `agenda_events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT pour la table `agenda_people`
--
ALTER TABLE `agenda_people`
  MODIFY `people_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
--
-- AUTO_INCREMENT pour la table `agpa_categories`
--
ALTER TABLE `agpa_categories`
  MODIFY `category_id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pour la table `agpa_photos`
--
ALTER TABLE `agpa_photos`
  MODIFY `photo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2562;
--
-- AUTO_INCREMENT pour la table `citations`
--
ALTER TABLE `citations`
  MODIFY `citation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=406;
--
-- AUTO_INCREMENT pour la table `cultureg`
--
ALTER TABLE `cultureg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `forum_forums`
--
ALTER TABLE `forum_forums`
  MODIFY `forum_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT pour la table `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `post_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30285;
--
-- AUTO_INCREMENT pour la table `forum_topics`
--
ALTER TABLE `forum_topics`
  MODIFY `topic_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1309;
--
-- AUTO_INCREMENT pour la table `gnereux`
--
ALTER TABLE `gnereux`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `gtheque_elements`
--
ALTER TABLE `gtheque_elements`
  MODIFY `element_id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT pour la table `gtheque_sets`
--
ALTER TABLE `gtheque_sets`
  MODIFY `set_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `jog_delegations`
--
ALTER TABLE `jog_delegations`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `jog_editions`
--
ALTER TABLE `jog_editions`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `jog_games`
--
ALTER TABLE `jog_games`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `jog_questions`
--
ALTER TABLE `jog_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24826;
--
-- AUTO_INCREMENT pour la table `web3g`
--
ALTER TABLE `web3g`
  MODIFY `web_id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
