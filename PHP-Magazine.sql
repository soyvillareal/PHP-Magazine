-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-11-2022 a las 22:06:58
-- Versión del servidor: 10.4.22-MariaDB
-- Versión de PHP: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `testp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `block`
--

CREATE TABLE `block` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `profile_id` int(10) UNSIGNED NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `breaking`
--

CREATE TABLE `breaking` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `expiration_at` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `category`
--

CREATE TABLE `category` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(550) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `footer` set('f_one','f_two','more') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'more',
  `order` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` set('disabled','enabled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'disabled',
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `category`
--

INSERT INTO `category` (`id`, `name`, `description`, `slug`, `keywords`, `footer`, `order`, `status`, `updated_at`, `created_at`) VALUES
(1, 'fashion', 'PHP Magazine Category Fashion', 'fashion', 'php magazine, category, fashion', 'f_one', '1', 'enabled', 1560132205, 1560132205),
(2, 'travel', 'PHP Magazine Category Travel', 'travel', 'php magazine, category, travel', 'f_one', '2', 'enabled', 1560132225, 1560132225),
(3, 'sport', 'PHP Magazine Category Sport', 'sport', 'php magazine, category, sport', 'f_two', '3', 'enabled', 1560132276, 1560132276),
(4, 'clothes', 'PHP Magazine Category Clothes', 'clothes', 'php magazine, category, clothes', 'f_one', '4', 'enabled', 1560132293, 1560132293),
(5, 'places', 'PHP Magazine Category Places', 'places', 'php magazine, category, places', 'f_two', '5', 'enabled', 1560132311, 1560132311),
(6, 'nature', 'PHP Magazine Category Nature', 'nature', 'php magazine, category, nature', 'more', '6', 'enabled', 1560132332, 1560132332),
(7, 'design', 'PHP Magazine Category Design', 'design', 'php magazine, category, design', 'more', '7', 'enabled', 1560132350, 1560132350),
(8, 'business', 'PHP Magazine Category Business', 'business', 'php magazine, category, business', 'f_two', '8', 'enabled', 1560132370, 1560132370),
(9, 'photography', 'PHP Magazine Category Photography', 'photography', 'php magazine, category, photography', 'more', '9', 'enabled', 1560132384, 1560132384);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat`
--

CREATE TABLE `chat` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `profile_id` int(10) UNSIGNED NOT NULL,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `collaborator`
--

CREATE TABLE `collaborator` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `aorder` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comment`
--

CREATE TABLE `comment` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `text` text CHARACTER SET utf8mb4 NOT NULL,
  `pinned` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entry`
--

CREATE TABLE `entry` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `type` set('text','image','carousel','video','embed','soundcloud','facebookpost','instagrampost','tweet','tiktok','spotify') COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `frame` text CHARACTER SET utf8mb4 DEFAULT NULL,
  `esource` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `eorder` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `follower`
--

CREATE TABLE `follower` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `profile_id` int(10) UNSIGNED NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `label`
--

CREATE TABLE `label` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `language`
--

CREATE TABLE `language` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL DEFAULT 'en',
  `code` varchar(16) NOT NULL DEFAULT 'en_US',
  `dir` set('rtl','ltr') NOT NULL,
  `status` set('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `language`
--

INSERT INTO `language` (`id`, `name`, `lang`, `code`, `dir`, `status`, `created_at`) VALUES
(1, 'English', 'en', 'en_US', 'ltr', 'enabled', 1611596407),
(2, 'Español', 'es', 'es_CO', 'ltr', 'enabled', 1611596407),
(3, 'عرب', 'ar', 'ar_IL', 'rtl', 'enabled', 1611596407);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messaan`
--

CREATE TABLE `messaan` (
  `id` int(10) UNSIGNED NOT NULL,
  `message_id` int(10) UNSIGNED NOT NULL,
  `answered_id` int(10) UNSIGNED NOT NULL,
  `type` set('text','file','image') NOT NULL DEFAULT 'text',
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messafi`
--

CREATE TABLE `messafi` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `message_id` int(10) UNSIGNED NOT NULL,
  `file` varchar(128) CHARACTER SET latin1 NOT NULL,
  `size` int(11) NOT NULL DEFAULT 0,
  `deleted_fuser` int(11) NOT NULL DEFAULT 0,
  `deleted_fprofile` int(11) NOT NULL DEFAULT 0,
  `deleted_at` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `message`
--

CREATE TABLE `message` (
  `id` int(10) UNSIGNED NOT NULL,
  `chat_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `profile_id` int(10) UNSIGNED NOT NULL,
  `text` text CHARACTER SET utf8mb4 DEFAULT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_fuser` int(11) NOT NULL DEFAULT 0,
  `deleted_fprofile` int(11) NOT NULL DEFAULT 0,
  `deleted_at` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `newscate`
--

CREATE TABLE `newscate` (
  `id` int(10) UNSIGNED NOT NULL,
  `newsletter_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `frequency` set('all','now','daily','weekly') NOT NULL DEFAULT 'all',
  `popular` set('off','on') NOT NULL DEFAULT 'off',
  `reason` text DEFAULT NULL,
  `status` set('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notification`
--

CREATE TABLE `notification` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `notified_id` text NOT NULL,
  `type` set('n_post','n_collab','n_followers','n_preact','n_creact','n_rreact','n_pcomment','n_preply','n_ucomment','n_ureply') NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `page`
--

CREATE TABLE `page` (
  `id` int(11) NOT NULL,
  `slug` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  `keywords` text NOT NULL,
  `text` text DEFAULT NULL,
  `status` set('disabled','enabled') NOT NULL DEFAULT 'disabled',
  `footer` set('on','off') NOT NULL DEFAULT 'on',
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `page`
--

INSERT INTO `page` (`id`, `slug`, `description`, `keywords`, `text`, `status`, `footer`, `updated_at`, `created_at`) VALUES
(1, 'terms_of_use', 'Learn about the PHP Magazine Terms of Use. Know the guidelines for the treatment of information and the management of your personal data.', 'terms and conditions, privacy notice, terms of use, php magazine', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec fringilla id augue vitae consectetur. Praesent augue justo, efficitur nec ipsum sit amet, lacinia porttitor felis. Quisque consequat mattis rutrum. Aliquam tristique sapien ut enim iaculis, nec pretium odio aliquet. Aenean eu massa nec nunc auctor viverra. Cras dapibus sem nec magna gravida convallis et eu diam. Maecenas sagittis turpis vitae nibh pellentesque, eget semper erat rutrum. Vestibulum sed urna nunc. Donec felis odio, condimentum vitae maximus vel, consectetur eu urna. Integer metus velit, accumsan at aliquam sit amet, porta quis ipsum. Donec tristique leo ac massa efficitur porttitor in eget risus. Duis porta ante turpis.</p>\n\n<p>Nullam ultricies sodales augue, ut laoreet purus eleifend vel. Duis elit elit, interdum vel erat ut, suscipit pulvinar leo. In porttitor nisi ac blandit iaculis. In ultricies mi sed sem viverra condimentum. Aenean in massa ac elit feugiat egestas a eu elit. Morbi ut lectus tincidunt, fermentum mauris vel, fringilla velit. Ut massa lectus, rutrum non eros id, luctus elementum est. Aliquam lorem arcu, scelerisque elementum odio quis, mollis ultricies erat. Ut pharetra diam a arcu facilisis, at porttitor libero dapibus. Nam enim odio, efficitur ac congue et, interdum vel lectus. Vestibulum eget egestas eros. Vivamus vitae sem vel neque rhoncus mattis. In sit amet commodo arcu. Aenean nec consequat urna, id varius nunc.</p>\n\n<p>Praesent eget justo lobortis, aliquam nisi et, mattis ex. Etiam pretium urna risus, eu facilisis tortor interdum a. Nullam lacinia arcu vitae dignissim semper. Sed malesuada, justo id porttitor feugiat, lectus leo volutpat mi, porta tempor odio nunc tincidunt neque. Fusce gravida sem a erat hendrerit bibendum. Donec non finibus velit, quis rutrum libero. Nulla ante tortor, suscipit fermentum arcu et, efficitur ultrices massa. Vestibulum nunc mi, lobortis nec rhoncus quis, mattis id ipsum. In in fringilla tortor, a luctus tellus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nunc sem diam, consectetur id neque sed, iaculis efficitur enim. Phasellus bibendum scelerisque imperdiet. Nulla id tempus metus. Pellentesque sagittis, lorem sit amet semper porttitor, dolor leo sollicitudin leo, at auctor dui nulla ac dolor. Nam volutpat in magna ac pretium. Proin tristique aliquam justo sit amet lacinia.</p>\n\n<p>Ut varius sapien sed finibus lacinia. Curabitur luctus metus quis eros fringilla, sed finibus mi condimentum. Duis et rutrum nunc. Praesent quis dui sed orci placerat accumsan nec a tortor. Duis ut purus faucibus, lobortis magna non, venenatis eros. Nulla sed nisi vel risus egestas ullamcorper sed quis urna. Vivamus enim risus, dignissim in sollicitudin ac, elementum eget ipsum. Vestibulum ac malesuada dui, pulvinar lacinia dolor. Aliquam viverra diam massa, et fermentum est ultrices quis. Aliquam finibus libero a ex eleifend feugiat. Duis a condimentum justo.</p>\n\n<p>Praesent quis volutpat eros. Suspendisse finibus leo vitae pulvinar tempus. Phasellus sodales, sem eu consectetur blandit, nulla dolor sodales eros, vel pulvinar ligula nibh et felis. Nam porttitor dictum urna, ac pharetra enim. Donec orci lectus, consequat id dui vitae, efficitur pellentesque dolor. Nunc non pulvinar turpis. Aenean quis arcu nec leo suscipit consequat id sit amet tortor. Nullam posuere elementum mi id semper. Mauris non tempus nibh. Duis dapibus ipsum urna, vitae pellentesque magna fermentum eu.</p>\n\n<p>Proin quis ligula mauris. Integer eleifend ex vel fermentum finibus. Proin eu lorem id risus feugiat molestie. Ut sit amet venenatis quam. Quisque sollicitudin fermentum nibh a semper. Vestibulum blandit congue lectus, malesuada lacinia justo accumsan ac. Nullam a venenatis nulla. Morbi suscipit ligula a ligula pretium tincidunt. Aliquam erat volutpat. Praesent sed elementum massa. In vitae purus malesuada, consectetur velit quis, sagittis purus. Suspendisse potenti. Etiam augue ligula, euismod nec ultricies eu, placerat eu nisi.</p>\n\n<p>Cras at tortor varius, ultricies nulla vitae, sodales orci. Nulla tempor lobortis mauris, ut placerat neque dignissim eleifend. Maecenas vel eleifend velit. Aenean convallis sodales egestas. Nam sit amet rhoncus eros, et venenatis ligula. Fusce ante elit, semper sit amet nisl a, ultrices feugiat eros. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec in pellentesque odio, vitae commodo elit. Nulla sed leo at purus pharetra auctor. Morbi commodo vel risus eget mollis. Duis sit amet sollicitudin leo. Donec ultricies condimentum felis, in rhoncus nunc mattis eget.</p>\n\n<p>Mauris suscipit mauris sed purus posuere iaculis. Etiam scelerisque at mauris eget scelerisque. Nam vel quam magna. Mauris consectetur enim vel consectetur finibus. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent a dolor nunc. Vestibulum in luctus erat. Duis quis iaculis enim, eu volutpat eros. Maecenas ut sollicitudin erat. In placerat nibh quis lobortis rutrum. Curabitur volutpat blandit urna, dapibus consequat eros vestibulum a. Sed fringilla lobortis dolor, at aliquam arcu convallis et. Duis dignissim luctus facilisis. Nulla bibendum porta dui. Suspendisse vel urna a risus tempus sagittis ut a velit. Praesent iaculis, nulla quis ultricies vehicula, tellus lectus fringilla nunc, in consectetur metus libero eget arcu.</p>\n\n<p>Aenean vel dolor vel nisl blandit ultrices rhoncus eget arcu. Nam ac nisl sem. Phasellus odio dolor, euismod quis sapien vel, euismod consectetur purus. Quisque non auctor odio. Vestibulum pellentesque metus egestas, consectetur sem eget, eleifend metus. In hac habitasse platea dictumst. Ut accumsan ante eget dolor tincidunt, quis posuere nulla tincidunt. Vivamus sagittis blandit metus nec pellentesque. Integer sit amet aliquet arcu. Donec sed iaculis magna. Vivamus vehicula pharetra lacus, at varius tortor sollicitudin sed. Proin turpis ligula, commodo eu auctor vel, varius at justo. Pellentesque porta hendrerit risus, eu placerat lacus. Nunc malesuada pharetra convallis. Aenean venenatis, dui a bibendum scelerisque, erat lectus interdum orci, ac tristique elit leo quis turpis.</p>\n\n<p>Etiam et tempor augue. Mauris tempor eget neque a semper. Nulla ante erat, dignissim ut tincidunt sit amet, consectetur eu orci. Nam vel sagittis erat. Nunc rhoncus consequat blandit. Aliquam iaculis enim eu viverra porttitor. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>', 'enabled', 'on', 1665139297, 1664116167),
(2, 'habeas_data', 'Get to know the privacy policies of PHP Magazine. Know the guidelines for the treatment of information and the management of your personal data.', 'habeas data, privacy policy, privacy notice, php magazine', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec fringilla id augue vitae consectetur. Praesent augue justo, efficitur nec ipsum sit amet, lacinia porttitor felis. Quisque consequat mattis rutrum. Aliquam tristique sapien ut enim iaculis, nec pretium odio aliquet. Aenean eu massa nec nunc auctor viverra. Cras dapibus sem nec magna gravida convallis et eu diam. Maecenas sagittis turpis vitae nibh pellentesque, eget semper erat rutrum. Vestibulum sed urna nunc. Donec felis odio, condimentum vitae maximus vel, consectetur eu urna. Integer metus velit, accumsan at aliquam sit amet, porta quis ipsum. Donec tristique leo ac massa efficitur porttitor in eget risus. Duis porta ante turpis.</p>\n\n<p>Nullam ultricies sodales augue, ut laoreet purus eleifend vel. Duis elit elit, interdum vel erat ut, suscipit pulvinar leo. In porttitor nisi ac blandit iaculis. In ultricies mi sed sem viverra condimentum. Aenean in massa ac elit feugiat egestas a eu elit. Morbi ut lectus tincidunt, fermentum mauris vel, fringilla velit. Ut massa lectus, rutrum non eros id, luctus elementum est. Aliquam lorem arcu, scelerisque elementum odio quis, mollis ultricies erat. Ut pharetra diam a arcu facilisis, at porttitor libero dapibus. Nam enim odio, efficitur ac congue et, interdum vel lectus. Vestibulum eget egestas eros. Vivamus vitae sem vel neque rhoncus mattis. In sit amet commodo arcu. Aenean nec consequat urna, id varius nunc.</p>\n\n<p>Praesent eget justo lobortis, aliquam nisi et, mattis ex. Etiam pretium urna risus, eu facilisis tortor interdum a. Nullam lacinia arcu vitae dignissim semper. Sed malesuada, justo id porttitor feugiat, lectus leo volutpat mi, porta tempor odio nunc tincidunt neque. Fusce gravida sem a erat hendrerit bibendum. Donec non finibus velit, quis rutrum libero. Nulla ante tortor, suscipit fermentum arcu et, efficitur ultrices massa. Vestibulum nunc mi, lobortis nec rhoncus quis, mattis id ipsum. In in fringilla tortor, a luctus tellus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nunc sem diam, consectetur id neque sed, iaculis efficitur enim. Phasellus bibendum scelerisque imperdiet. Nulla id tempus metus. Pellentesque sagittis, lorem sit amet semper porttitor, dolor leo sollicitudin leo, at auctor dui nulla ac dolor. Nam volutpat in magna ac pretium. Proin tristique aliquam justo sit amet lacinia.</p>\n\n<p>Ut varius sapien sed finibus lacinia. Curabitur luctus metus quis eros fringilla, sed finibus mi condimentum. Duis et rutrum nunc. Praesent quis dui sed orci placerat accumsan nec a tortor. Duis ut purus faucibus, lobortis magna non, venenatis eros. Nulla sed nisi vel risus egestas ullamcorper sed quis urna. Vivamus enim risus, dignissim in sollicitudin ac, elementum eget ipsum. Vestibulum ac malesuada dui, pulvinar lacinia dolor. Aliquam viverra diam massa, et fermentum est ultrices quis. Aliquam finibus libero a ex eleifend feugiat. Duis a condimentum justo.</p>\n\n<p>Praesent quis volutpat eros. Suspendisse finibus leo vitae pulvinar tempus. Phasellus sodales, sem eu consectetur blandit, nulla dolor sodales eros, vel pulvinar ligula nibh et felis. Nam porttitor dictum urna, ac pharetra enim. Donec orci lectus, consequat id dui vitae, efficitur pellentesque dolor. Nunc non pulvinar turpis. Aenean quis arcu nec leo suscipit consequat id sit amet tortor. Nullam posuere elementum mi id semper. Mauris non tempus nibh. Duis dapibus ipsum urna, vitae pellentesque magna fermentum eu.</p>\n\n<p>Proin quis ligula mauris. Integer eleifend ex vel fermentum finibus. Proin eu lorem id risus feugiat molestie. Ut sit amet venenatis quam. Quisque sollicitudin fermentum nibh a semper. Vestibulum blandit congue lectus, malesuada lacinia justo accumsan ac. Nullam a venenatis nulla. Morbi suscipit ligula a ligula pretium tincidunt. Aliquam erat volutpat. Praesent sed elementum massa. In vitae purus malesuada, consectetur velit quis, sagittis purus. Suspendisse potenti. Etiam augue ligula, euismod nec ultricies eu, placerat eu nisi.</p>\n\n<p>Cras at tortor varius, ultricies nulla vitae, sodales orci. Nulla tempor lobortis mauris, ut placerat neque dignissim eleifend. Maecenas vel eleifend velit. Aenean convallis sodales egestas. Nam sit amet rhoncus eros, et venenatis ligula. Fusce ante elit, semper sit amet nisl a, ultrices feugiat eros. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec in pellentesque odio, vitae commodo elit. Nulla sed leo at purus pharetra auctor. Morbi commodo vel risus eget mollis. Duis sit amet sollicitudin leo. Donec ultricies condimentum felis, in rhoncus nunc mattis eget.</p>\n\n<p>Mauris suscipit mauris sed purus posuere iaculis. Etiam scelerisque at mauris eget scelerisque. Nam vel quam magna. Mauris consectetur enim vel consectetur finibus. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent a dolor nunc. Vestibulum in luctus erat. Duis quis iaculis enim, eu volutpat eros. Maecenas ut sollicitudin erat. In placerat nibh quis lobortis rutrum. Curabitur volutpat blandit urna, dapibus consequat eros vestibulum a. Sed fringilla lobortis dolor, at aliquam arcu convallis et. Duis dignissim luctus facilisis. Nulla bibendum porta dui. Suspendisse vel urna a risus tempus sagittis ut a velit. Praesent iaculis, nulla quis ultricies vehicula, tellus lectus fringilla nunc, in consectetur metus libero eget arcu.</p>\n\n<p>Aenean vel dolor vel nisl blandit ultrices rhoncus eget arcu. Nam ac nisl sem. Phasellus odio dolor, euismod quis sapien vel, euismod consectetur purus. Quisque non auctor odio. Vestibulum pellentesque metus egestas, consectetur sem eget, eleifend metus. In hac habitasse platea dictumst. Ut accumsan ante eget dolor tincidunt, quis posuere nulla tincidunt. Vivamus sagittis blandit metus nec pellentesque. Integer sit amet aliquet arcu. Donec sed iaculis magna. Vivamus vehicula pharetra lacus, at varius tortor sollicitudin sed. Proin turpis ligula, commodo eu auctor vel, varius at justo. Pellentesque porta hendrerit risus, eu placerat lacus. Nunc malesuada pharetra convallis. Aenean venenatis, dui a bibendum scelerisque, erat lectus interdum orci, ac tristique elit leo quis turpis.</p>\n\n<p>Etiam et tempor augue. Mauris tempor eget neque a semper. Nulla ante erat, dignissim ut tincidunt sit amet, consectetur eu orci. Nam vel sagittis erat. Nunc rhoncus consequat blandit. Aliquam iaculis enim eu viverra porttitor. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>', 'enabled', 'on', 0, 1664116167),
(4, 'about_us', 'Learn more about PHP Magazine.', 'abous us, php magazine', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec fringilla id augue vitae consectetur. Praesent augue justo, efficitur nec ipsum sit amet, lacinia porttitor felis. Quisque consequat mattis rutrum. Aliquam tristique sapien ut enim iaculis, nec pretium odio aliquet. Aenean eu massa nec nunc auctor viverra. Cras dapibus sem nec magna gravida convallis et eu diam. Maecenas sagittis turpis vitae nibh pellentesque, eget semper erat rutrum. Vestibulum sed urna nunc. Donec felis odio, condimentum vitae maximus vel, consectetur eu urna. Integer metus velit, accumsan at aliquam sit amet, porta quis ipsum. Donec tristique leo ac massa efficitur porttitor in eget risus. Duis porta ante turpis.</p>\n\n<p>Nullam ultricies sodales augue, ut laoreet purus eleifend vel. Duis elit elit, interdum vel erat ut, suscipit pulvinar leo. In porttitor nisi ac blandit iaculis. In ultricies mi sed sem viverra condimentum. Aenean in massa ac elit feugiat egestas a eu elit. Morbi ut lectus tincidunt, fermentum mauris vel, fringilla velit. Ut massa lectus, rutrum non eros id, luctus elementum est. Aliquam lorem arcu, scelerisque elementum odio quis, mollis ultricies erat. Ut pharetra diam a arcu facilisis, at porttitor libero dapibus. Nam enim odio, efficitur ac congue et, interdum vel lectus. Vestibulum eget egestas eros. Vivamus vitae sem vel neque rhoncus mattis. In sit amet commodo arcu. Aenean nec consequat urna, id varius nunc.</p>\n\n<p>Praesent eget justo lobortis, aliquam nisi et, mattis ex. Etiam pretium urna risus, eu facilisis tortor interdum a. Nullam lacinia arcu vitae dignissim semper. Sed malesuada, justo id porttitor feugiat, lectus leo volutpat mi, porta tempor odio nunc tincidunt neque. Fusce gravida sem a erat hendrerit bibendum. Donec non finibus velit, quis rutrum libero. Nulla ante tortor, suscipit fermentum arcu et, efficitur ultrices massa. Vestibulum nunc mi, lobortis nec rhoncus quis, mattis id ipsum. In in fringilla tortor, a luctus tellus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nunc sem diam, consectetur id neque sed, iaculis efficitur enim. Phasellus bibendum scelerisque imperdiet. Nulla id tempus metus. Pellentesque sagittis, lorem sit amet semper porttitor, dolor leo sollicitudin leo, at auctor dui nulla ac dolor. Nam volutpat in magna ac pretium. Proin tristique aliquam justo sit amet lacinia.</p>\n\n<p>Ut varius sapien sed finibus lacinia. Curabitur luctus metus quis eros fringilla, sed finibus mi condimentum. Duis et rutrum nunc. Praesent quis dui sed orci placerat accumsan nec a tortor. Duis ut purus faucibus, lobortis magna non, venenatis eros. Nulla sed nisi vel risus egestas ullamcorper sed quis urna. Vivamus enim risus, dignissim in sollicitudin ac, elementum eget ipsum. Vestibulum ac malesuada dui, pulvinar lacinia dolor. Aliquam viverra diam massa, et fermentum est ultrices quis. Aliquam finibus libero a ex eleifend feugiat. Duis a condimentum justo.</p>\n\n<p>Praesent quis volutpat eros. Suspendisse finibus leo vitae pulvinar tempus. Phasellus sodales, sem eu consectetur blandit, nulla dolor sodales eros, vel pulvinar ligula nibh et felis. Nam porttitor dictum urna, ac pharetra enim. Donec orci lectus, consequat id dui vitae, efficitur pellentesque dolor. Nunc non pulvinar turpis. Aenean quis arcu nec leo suscipit consequat id sit amet tortor. Nullam posuere elementum mi id semper. Mauris non tempus nibh. Duis dapibus ipsum urna, vitae pellentesque magna fermentum eu.</p>\n\n<p>Proin quis ligula mauris. Integer eleifend ex vel fermentum finibus. Proin eu lorem id risus feugiat molestie. Ut sit amet venenatis quam. Quisque sollicitudin fermentum nibh a semper. Vestibulum blandit congue lectus, malesuada lacinia justo accumsan ac. Nullam a venenatis nulla. Morbi suscipit ligula a ligula pretium tincidunt. Aliquam erat volutpat. Praesent sed elementum massa. In vitae purus malesuada, consectetur velit quis, sagittis purus. Suspendisse potenti. Etiam augue ligula, euismod nec ultricies eu, placerat eu nisi.</p>\n\n<p>Cras at tortor varius, ultricies nulla vitae, sodales orci. Nulla tempor lobortis mauris, ut placerat neque dignissim eleifend. Maecenas vel eleifend velit. Aenean convallis sodales egestas. Nam sit amet rhoncus eros, et venenatis ligula. Fusce ante elit, semper sit amet nisl a, ultrices feugiat eros. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec in pellentesque odio, vitae commodo elit. Nulla sed leo at purus pharetra auctor. Morbi commodo vel risus eget mollis. Duis sit amet sollicitudin leo. Donec ultricies condimentum felis, in rhoncus nunc mattis eget.</p>\n\n<p>Mauris suscipit mauris sed purus posuere iaculis. Etiam scelerisque at mauris eget scelerisque. Nam vel quam magna. Mauris consectetur enim vel consectetur finibus. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent a dolor nunc. Vestibulum in luctus erat. Duis quis iaculis enim, eu volutpat eros. Maecenas ut sollicitudin erat. In placerat nibh quis lobortis rutrum. Curabitur volutpat blandit urna, dapibus consequat eros vestibulum a. Sed fringilla lobortis dolor, at aliquam arcu convallis et. Duis dignissim luctus facilisis. Nulla bibendum porta dui. Suspendisse vel urna a risus tempus sagittis ut a velit. Praesent iaculis, nulla quis ultricies vehicula, tellus lectus fringilla nunc, in consectetur metus libero eget arcu.</p>\n\n<p>Aenean vel dolor vel nisl blandit ultrices rhoncus eget arcu. Nam ac nisl sem. Phasellus odio dolor, euismod quis sapien vel, euismod consectetur purus. Quisque non auctor odio. Vestibulum pellentesque metus egestas, consectetur sem eget, eleifend metus. In hac habitasse platea dictumst. Ut accumsan ante eget dolor tincidunt, quis posuere nulla tincidunt. Vivamus sagittis blandit metus nec pellentesque. Integer sit amet aliquet arcu. Donec sed iaculis magna. Vivamus vehicula pharetra lacus, at varius tortor sollicitudin sed. Proin turpis ligula, commodo eu auctor vel, varius at justo. Pellentesque porta hendrerit risus, eu placerat lacus. Nunc malesuada pharetra convallis. Aenean venenatis, dui a bibendum scelerisque, erat lectus interdum orci, ac tristique elit leo quis turpis.</p>\n\n<p>Etiam et tempor augue. Mauris tempor eget neque a semper. Nulla ante erat, dignissim ut tincidunt sit amet, consectetur eu orci. Nam vel sagittis erat. Nunc rhoncus consequat blandit. Aliquam iaculis enim eu viverra porttitor. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>', 'enabled', 'on', 0, 1664116167),
(5, 'contact', 'If you have any questions, concerns or need to make a proposal, you can contact us and we will respond as soon as possible.', 'contact, how to contact', '', 'enabled', 'on', 0, 1664116167),
(6, 'sitemap', 'An index of all the stories published by PHP Magazine.', 'sitemap, php magazine sitemap', '', 'enabled', 'on', 0, 1664116167),
(7, 'delete_account', 'Instructions for deleting your PHP Magazine account', 'Delete account, instructions, php magazine', '', 'enabled', 'off', 0, 1664116167);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post`
--

CREATE TABLE `post` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `thumbnail` varchar(128) CHARACTER SET latin1 DEFAULT 'default-holder',
  `views` int(11) NOT NULL DEFAULT 0,
  `likes` int(11) NOT NULL DEFAULT 0,
  `dislikes` int(11) NOT NULL DEFAULT 0,
  `post_sources` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `thumb_sources` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` set('normal','video') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
  `status` set('approved','rejected','pending','deleted') CHARACTER SET utf8 NOT NULL DEFAULT 'pending',
  `deleted_at` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `published_at` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reaction`
--

CREATE TABLE `reaction` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `reacted_id` int(10) UNSIGNED NOT NULL,
  `type` set('like','dislike') NOT NULL,
  `place` set('post','comment','reply') NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recobo`
--

CREATE TABLE `recobo` (
  `id` int(10) UNSIGNED NOT NULL,
  `recommended_id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `rorder` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reply`
--

CREATE TABLE `reply` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `comment_id` int(10) UNSIGNED NOT NULL,
  `text` text CHARACTER SET utf8mb4 NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `report`
--

CREATE TABLE `report` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `reported_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `type` set('r_spam','r_none','rp_writing','rp_thumbnail','rp_copyright','rc_offensive','rc_abusive','rc_disagree','rc_marketing','ru_hate','ru_picture','ru_copyright') NOT NULL,
  `place` set('user','post','comment','reply') NOT NULL,
  `description` text DEFAULT NULL,
  `status` set('unanswered','answered','archived','removed') NOT NULL DEFAULT 'unanswered',
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `saved`
--

CREATE TABLE `saved` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `session`
--

CREATE TABLE `session` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(150) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `setting`
--

CREATE TABLE `setting` (
  `name` varchar(128) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `setting`
--

INSERT INTO `setting` (`name`, `value`) VALUES
('2check', 'on'),
('approve_posts', 'off'),
('blocked_users', 'on'),
('censored_words', 'fuck, fuck you, puto, maldito, porno, puta'),
('contact_email', 'hi@soyvillareal.com'),
('dark_palette', '{\"background\":{\"white\":\"#181818\",\"blue\":\"#265070\",\"grely\":\"#303030\",\"black\":\"#e4e6eb\"},\"color\":{\"blackly\":\"#b0b3b8\",\"black\":\"#e4e6eb\",\"white\":\"#181818\",\"grey\":\"#aaa\",\"blue\":\"#265070\"},\"border\":{\"blue\":\"#265070\",\"focus-blue\":\"#265070\",\"grely\":\"#606060\",\"grey\":\"#aaa\",\"black\":\"#a5a6ab\"},\"hover\":{\"blue\":{\"type\":\"color\",\"value\":\"#265070\"},\"background\":{\"type\":\"background-color\",\"value\":\"#222222\"}}}'),
('description', 'The best digital magazine for newspapers or bloggers'),
('dir_pages', 'ltr'),
('dismiss_cookie', 'on'),
('facebook_page', 'https://facebook.com/soyvillareal'),
('fb_app_id', ''),
('fb_comments', 'on'),
('fb_secret_id', ''),
('file_size_limit', '26214400'),
('from_email', 'no-reply@phpmagazine.soyvillareal.com'),
('google_analytics', 'G-T102KHMVHQ'),
('go_app_id', ''),
('go_secret_id', ''),
('hidden_domains', 'pornhub.com, xvideos.com'),
('instagram_page', 'https://instagram.com/soyvillareal'),
('keywords', 'PHP Magazine, Magazine, PHP Script, Nyt clone, Open Source project'),
('language', 'en'),
('last_sitemap', '0'),
('light_palette', '{\"background\":{\"white\":\"#fff\",\"blue\":\"#326891\",\"grely\":\"#e9e9e9\",\"redly\":\"#dd6e68\",\"red\":\"#cb423b\",\"black\":\"#000\",\"blackly\":\"rgba(0,0,0,.5)\",\"green\":\"#61a125\"},\"color\":{\"blackly\":\"#333\",\"black\":\"#000\",\"white\":\"#fff\",\"grey\":\"#909090\",\"blue\":\"#326891\",\"red\":\"#cb0e0b\",\"green\":\"#61a125\",\"orange\":\"#f29f18\"},\"border\":{\"blue\":\"#326891\",\"focus-blue\":\"#326891\",\"grely\":\"#cdcdcd\",\"grey\":\"#909090\",\"black\":\"#000\",\"red\":\"#cb0e0b\"},\"hover\":{\"blue\":{\"type\":\"color\",\"value\":\"#326891\"},\"background\":{\"type\":\"background-color\",\"value\":\"#ebebeb\"}}}'),
('max_words_about', '800'),
('max_words_comments', '1000'),
('max_words_report', '500'),
('max_words_unsub_newsletter', '600'),
('newsletter', 'off'),
('nodejs', 'off'),
('node_hostname', 'phpmagazine.soyvillareal.com'),
('node_server_port', '3000'),
('number_labels', '8'),
('number_of_fonts', '8'),
('post_article', 'all'),
('recaptcha', 'off'),
('recaptcha_private_key', ''),
('recaptcha_public_key', ''),
('server_type', 'smtp'),
('show_palette', 'on'),
('smtp_encryption', 'tls'),
('smtp_host', 'smtp.soyvillareal.com'),
('smtp_password', ''),
('smtp_port', '587'),
('smtp_username', 'no-reply@phpmagazine.soyvillareal.com'),
('switch_mode', 'on'),
('system_comments', 'on'),
('theme', 'default'),
('theme_mode', 'light'),
('timezone', 'America/Bogota'),
('title', 'PHP Magazine'),
('token_expiration_attempts', '7'),
('token_expiration_hours', '1'),
('twitter_page', 'https://twitter.com/zoyvillareal'),
('tw_api_key', ''),
('tw_api_key_secret', ''),
('verify_email', 'off');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tag`
--

CREATE TABLE `tag` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `label_id` int(10) UNSIGNED NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `token`
--

CREATE TABLE `token` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `verify_email` varchar(50) NOT NULL,
  `change_email` varchar(50) NOT NULL,
  `reset_password` varchar(50) NOT NULL,
  `unlink_email` varchar(50) NOT NULL,
  `2check` varchar(50) NOT NULL,
  `expires` text DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `typing`
--

CREATE TABLE `typing` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `profile_id` int(10) UNSIGNED NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(40) CHARACTER SET utf8 NOT NULL,
  `user_changed` int(11) NOT NULL DEFAULT 0,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `new_email` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `ip` varchar(43) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `surname` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `birthday` int(11) NOT NULL DEFAULT 0,
  `birthday_changed` int(11) NOT NULL DEFAULT 0,
  `gender` set('male','female') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'male',
  `language` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en',
  `darkmode` tinyint(1) NOT NULL DEFAULT 0,
  `avatar` varchar(128) CHARACTER SET latin1 NOT NULL DEFAULT 'default-holder',
  `about` text CHARACTER SET utf8mb4 DEFAULT NULL,
  `facebook` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `twitter` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `instagram` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `main_sonet` set('facebook','twitter','instagram') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'twitter',
  `contact_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` set('pending','active','deactivated','deleted') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `role` set('admin','moderator','publisher','viewer') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'viewer',
  `2check` set('activated','deactivated') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'deactivated',
  `type` set('normal','facebook','twitter','google') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
  `notifications` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `shows` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `view`
--

CREATE TABLE `view` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `post_id` int(10) UNSIGNED NOT NULL,
  `fingerprint` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `widget`
--

CREATE TABLE `widget` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `status` set('enabled','disabled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'disabled',
  `updated_at` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `widget`
--

INSERT INTO `widget` (`id`, `name`, `content`, `type`, `status`, `updated_at`, `created_at`) VALUES
(7, 'post_body', '', 'pbody', 'enabled', 1560990818, 1559974643),
(8, 'aside', '', 'aside', 'enabled', 1561003881, 1561003881),
(9, 'post_top', '', 'ptop', 'enabled', 1560990818, 1559974643),
(10, 'home_top', '', 'htop', 'enabled', 1560990818, 1559974643),
(11, 'home_load', '', 'hload', 'enabled', 1560990818, 1559974643),
(12, 'horiz_posts', '', 'horizposts', 'enabled', 1561003881, 1561003881);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `word`
--

CREATE TABLE `word` (
  `word` varchar(160) NOT NULL,
  `en` text DEFAULT NULL,
  `ar` text DEFAULT NULL,
  `es` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `word`
--

INSERT INTO `word` (`word`, `en`, `ar`, `es`) VALUES
('2check', 'Two Factor Authentication', 'توثيق ذو عاملين', 'Autenticación de dos factores'),
('404_description', 'The page you were looking for does not exist.', 'الصفحة التي تبحث عنها غير موجودة.', 'La página que buscabas no existe.'),
('404_title', '404 Page not found', '404 الصفحة غير موجودة', '404 Pagina no encontrada'),
('about', 'Short description', 'وصف قصير', 'Descripción corta'),
('about_us', 'About us', 'معلومات عنا', 'Sobre nosotros'),
('access_credentials', 'Access credentials', 'الوصول إلى بيانات الاعتماد', 'Credenciales de acceso'),
('account', 'Account', 'مشروع قانون', 'Cuenta'),
('account_menu', 'Account menu', 'قائمة الحساب', 'Menú de la cuenta'),
('account_pending_verification', 'Account pending verification', 'في انتظار التحقق من الحساب', 'Cuenta pendiente de verificación'),
('account_settings', 'account settings', 'إعدادت الحساب', 'Configuraciones de la cuenta'),
('account_was_deactivated_if_need_help', 'This account was deactivated if you need help', 'تم إلغاء تنشيط هذا الحساب إذا كنت بحاجة إلى مساعدة', 'Esta cuenta fue desactivada si necesita ayuda'),
('action', 'Action', 'عمل', 'Acción'),
('action_can_not_undone', 'This action can not be undone.', 'لا يمكن التراجع عن هذا الإجراء.', 'Esta acción no se puede deshacer.'),
('activated', 'Activated', 'مفعل', 'Activada'),
('activate_night_mode', 'Activate night mode', 'تنشيط الوضع الليلي', 'Activar modo nocturno'),
('add', 'Add', 'يضيف', 'Agregar'),
('added_collaborator_one_posts', 'He added you as a collaborator on one of his posts', 'لقد أضافك كمتعاون في إحدى مشاركاته', 'Te agregó como colaborador en una de sus publicaciones'),
('add_a_new_entry', 'Add a new post', 'أضف منشور جديد', 'Agregar una nueva entrada'),
('add_a_post', 'Add a post', 'أضف منشورًا', 'Agregar una publicación'),
('all', 'All', 'الجميع', 'Todas'),
('all_', 'All', 'الجميع', 'Todo'),
('all_content_published', 'All published content', 'كل المحتوى المنشور', 'Todo el contenido publicado'),
('all_content_published_day', 'All content published on the day', 'تم نشر جميع المحتويات في اليوم', 'Todo el contenido publicado el día'),
('all_content_published_in', 'All content posted on', 'تم نشر جميع المحتويات على', 'Todo el contenido publicado en'),
('all_rights_reserved', 'All rights reserved', 'كل الحقوق محفوظة', 'Todos los derechos reservados'),
('all_the_news', 'All the news', 'كل الأخبار', 'Todas las noticias'),
('already_have_an_account', 'Do you already have an account?', 'هل لديك حساب بالفعل؟', '¿Ya tienes una cuenta?'),
('already_logged_in', 'You are already logged in', 'انت بالفعل داخل', 'Ya has iniciado sesión'),
('and', 'and', 'ص', 'y'),
('another_reason_unsubscribe', 'Another reason to unsubscribe', 'سبب آخر لإلغاء الاشتراك', 'Otra razón para darse de baja'),
('answer', 'Answer', 'إجابه', 'Responder'),
('answered', 'Answered', 'أجاب', 'Respondido'),
('answered_you', 'answered you', 'أجابك', 'te respondió'),
('answering_to', 'Answering to', 'الرد على', 'Respondiendo a'),
('answers', 'answers', 'الإجابات', 'respuestas'),
('april', 'April', 'أبريل', 'Abril'),
('archived', 'Archived', 'مؤرشف', 'Archivado'),
('arent_trying_access', 'Aren\'t you the one trying to access?', 'ألست أنت من يحاول الوصول؟', '¿No eres tú quien intenta acceder?'),
('are_you_sure', 'Are you sure?', 'هل أنت واثق؟', '¿Está segur@?'),
('ask_for_information', 'Ask for information', 'اسأل للحصول على معلومات', 'Solicitar Información'),
('attached_file', 'Attached file', 'ملف مرفق', 'Archivo adjunto'),
('attribute', 'attribute', 'ينسب', 'atributo'),
('at_the_time', 'At the time', 'في الوقت', 'En el momento'),
('august', 'August', 'أغسطس', 'Agosto'),
('author', 'Author', 'مؤلف', 'Autor'),
('authors', 'Authors', 'المؤلفون', 'Autores'),
('back', 'To return', 'لكي ترجع', 'Regresar'),
('background', 'Background', 'خلفية', 'Fondo'),
('back_', 'Behind', 'خلف', 'Atrás'),
('back_to_settings', 'Back to settings', 'رجوع إلى الإعدادات', 'Regresar a configuraciones'),
('block', 'Block', 'حاجز', 'Bloquear'),
('blocked_users', 'Blocked users', 'مستخدمين محجوبين', 'Usuarios bloqueados'),
('blocking_user_takes_effect_access', 'Blocking this user takes effect on both parties, they will no longer have access to each other to:', 'يسري حظر هذا المستخدم على كلا الطرفين ، ولن يكون بإمكانهما الوصول إلى بعضهما البعض من أجل:', 'Bloquear este usuario tiene efecto en ambas partes, dejarán de tener acceso uno del otro a:'),
('block_this_user', 'Block this user?', 'منع هذا المستخدم؟', '¿Bloquear este usuario?'),
('border', 'Border', 'الحدود', 'Borde'),
('browser', 'Browser', 'المستعرض', 'Navegador'),
('browser_isnt_supported_anymore', 'Your browser isn\'t supported anymore. Update it to get the best {$settings->title} experience and our latest features.', 'لم يعد متصفحك مدعومًا. قم بتحديثه للحصول على أفضل تجربة لـ {$settings->title} وأحدث الميزات.', 'Su navegador ya no es compatible. Actualízalo para obtener la mejor experiencia {$settings->title} y nuestras funciones más recientes.'),
('browser_not_supported', 'Browser not supported', 'المستعرض غير مدعوم', 'Navegador no compatible'),
('browser_up_date', 'Your browser is up to date', 'متصفحك محدث', 'Su navegador está actualizado'),
('browse_site', 'Browse {$settings->title}', 'تصفح {$settings->title}', 'Explorar {$settings->title}'),
('by', 'By', 'بواسطة', 'Por'),
('cancel', 'Cancel', 'يلغي', 'Cancelar'),
('can_configure_want_receive', 'You can configure what you want to receive and what not at the top, just look for the settings icon', 'يمكنك تكوين ما تريد تلقيه وما ليس في الجزء العلوي ، ما عليك سوى البحث عن رمز الإعدادات', 'Puedes configurar que deseas recibir y que no en la parte de arriba, solo busca el icono de configuraciones'),
('can_only_change_date_birth', 'You can only change your date of birth once, after that you will not be able to change it again', 'يمكنك تغيير تاريخ ميلادك مرة واحدة فقط ، وبعد ذلك لن تتمكن من تغييره مرة أخرى', 'Solo puedes cambiar tu fecha de nacimiento una vez, luego de esto no podrás volver a cambiarla'),
('can_use_latest_features', 'You can use the latest {$settings->title} features!', 'يمكنك استخدام أحدث ميزات {$settings->title}!', '¡Puedes usar las funciones más recientes de {$settings->title}!'),
('carousel', 'Carousel', 'دائري', 'Carrusel'),
('carousel_of_images', 'Carousel of images', 'دائري للصور', 'Carrusel de imágenes'),
('carousel_progress_bar', 'Carousel progress bar', 'شريط تقدم الرف الدائري', 'Barra de progreso del carrusel'),
('categories', 'Categories', 'فئات', 'Categorías'),
('category', 'Category', 'فئة', 'Categoría'),
('category_business', 'Business', 'اعمال', 'Negocio'),
('category_clothes', 'Clothes', 'ملابس', 'Ropa'),
('category_design', 'Design', 'تصميم', 'Diseño'),
('category_fashion', 'Fashion', 'موضه', 'Moda'),
('category_nature', 'Nature', 'طبيعة سجية', 'Naturaleza'),
('category_photography', 'Photography', 'التصوير', 'Fotografía'),
('category_places', 'Places', 'أماكن', 'Lugares'),
('category_sport', 'Sport', 'رياضة', 'Deporte'),
('category_travel', 'Travel', 'يسافر', 'Viajar'),
('change', 'Change', 'يتغيرون', 'Cambiar'),
('change_password', 'Change Password', 'غير كلمة السر', 'Cambiar contraseña'),
('change_your_password', 'change your password', 'غير كلمة المرور الخاصة بك', 'cambiar tu contraseña'),
('chats', 'Chats', 'دردشة', 'Chats'),
('check_your_email', 'Check your email', 'تحقق من بريدك الالكتروني', 'Verifica tu correo'),
('choose', 'Choose', 'لإختيار', 'Elegir'),
('clean_up', 'Clean up', 'عملية تنظيف', 'Limpiar'),
('close', 'To close', 'لإغلاق', 'Cerrar'),
('close_breaking_news_ad', 'Close breaking news ad', 'إغلاق إعلان الأخبار العاجلة', 'Cerrar anuncio de noticia de última hora'),
('close_menu', 'Close menu', 'إغلاق القائمة', 'Cerrar menú'),
('close_newsletter', 'Close newsletter', 'إغلاق النشرة', 'Cerrar boletín'),
('collaborations', 'Collaborations', 'التعاون', 'Colaboraciones'),
('collaborator', 'Collaborator', 'متعاون', 'Colaborador'),
('collaborators', 'Collaborators', 'المتعاونون', 'Colaboradores'),
('color', 'Text', 'نص', 'Texto'),
('comment', 'Comment', 'تعليق', 'Comentar'),
('commented_one_your_posts', 'Has commented on one of your posts', 'علقت على واحدة من مشاركاتك', 'Ha comentado una de tus publicaciones'),
('comments', 'Comments', 'تعليقات', 'Comentarios'),
('compatible_sites', 'Compatible Sites', 'المواقع المتوافقة', 'Sitios compatibles'),
('configuration_tells_send_news', 'This configuration tells us whether or not we should send news by email.', 'يخبرنا هذا التكوين ما إذا كان ينبغي لنا إرسال الأخبار عبر البريد الإلكتروني أم لا.', 'Esta configuración nos indica si debemos o no enviar noticias por correo electronico.'),
('configuration_updated', 'Configuration updated!', 'تم تحديث التكوين!', '¡Configuración actualizada!'),
('confirm_are_who_trying_enter', 'Confirm that it is you who is trying to enter.', 'تأكد من أنك أنت من تحاول الدخول.', 'Confirma que eres tú quien intenta ingresar.'),
('confirm_code', 'Enter your confirmation code', 'أدخل رمز التأكيد الخاص بك', 'Introduce tu código de confirmación'),
('confirm_password', 'Confirm Password', 'تأكيد كلمة المرور', 'Confirmar contraseña'),
('consulted_source', 'Consulted source', 'مصدر استشاري', 'Fuente consultada'),
('consulted_sources', 'Sources consulted', 'مصادر استشارية', 'Fuentes consultadas'),
('contact_email', 'contact email', 'تواصل بالبريد الاكتروني', 'correo de contacto'),
('contact_us', 'Contact us', 'اتصل بنا', 'Contactanos'),
('contents', 'Contents', 'محتويات', 'Contenido'),
('continue', 'Continue', 'يكمل', 'Continuar'),
('continue_with', 'Continue with', 'تواصل مع', 'Continuar con'),
('cookie_consent', 'Cookie consent', 'موافقة ملفات تعريف الارتباط', 'Consentimiento de cookies'),
('copyright', 'Copyright © {$year_now} {$settings->title}.', 'حقوق الطبع والنشر © {$year_now} {$settings->title}.', 'Copyright © {$year_now} {$settings->title}.'),
('copy_link_to_share', 'Copy link to share', 'انسخ الرابط للمشاركة', 'Copiar enlace para compartir'),
('could_not_send_message_error', 'An error occurred, the message could not be sent', 'حدث خطأ ، تعذر إرسال الرسالة', 'Ocurrio un error, no se pudo enviar el mensaje'),
('create_account', 'Create account', 'انشئ حساب', 'Crear una cuenta'),
('create_post', 'Create post', 'إنشاء وظيفة', 'Crear publicación'),
('currently_receive_best_information_newsletter', 'Currently you receive the best information through our newsletter', 'تتلقى حاليًا أفضل المعلومات من خلال نشرتنا الإخبارية', 'Actualmente recibes la mejor información a través de nuestro boletín de noticias'),
('current_password', 'Current password', 'كلمة المرور الحالية', 'Contraseña actual'),
('current_password_not_match', 'The current password does not match', 'كلمة المرور الحالية غير متطابقة', 'La contraseña actual no coincide'),
('customer_support', 'Customer Support', 'دعم العملاء', 'Atención al cliente'),
('dailymotion', 'Dailymotion', 'Dailymotion', 'Dailymotion'),
('daily_summary', 'Daily summary', 'ملخص يومي', 'Resumen diario'),
('dark_mode', 'Dark mode', 'الوضع المظلم', 'Modo oscuro'),
('date', 'Date', 'تاريخ', 'Fecha'),
('date_of_birth', 'date of birth', 'تاريخ الولادة', 'fecha de nacimiento'),
('day', 'day', 'يوم', 'día'),
('days', 'days', 'أيام', 'días'),
('deactivated', 'Deactivated', 'معطل', 'Desactivada'),
('deactivate_this_account', 'Deactivate this account', 'تعطيل هذا الحساب', 'Desactiva esta cuenta'),
('december', 'December', 'ديسمبر', 'Diciembre'),
('delete', 'Remove', 'إزالة', 'Eliminar'),
('delete_account', 'Delete account', 'حذف الحساب', 'Eliminar cuenta'),
('delete_account_only_if_sure_so', '<b>Delete your account only if you are sure to do so</b>, since your data such as; your settings, messages, comments, likes, dislikes, notifications and posts. Among many other data, they cannot be recovered after performing this action.', '<b> احذف حسابك فقط إذا كنت متأكدًا من القيام بذلك </b> ، نظرًا لأن بياناتك مثل ؛ إعداداتك ، رسائلك ، تعليقاتك ، إبداءات الإعجاب ، عدم الإعجاب ، الإخطارات والمشاركات. من بين العديد من البيانات الأخرى ، لا يمكن استعادتها بعد تنفيذ هذا الإجراء.', '<b>Elimina tu cuenta solo si estas seguro de hacerlo</b>, ya que tus datos tales como; tu configuración, mensajes, comentarios, likes, dislikes, notificaciones y publicaciones. Entre muchos otros datos, no podrán ser recuperados luego de realizar esta acción.'),
('DELETE_COMMAND', 'REMOVE', 'إزالة', 'ELIMINAR'),
('delete_copy_conversation_wont_back', 'If you delete your copy of this conversation, you won\'t be able to get it back.', 'إذا حذفت نسختك من هذه المحادثة ، فلن تتمكن من استعادتها.', 'Si eliminas tu copia de esta conversación, no podrás recuperarla.'),
('delete_for_me', 'Delete for me', 'حذف من أجلي', 'Eliminar para mí'),
('delete_post', 'Delete post', 'حذف آخر', 'Eliminar publicación'),
('delete_session_number', 'Delete session number', 'احذف رقم الجلسة', 'Eliminar sesión número'),
('deputy_file_deleted', 'Attachment removed', 'تمت إزالة المرفق', 'Archivo adjunto eliminado'),
('describe_reason_report', 'Describe the reason for your report..', 'صف سبب التقرير الخاص بك..', 'Describe el motivo de tu reporte..'),
('describe_the_error', 'Describe the error..', 'صف الخطأ..', 'Describe el error..'),
('describe_type_request', 'Describe the type of request', 'صف نوع الطلب', 'Describa el tipo de solicitud'),
('describe_your_post', 'Describe your post', 'صف رسالتك', 'Describe tu publicación'),
('description', 'description', 'وصف', 'descripción'),
('didnt_create_this_account', 'Didn\'t you create this account?', 'ألم تنشئ هذا الحساب؟', '¿No creaste esta cuenta?'),
('did_want_reset_password', 'Did you want to reset the password?', 'هل تريد إعادة تعيين كلمة المرور؟', '¿querías restablecer la contraseña?'),
('did_you_forget_your_password', 'Did you forget your password?', 'هل نسيت كلمة المرور الخاصة بك؟', '¿Olvidaste tu contraseña?'),
('disabled', 'Disabled', 'معاق', 'Desactivado'),
('disable_night_mode', 'Disable night mode', 'تعطيل الوضع الليلي', 'Desactivar modo nocturno'),
('disconnect_this_account', 'Disconnect this account', 'افصل هذا الحساب', 'Desconectar esta cuenta'),
('dislike', 'Dislike', 'لا يعجبني', 'No me gusta'),
('dismiss_cookie_message', 'Dismiss cookie message', 'تجاهل رسالة ملف تعريف الارتباط', 'Descartar mensaje de cookies'),
('does', 'Does', 'يفعل', 'Hace'),
('dont_like_him', 'I dont like him', 'أنا لا أحبه', 'No le gustó'),
('download', 'Download', 'تسريح', 'Descargar'),
('download_could_not_completed', 'Download could not be completed', 'لا يمكن أن يكتمل التنزيل', 'No se pudo completar la descarga'),
('download_image_from_website', 'Download image from a website', 'تحميل الصورة من موقع على شبكة الإنترنت', 'Descargar imagen de un sitio web'),
('do_not_have_account_yet', 'You do not have an account yet?', 'ليس لديك حساب بعد؟', '¿Todavía no tiene una cuenta?'),
('do_really_want_delete_close', 'Do you really want to delete and close this session?', 'هل تريد حقًا حذف هذه الجلسة وإغلاقها؟', '¿Realmente desea eliminar y cerrar esta sesión?'),
('do_really_want_delete_comment', 'Do you really want to delete this comment?', 'هل تريد حقا حذف هذا التعليق؟', '¿Realmente desea eliminar este comentario?'),
('do_really_want_delete_post', 'Do you really want to delete this post?', 'هل حقا تريد حذف هذه المشاركة؟', '¿Realmente desea eliminar esta publicación?'),
('do_really_want_unblock_user', 'Do you really want to unblock this user?', 'هل تريد حقًا إلغاء حظر هذا المستخدم؟', '¿Realmente desea desbloquear este usuario?'),
('do_you_wish_continue', 'Do you wish to continue?', 'هل ترغب في الاستمرار؟', '¿Desea continuar?'),
('draft', 'Draft', 'ممحاة', 'Borrador'),
('edit', 'Edit', 'يحرر', 'Editar'),
('edit_post', 'Edit post', 'تعديل المنشور', 'Editar publicación'),
('email', 'email', 'بريد الكتروني', 'correo electrónico'),
('email_already_registered', 'This email is already registered', 'عنوان البريد الإلكترونى هذا مسجل بالفعل', 'Este correo ya está registrado'),
('email_associated_with_account', 'This email is associated with a <b>{$red_social}</b> account, if you change the email and verify it, you will no longer be able to log in with <b>{$red_social}</b>.', 'هذا البريد الإلكتروني مقترن بحساب <b>{$red_social}</b> ، إذا قمت بتغيير البريد الإلكتروني وتحقق منه ، فلن تتمكن بعد ذلك من تسجيل الدخول باستخدام <b>{$red_social}</b>.', 'Este correo está asociado a una cuenta de <b>{$red_social}</b>, si cambia el correo electronico y lo verifica, ya no podrá iniciar sesión con <b>{$red_social}</b>.'),
('email_been_sent_click_code', 'An email has been sent. You just need to click the link in the email or enter the code if you have a link.', 'تم ارسال البريد الإلكتروني. ما عليك سوى النقر فوق الارتباط الموجود في البريد الإلكتروني أو إدخال الرمز إذا كان لديك ارتباط.', 'Un correo electronico ha sido enviado. Solo necesita hacer clic en el enlace del correo electrónico o introducir el codigo si cuenta con un enlace.'),
('email_not_exist', 'This email does not exist', 'هذا البريد الإلكتروني غير موجود', 'Este correo no existe'),
('embed', 'Embed', 'المضمنة', 'Embed'),
('embedded_code', 'Embedded code', 'رمز مضمن', 'Código insertado'),
('enabled', 'Activated', 'مفعل', 'Activado'),
('enter_a_new', 'Please enter a new', 'الرجاء إدخال ملف', 'Ingrese un nuevo'),
('enter_a_new_', 'Please enter a new', 'الرجاء إدخال ملف', 'Ingrese una nueva'),
('enter_a_valid_email', 'Enter a valid email', 'أدخل بريد إلكتروني متاح', 'Introduce un correo valido'),
('enter_a_valid_url', 'Please enter a valid URL', 'أدخل رابط صحيح من فضلك', 'Introduce una URL válida'),
('enter_code', 'Enter code', 'ادخل الرمز', 'Ingresar código'),
('enter_email_address_use_signin', 'Enter the email address you use to sign in and we\'ll send you a link to reset your password.', 'أدخل عنوان البريد الإلكتروني الذي تستخدمه لتسجيل الدخول وسنرسل لك رابطًا لإعادة تعيين كلمة المرور الخاصة بك.', 'Introduce la dirección de correo electrónico que usas para iniciar sesión y te enviaremos un enlace para restablecer tu contraseña.'),
('enter_the_url', 'Enter URL', 'إدخال عنوان الموقع', 'Introduzca la URL'),
('enter_url_image_downloaded', 'Enter the URL of the image to be downloaded', 'أدخل عنوان URL للصورة المراد تنزيلها', 'Ingresa la URL de la imagen para que sea descargada'),
('error_sending_email_again_later', 'Error sending email, please try again later', 'خطأ أثناء إرسال البريد الإلكتروني، يرجى المحاولة مرة أخرى في وقت لاحق', 'Error al enviar el correo, inténtalo de nuevo más tarde'),
('error_uploading_files', 'Error uploading files', 'خطأ في تحميل الملفات', 'Error al subir los archivos'),
('every_time_followed_uploads_post', 'Every time one of the users you follow uploads a new post.', 'في كل مرة يقوم أحد المستخدمين الذين تتابعهم بتحميل منشور جديد.', 'Cada vez que uno de los usuarios que sigues suba una nueva publicación.'),
('every_time_follow_publication_category', 'Every time one of the users you follow uploads a new publication in this category.', 'في كل مرة يقوم أحد المستخدمين الذين تتابعهم بتحميل منشور جديد في هذه الفئة.', 'Cada vez que uno de los usuarios que sigues suba una nueva publicación en esta categoria.'),
('every_time_someone_assigns_contributor', 'Every time someone assigns you as a contributor on a post.', 'في كل مرة يقوم شخص ما بتعيينك كمساهم في منشور.', 'Cada vez que alguien te asigne como colaborador en una publicación.'),
('every_time_someone_comments_posts', 'Every time someone comments on your posts.', 'في كل مرة يعلق شخص ما على مشاركاتك.', 'Cada vez que alguien comente tus publicaciones.'),
('every_time_someone_mentions_comment_replied', 'Every time someone comments on your posts.', 'في كل مرة يذكرك أحدهم في تعليق خاص بك أو قمت بالرد عليه.', 'Cada vez que alguien te mencione en un comentario tuyo o que hayas respondido.'),
('every_time_someone_replies_comments', 'Every time someone replies to your comments.', 'في كل مرة يرد شخص ما على تعليقاتك.', 'Cada vez que alguien responda a tus comentarios.'),
('every_time_user_follows', 'Every time a user follows you.', 'في كل مرة يتبعك مستخدم.', 'Cada vez que algún usuario te siga.'),
('every_user_reacts_pocomrep', 'Every time a user reacts to one of your posts, comments, or replies.', 'في كل مرة يتفاعل المستخدم مع إحدى مشاركاتك أو تعليقاتك أو ردودك.', 'Cada vez que un usuario reaccione en alguna de tus publicaciones, comentarios o respuestas.'),
('facebook', 'Facebook user', 'مستخدم Facebook', 'usuario de Facebook'),
('facebook_', 'Facebook', 'Facebook', 'Facebook'),
('facebook_post', 'Facebook post', 'نشر Facebook', 'Publicación de Facebook'),
('featured', 'Most outstanding', 'الأكثر تميزا', 'Más destacados'),
('featured_answer', 'Featured answer', 'إجابة مميزة', 'Respuesta destacada'),
('featured_comment', 'Featured comment', 'تعليق مميز', 'Comentario destacado'),
('february', 'February', 'شهر فبراير', 'Febrero'),
('female', 'Feminine', 'المؤنث', 'Femenino'),
('field_optional_but_will_help', 'This field is optional, but it would help us understand your message', 'هذا الحقل اختياري ، لكنه سيساعدنا في فهم رسالتك', 'Este campo es opcional, pero nos ayudaría a entender su mensaje'),
('file', 'File', 'ملف ، أرشفة', 'Archivo'),
('file_not_supported', 'The file format is invalid', 'تنسيق الملف غير صالح', 'El formato del archivo no es válido'),
('file_selected_too_large', 'The file you selected is too large. The maximum size is {$file_size_limit}.', 'الملف الذي حددته كبير جدًا. الحجم الأقصى هو {$file_size_limit}.', 'El archivo que seleccionaste es demasiado grande. El tamaño máximo es de {$file_size_limit}.'),
('file_too_big_maximum_size', 'File too big. The maximum size is {$file_size_limit}.', 'ملف كبير جدا. الحجم الأقصى هو {$file_size_limit}.', 'Archivo demasiado grande. El tamaño máximo es de {$file_size_limit}.'),
('filters_by_author', 'Filters by author', 'مرشحات من قبل المؤلف', 'Filtros por author'),
('filters_by_category', 'Filters by category', 'مرشحات حسب الفئة', 'Filtros por categoría'),
('filters_by_date', 'Filters by date', 'مرشحات حسب التاريخ', 'Filtros por fecha'),
('filters_by_order', 'Filters by order', 'مرشحات حسب الطلب', 'Filtros por orden'),
('filter_by_author', 'Filter by author', 'التصفية حسب المؤلف', 'Filtrar por autor'),
('filter_by_author_', 'Filter by author', 'التصفية حسب المؤلف', 'Filtrar por el autor'),
('filter_by_category', 'Filter by category', 'تصفية حسب الفئة', 'Filtrar por categoría'),
('filter_by_category_', 'Filter by category', 'تصفية حسب الفئة', 'Filtrar por la categoría'),
('filter_by_date', 'Filter by date', 'التصفية حسب التاريخ', 'Filtrar por fecha'),
('filter_by_month', 'Filter by month', 'تصفية حسب الشهر', 'Filtrar por mes'),
('filter_by_most_current', 'Filter by most current', 'تصفية حسب الأحدث', 'Filtrar por lo más actual'),
('filter_by_week', 'Filter by week', 'تصفية حسب الأسبوع', 'Filtrar por semana'),
('filter_by_year', 'Filter by year', 'تصفية بالسنة', 'Filtrar por año'),
('find_a_post', 'Find a post', 'ابحث عن وظيفة', 'Buscar una publicación'),
('find_a_user', 'Find a user', 'ابحث عن مستخدم', 'Buscar un usuario'),
('follow', 'To follow', 'للمتابعة', 'Seguir'),
('followed', 'Followed', 'يتبع', 'Seguidos'),
('follower', 'Follower', 'تابع', 'Seguidor'),
('followers', 'Followers', 'متابعون', 'Seguidores'),
('followers_settings', 'followers settings', 'إعدادات المتابعين', 'configuración de seguidores'),
('following', 'Following', 'التالية', 'Siguiendo'),
('follow_us_on', 'Follow us on', 'اتبعنا', 'Síguenos en'),
('footer_copyright_message', 'Copyright © {$year_now} {$settings->title}. All rights reserved.', 'حقوق النشر © {$year_now} {$settings->title}. كل الحقوق محفوظة.', 'Copyright © {$year_now} {$settings->title}. Todos los derechos reservados.'),
('footer_message', 'The best digital magazine for newspapers or bloggers; Warning: this is the beta version, there are features to be developed, v1 will contain those features.', 'أفضل مجلة رقمية للصحف أو المدونين ؛ تحذير: هذا هو الإصدار التجريبي ، وهناك ميزات يجب تطويرها ، وسيحتوي الإصدار 1 على هذه الميزات.', 'La mejor revista digital para periodicos o blogueros; Advertencia: esta es la versión beta, faltan caracacteristicas por desarrollar, la v1 contendrá esas caracacteristicas.'),
('for', 'For', 'إلى عن على', 'Para'),
('forgot_your_password', 'Did you forget your password?', 'هل نسيت كلمة المرور الخاصة بك؟', '¿Olvidaste tu contraseña?'),
('form_no_longer_valid_please_again_later', 'This form is no longer valid, please try again later.', 'هذا النموذج لم يعد صالحًا ، يرجى المحاولة مرة أخرى لاحقًا.', 'Este formulario ya no es válido, por favor intenta de nuevo más tarde.'),
('frequency', 'Frequency', 'تكرار', 'Frecuencia'),
('friday', 'Friday', 'جمعة', 'Viernes'),
('fullname', 'Full name', 'اسم كامل', 'Nombre completo'),
('f_one', 'Life Style', 'لايف ستايل', 'Estilo de vida'),
('f_two', 'Social', 'اجتماعي', 'Social'),
('gender', 'gender', 'جنس', 'genero'),
('get_image', 'Get image', 'احصل على الصورة', 'Obtener imagen'),
('get_inspired_write_something_delight_readers', 'Get inspired and write something to delight your readers...', 'احصل على الإلهام واكتب شيئًا يسعد قراءك ...', 'Inspírate y escribe algo para deleitar a tus lectores...'),
('get_into', 'Get into', 'ندخل', 'Ingresar'),
('good', 'Good', 'جيد', 'Buena'),
('good_morning', 'Good morning', 'مرحبًا', 'Buenos días'),
('got_your_password', 'Do you have your password?', 'هل لديك كلمة السر الخاصة بك؟', '¿Tienes tu contraseña?'),
('go_down', 'Go down', 'انزل', 'Bajar'),
('half', 'Half', 'نصف', 'Media'),
('has_replied_comment', 'has replied to your comment', 'رد على تعليقك', 'Ha respondido a tu comentario'),
('has_started_following', 'has started following you', 'بدأ في متابعتك', 'Ha comenzado a seguirte'),
('have_already_changed_username_change_day', 'You have already changed your username, you can change it again on the day', 'لقد قمت بالفعل بتغيير اسم المستخدم الخاص بك ، يمكنك تغييره مرة أخرى في نفس اليوم', 'Ya has modificado tu nombre de usuario, podrás volver a cambiarlo el día'),
('have_already_reported_comment', 'You have already reported this comment', 'لقد سبق أن ذكرت هذا التعليق', 'Ya has reportado este comentario'),
('have_already_reported_post', 'You have already reported this post', 'لقد أبلغت بالفعل عن هذا المنشور', 'Ya has reportado esta publicación'),
('have_already_reported_user', 'You have already reported this user', 'لقد أبلغت بالفعل عن هذا المستخدم', 'Ya has reportado este usuario'),
('have_been_one_who_has_carried_out', 'If you have not been the one who has performed this action, you do not have to do anything about it, you can ignore this email.', 'إذا لم تكن الشخص الذي نفذ هذا الإجراء ، فلا يتعين عليك فعل أي شيء حيال ذلك ، يمكنك تجاهل هذا البريد الإلكتروني.', 'Si no has sido tú quien ha realizado esta acción, no tienes que hacer nada al respecto, puedes ignorar este correo.'),
('have_been_very_successful', 'You have successfully unsubscribed!', 'لقد تم إلغاء اشتراكك بنجاح!', '¡Te has dado de baja con éxito!'),
('hello', 'Hi {$username}!', 'مرحبًا {$username}!', '¡Hola {$username}!'),
('help_us_understand_why', 'Help us understand why you want to make this decision', 'ساعدنا في فهم سبب رغبتك في اتخاذ هذا القرار', 'Ayudanos a entender porque quieres tomar esta decisión'),
('he_mentioned_comment', 'He mentioned you in a comment', 'لقد ذكرك في تعليق', 'Te mencionó en un comentario'),
('hidden_link', 'Hidden link', 'رابط مخفي', 'Enlace oculto'),
('hide', 'Disguise', 'تمويه', 'Ocultar'),
('home', 'Start', 'البدء', 'Inicio'),
('hour', 'hour', 'ساعة', 'hora'),
('hours', 'hours', 'ساعات', 'horas'),
('hover', 'Pseudo-class hover', 'فئة زائفة Hover', 'Pseudo-clase hover'),
('if_wish_contact_customer_service_center', 'If you wish to contact our Customer Service Center, fill out the following form.', 'إذا كنت ترغب في الاتصال بمركز خدمة العملاء لدينا ، فاملأ النموذج التالي.', 'Si deseas ponerte en contacto con nuestro Centro de Atención al Cliente rellena el siguiente formulario.'),
('illustration', 'Illustration', 'توضيح', 'Ilustración'),
('image', 'Image', 'صورة', 'Imagen'),
('images_taken_from', 'Images taken from', 'الصور مأخوذة من', 'Imágenes tomadas de'),
('image_taken_from', 'Image taken from', 'الصورة مأخوذة من', 'Imagen tomada de'),
('incorrect_user_password', 'Incorrect user or password', 'مستخدم أو كلمة مرور غير صحيحة', 'Usuario o contraseña incorrecta'),
('instagram', 'Instagram user', 'مستخدم Instagram', 'usuario de Instagram'),
('instagram_', 'Instagram', 'Instagram', 'Instagram'),
('instagram_post', 'Instagram post', 'نشر Instagram', 'Publicación de Instagram'),
('invalid_request', 'Invalid request', 'طلب غير صالح', 'Solicitud no válida'),
('in_settings', 'in settings', 'في الاعدادات', 'en configuraciones'),
('ip_address', 'IP adress', 'عنوان IP', 'Dirección IP'),
('is_writing', 'Is writing...', 'يكتب...', 'Está escribiendo...'),
('i_agree_with_in_your', 'I agree with {$settings->title} in your', 'أوافق على {$settings->title} في ملفك', 'Estoy de acuerdo con {$settings->title} en sus'),
('i_have_it', 'I have it!', 'املكه!', '¡Lo tengo!'),
('january', 'January', 'يناير', 'Enero'),
('july', 'July', 'يوليو', 'Julio'),
('june', 'June', 'يونيه', 'Junio'),
('just_changed_date_birth_day', 'You changed your date of birth on', 'لقد غيرت تاريخ ميلادك في', 'Modificaste tu fecha de nacimiento el día'),
('keep_me_signed_in', 'Keep me signed in', 'ابقني مسجل', 'Mantener sesión iniciada'),
('know_more', 'Know more', 'تعرف أكثر', 'Saber más'),
('language', 'Language', 'لغة', 'Lenguaje'),
('languages', 'Languages', 'التعبيرات الاصطلاحية', 'Lenguajes'),
('last_week', 'In the past week', 'في الاسبوع الماضي', 'La semana pasada'),
('latest_from', 'The latest of', 'أحدث من', 'Lo último de'),
('latest_in', 'Latest in', 'الأحدث في', 'Lo último en'),
('learn_more_about_cookies', 'Learn more about cookies', 'تعرف على المزيد حول ملفات تعريف الارتباط', 'Aprender más sobre las cookies'),
('leave_page_changes_will_saved', 'If you leave the page your changes will not be saved', 'إذا غادرت الصفحة ، فلن يتم حفظ تغييراتك', 'Si abandona la página sus cambios no se guardarán'),
('less_details', 'Less details', 'تفاصيل أقل', 'Menos detalles'),
('let_us_know', 'let us know', 'دعنا نعرف', 'avísanos'),
('light_mode', 'Light mode', 'وضع الضوء', 'Modo claro'),
('like', 'Like', 'أحبها', 'Me gusta'),
('liked_it', 'liked it', 'أعجبني', 'Le gustó'),
('link', 'Link', 'نهاية لهذه الغاية', 'Enlace'),
('link_copied_clipboard', 'Link copied to clipboard!', 'تم نسخ الرابط إلى الحافظة!', '¡Enlace copiado en el portapapeles!'),
('load_more', 'Load more', 'تحميل المزيد', 'Cargar más'),
('load_more_answers', 'Load more answers', 'تحميل المزيد من الإجابات', 'Cargar más respuestas'),
('login', 'Log in', 'تسجيل الدخول', 'Iniciar sesión'),
('logins', 'Logins', 'عمليات تسجيل الدخول', 'Inicios de sesión'),
('logout', 'Log out', 'تسجيل خروج', 'Cerrar sesión'),
('looks_like_author_has_blocked_you', 'Looks like this author has blocked you :(', 'يبدو أن هذا المؤلف قد حظرك :(', 'Parece que este autor te a bloqueado :('),
('made_too_many_attempts_try', 'You made too many attempts, try again in 1 hour', 'لقد أجريت محاولات كثيرة جدًا ، حاول مرة أخرى خلال ساعة واحدة', 'Realizaste demasiados intentos, prueba nuevamente en 1 hora'),
('mail_sent_successfully', 'Mail sent successfully!', 'إرسال البريد بنجاح!', '¡Correo enviado exitosamente!'),
('main_social_network', 'main social network', 'الشبكة الاجتماعية الرئيسية', 'red social principal'),
('make_account_more_secure_case_loss', 'To make your account more secure in case of loss, we need to verify that your email address is active. We will send an email to your address. Simply click the \"Verify\" button or insert the submitted code if you already have a link', 'لجعل حسابك أكثر أمانًا في حالة فقدانه ، نحتاج إلى التحقق من أن عنوان بريدك الإلكتروني نشط. سنرسل بريدًا إلكترونيًا إلى عنوانك. ما عليك سوى النقر فوق الزر \"تحقق\" أو أدخل الرمز المقدم إذا كان لديك رابط بالفعل', 'Para que su cuenta sea mas segura en caso de perdida, necesitamos verificar que su dirección de correo electrónico esté activa. Le enviaremos un correo electrónico a su dirección. Simplemente haga clic en el botón \"Verificar\" o inserta el codigo enviado si ya tiene un enlace'),
('male', 'Male', 'ذكر', 'Masculino'),
('march', 'March', 'يمشي', 'Marzo'),
('may', 'May', 'مايو', 'Mayo'),
('mentions', 'Mentions', 'يذكر', 'Menciones'),
('messages', 'Messages', 'رسائل', 'Mensajes'),
('message_settings', 'message settings', 'إعدادات الرسالة', 'configuración de mensajes'),
('message_was_deleted', 'This message was deleted', 'تم حذف هذه الرسالة', 'Este mensaje fue eliminado'),
('message_will_deleted_people', 'The message will be deleted for you, but other people in the chat will still see it.', 'سيتم حذف الرسالة من أجلك ، لكن سيظل بإمكان الأشخاص الآخرين في الدردشة رؤيتها.', 'Se eliminará el mensaje para ti, pero las demás personas del chat seguirán viéndolo.'),
('message_will_unsent_everyone_chat', 'This message will be unsent to everyone in the chat. Others may have already seen it.', 'لن يتم إرسال هذه الرسالة إلى جميع المشاركين في الدردشة. قد يكون الآخرون قد رأوها بالفعل.', 'Se anulará el envío de este mensaje para todas las personas del chat. Es posible que los demás ya lo hayan visto.'),
('minute', 'minute', 'اللحظة', 'minuto'),
('minutes', 'minutes', 'الدقائق', 'minutos'),
('missing_fields_fill', 'Missing fields to fill', 'الحقول المفقودة للتعبئة', 'Faltan campos por llenar'),
('modified', 'Modified', 'المعدل', 'Modificado'),
('modify_message_settings_in', 'Modify your message settings in', 'قم بتعديل إعدادات الرسائل الخاصة بك في', 'Modifica tu configuración de mensajes en'),
('monday', 'Monday', 'الاثنين', 'Lunes'),
('month', 'month', 'شهر', 'mes'),
('months', 'months', 'الشهور', 'meses'),
('more', 'Plus', 'زائد', 'Más'),
('more_details', 'More details', 'المزيد من التفاصيل', 'Más detalles'),
('more_files', 'More files', 'المزيد من الملفات', 'Más archivos'),
('more_information', 'More information', 'معلومات اكثر', 'Más información'),
('more_news', 'More news', 'المزيد من الأخبار', 'Más noticias'),
('more_popular', 'More popular', 'اكثر شهرة', 'Más popular'),
('most_answered', 'Most answered', 'الأكثر إجابة', 'Más respondidos'),
('most_viewed', 'Most viewed', 'الأكثر مشاهدة', 'Más visto'),
('much_content', 'I get too many emails', 'تلقيت الكثير من رسائل البريد الإلكتروني', 'Recibo demasiados correos'),
('must_include_least_characters', 'Must include at least 8 characters', 'يجب أن يحتوي على 8 أحرف على الأقل', 'Debe incluir al menos 8 caracteres'),
('must_include_least_one', 'Must include at least one', 'يجب أن يتضمن واحدًا على الأقل', 'Debe incluir al menos un'),
('must_insert_more_one_image', 'You must insert more than one image', 'يجب عليك إدراج أكثر من صورة واحدة', 'Debes insertar más de una imagen'),
('name', 'name', 'اسم', 'nombre'),
('name_large_maximum_characters', 'Name too big, maximum 55 characters allowed', 'الاسم كبير جدًا ، الحد الأقصى المسموح به هو 55 حرفًا', 'Nombre demasiado grande, se permite maximo 55 caracteres'),
('never_authorice', 'I never authorized them to send me these emails', 'لم أصرح لهم مطلقًا بإرسال رسائل البريد الإلكتروني هذه إلي', 'Nunca autorice a que me enviaran estos correos'),
('newest', 'Newer', 'أحدث', 'Más nuevo'),
('newsletter_categories', 'Newsletter categories', 'فئات النشرة الإخبارية', 'Categorías del boletín'),
('newsletter_frequency', 'Newsletter Frequency', 'تردد النشرة الإخبارية', 'Frecuencia del boletín'),
('newsletter_settings', 'Newsletter Settings', 'إعدادات النشرة الإخبارية', 'Configuración del boletín informativo'),
('newsletter_updated_success', 'Newsletter Updated Successfully!', 'تم تحديث النشرة بنجاح!', '¡Boletín actualizado con éxito!'),
('new_message', 'New message', 'رسالة جديدة', 'Nuevo Mensaje'),
('next', 'Next', 'التالية', 'Siguiente'),
('next_', 'Ahead', 'امام', 'Adelante'),
('no', 'No', 'لا', 'No'),
('none', 'None', 'لا أحد', 'Ninguna'),
('normal', 'Normal', 'طبيعي', 'Normal'),
('note', 'Note', 'ملحوظة', 'Nota'),
('notifications', 'Notifications', 'إشعارات', 'Notificaciones'),
('notifications_will_saved_here', 'Your notifications will be saved here', 'سيتم حفظ إشعاراتك هنا', 'Tus notificaciones se guardarán aquí'),
('not_create_account_associated_email', 'If you did not create this account associated with this email {$email}, please let us know so we can take the appropriate steps to disable this account.', 'إذا لم تقم بإنشاء هذا الحساب المرتبط بهذا البريد الإلكتروني {$email} ، فيرجى إخبارنا حتى نتمكن من اتخاذ الخطوات المناسبة لتعطيل هذا الحساب.', 'Si no creó esta cuenta asociada con este correo electrónico {$email}, háganoslo saber para que podamos tomar las medidas adecuadas para deshabilitar esta cuenta.'),
('november', 'November', 'شهر نوفمبر', 'Noviembre'),
('now', 'Now', 'حاليا', 'Ahora'),
('no_anymore', 'I no longer wish to receive these emails', 'لم أعد أرغب في تلقي رسائل البريد الإلكتروني هذه', 'Ya no deseo recibir estos correos'),
('no_content', 'Emails are no longer relevant to me', 'لم تعد رسائل البريد الإلكتروني ذات صلة بي', 'Los correos ya no tienen relevancia para mi'),
('no_js_message', 'It appears that JavaScript may be disabled in your browser, which is causing problems on this page. Please check your browser settings to enable JavaScript.', 'يبدو أنه قد يتم تعطيل JavaScript في متصفحك ، مما يتسبب في حدوث مشكلات في هذه الصفحة. يرجى التحقق من إعدادات المتصفح الخاص بك لتمكين JavaScript.', 'Parece que JavaScript puede estar deshabilitado en su navegador, lo que está causando problemas en esta página. Compruebe la configuración de su navegador para habilitar JavaScript.'),
('no_js_title', 'Please enable JavaScript', 'الرجاء تمكين JavaScript', 'Por favor, habilite JavaScript'),
('no_longer_wish_in', 'No longer wish to be in {$settings->title}?', 'لم تعد ترغب في أن تكون في {$settings->title}؟', '¿Ya no deseas estar en {$settings->title}?'),
('no_messages_found_moment', 'No messages found for the moment', 'لم يتم العثور على رسائل في الوقت الحالي', 'No se encontraron mensajes por el momento'),
('no_posts_found_moment', 'No posts found at the moment', 'لا توجد مشاركات وجدت في الوقت الراهن', 'No se encontraron publicaciones por el momento'),
('no_result', 'Sorry, no results were found', 'عذرا، لم يتم العثور على نتائج', 'Lo sentimos, no se encontraron resultados'),
('no_result_for', 'Sorry, no results were found for', 'عذرا ، لم يتم العثور على نتائج ل', 'Lo sentimos, no se encontraron resultados para'),
('number_answer_options', 'Number answer options', 'عدد خيارات الإجابة', 'Opciones de la respuesta número'),
('number_comment_options', 'Number comment options', 'عدد خيارات التعليق', 'Opciones del comentario número'),
('october', 'October', 'اكتوبر', 'Octubre'),
('of', 'of', 'من', 'de'),
('oldest', 'Oldest', 'اكبر سنا', 'Más viejo'),
('oldest_', 'Oldest', 'أقدم', 'Más antiguos'),
('one_file_too_big_maximum_size', 'One of the files is too large. The maximum size is {$file_size_limit}.', 'أحد الملفات كبير جدًا. الحجم الأقصى هو {$file_size_limit}.', 'Uno de los archivos es demasiado grande. El tamaño máximo es de {$file_size_limit}.'),
('one_of_your_answers', 'one of your answers', 'أحد إجاباتك', 'una de tus respuestas'),
('one_of_your_comments', 'one of your comments', 'أحد تعليقاتكم', 'uno de tus comentarios'),
('one_of_your_posts', 'one of your posts', 'واحدة من مشاركاتك', 'una de tus publicaciones'),
('one_who_has_registered_this_account', 'If you have not been the one who has registered this account', 'إذا لم تكن أنت الشخص الذي قام بتسجيل هذا الحساب', 'Si no has sido tú quien ha registrado esta cuenta'),
('oops_error_has_occurred', 'Whoops! An error has occurred', 'عذرًا! حدث خطأ', '¡Ups! Ha ocurrido un error'),
('operating_system', 'Operating system', 'نظام التشغيل', 'Sistema operativo'),
('optional', 'Optional', 'اختياري', 'Opcional'),
('options', 'Options', 'خيارات', 'Opciones'),
('or', 'or', 'أيضاً', 'o'),
('other', 'Other', 'آخر', 'Otro'),
('other_settings', 'Other settings', 'اعدادات اخرى', 'Otras configuraciones'),
('page_about_us', 'About us?', 'معلومات عنا؟', '¿Quiénes somos?'),
('page_contact', 'Contact', 'اتصال', 'Contactar'),
('page_delete_account', 'Delete account', 'حذف الحساب', 'Borrar cuenta'),
('page_habeas_data', 'Habeas Data', 'بيانات المثول أمام القضاء', 'Habeas Data'),
('page_not_found', 'Page not found', 'الصفحة غير موجودة', 'Página no encontrada'),
('page_sitemap', 'Site Map', 'خريطة الموقع', 'Mapa del sitio'),
('page_terms_of_use', 'Terms of use', 'تعليمات الاستخدام', 'Términos de uso'),
('password', 'Password', 'كلمة المرور', 'Contraseña'),
('passwords_not_match', 'Passwords do not match', 'كلمة المرور غير مطابقة', 'Las contraseñas no coinciden'),
('password_security', 'Password security', 'أمان كلمة المرور', 'Seguridad de la contraseña'),
('pattern_with_us', 'Pattern with us', 'نمط معنا', 'Pauta con nosotros'),
('pause', 'Pause', 'يوقف', 'Pausa'),
('pending', 'Pending', 'قيد الانتظار', 'Pendiente'),
('personalized', 'Personalized', 'شخصية', 'Personalizado'),
('pin_up', 'Pin up', 'مِرسَاة', 'Anclar'),
('place_your_source_here', 'Place your source here', 'ضع مصدرك هنا', 'Coloca aquí tu fuente'),
('play', 'Play', 'لعب', 'Reproducir'),
('please_enter_full_name', 'Please enter your full name', 'من فضلك ادخل اسمك الكامل', 'Por favor ingresa tu nombre completo'),
('please_enter_valid_username', 'Please enter a valid username', 'الرجاءادخال اسم مستخدم صحيح', 'Ingrese un usuario válido'),
('please_take_moment_select_carefully', 'Please take a moment to select carefully. A precise section!', 'من فضلك خذ لحظة للاختيار بعناية. قسم دقيق!', '¡Por favor, tomese un momento para seleccionar cuidadosamente. ¡Una sección precisa!'),
('please_update_browser', 'Please update your browser', 'الرجاء تحديث المتصفح الخاص بك', 'Por favor actualice su navegador'),
('please_wait', 'Please wait...', 'أرجو الإنتظار...', 'Por favor espera...'),
('post', 'Post', 'بريد', 'Publicar'),
('posts_reported_users_reviewed_staff', 'Posts, comments, and reported users are reviewed 24/7 by {$settings->title} staff. In case of finding any irregularity, penalties are applied to the corresponding accounts. Serious or repeated violations may result in account deactivation.', 'تتم مراجعة المشاركات والتعليقات والمستخدمين المبلغين عنهم على مدار الساعة طوال أيام الأسبوع بواسطة طاقم {$settings->title}. في حالة العثور على أي مخالفة ، يتم تطبيق العقوبات على الحسابات المقابلة. قد تؤدي الانتهاكات الخطيرة أو المتكررة إلى إلغاء تنشيط الحساب.', 'El personal de {$settings->title} revisa las publicaciones, comentarios y usuarios denunciados de forma ininterrumpida. En caso de encontrar alguna irregularidad, se aplican penalizaciones a las cuentas correspondientes. Las infracciones graves o reiteradas pueden dar lugar a la desactivación de la cuenta.'),
('post_body', 'Post Body', 'نص المشاركة', 'Cuerpo de la publicación'),
('post_image', 'Post Image', 'صورة المشاركة', 'Imagen de la publicación'),
('post_shared_user', 'A post shared by a user.', 'وظيفة مشتركة من قبل المستخدم.', 'Una publicación compartida por un usuario.'),
('post_type', 'Post type', 'نوع آخر', 'Tipo de publicación'),
('preview', 'Preview', 'معاينة', 'Vista previa'),
('profile_settings', 'Profile settings', 'إعدادات الملف الشخصي', 'Configuraciones de perfil'),
('publications', 'publications', 'المنشورات', 'publicaciónes'),
('publication_title', 'Publication Title', 'عنوان النشر', 'Titula de publicación'),
('published', 'Published', 'نشرت', 'Publicado'),
('query', 'Query', 'استفسار', 'Consulta'),
('rc_abusive', 'This comment is abusive', 'هذا التعليق مسيء', 'Este comentario es abusivo'),
('rc_disagree', 'I do not agree with this comment', 'أنا لا أتفق مع هذا التعليق', 'No estoy de acuerdo con este comentario'),
('rc_marketing', 'This looks like an advertisement or marketing.', 'هذا يبدو وكأنه إعلان أو تسويق.', 'Esto parece un anuncio o marketing.'),
('rc_offensive', 'This comment is offensive', 'هذا التعليق مسيء', 'Este comentario es ofensivo'),
('reactions', 'Reactions', 'تفاعلات', 'Reacciones'),
('reading_list', 'Reading list', 'قائمة القراءة', 'Lista de lectura'),
('really_want_permanently_delete_account', 'Do you really want to permanently delete your account?', 'هل تريد حقًا حذف حسابك نهائيًا؟', '¿Realmente desea eliminar su cuenta definitivamente?'),
('reason_why_want_unsubscribe', 'Reason why you want to unsubscribe', 'سبب رغبتك في إلغاء الاشتراك', 'Motivo por el que deseas darte de baja'),
('recaptcha_error', 'Error checking reCAPTCHA, please try again', 'خطأ في التحقق من reCAPTCHA ، يرجى المحاولة مرة أخرى', 'Error al comprobar reCAPTCHA, intentalo de nuevo'),
('recent', 'Most recent', 'الأحدث', 'Más recientes'),
('recommended_article', 'Recommended article', 'مقالة موصى بها', 'Artículo recomendado'),
('recommended_posts', 'Recommended Posts', 'المشاركات الموصى بها', 'Publicaciones recomendadas'),
('register', 'Register', 'تحقق في', 'Registrarse'),
('regret_decides_go', 'We\'re sorry you decided to leave :(', 'نأسف لأنك قررت المغادرة :(', 'Lamentamos que decidas irte :('),
('rejected', 'Rejected', 'مرفوض', 'Rechazado'),
('related_posts', 'Related Posts', 'المنشورات ذات الصلة', 'Artículos relacionados'),
('related_topics', 'Related topics', 'مواضيع ذات صلة', 'Temas relacionados'),
('removed', 'Removed!', 'إزالة!', '¡Eliminado!'),
('removed_', 'Removed', 'إزالة', 'Eliminado'),
('remove_from_list', 'Remove from the list', 'إزالة من القائمة', 'Eliminar de la lista'),
('remove_number_lock', 'Remove number lock', 'إزالة قفل الرقم', 'Eliminar bloqueo número'),
('replied_his_own_message', 'Replied to his own message', 'رد على رسالته', 'Respondió a su propio mensaje'),
('replying_own_message', 'Replying to your own message', 'الرد على رسالتك الخاصة', 'Respondiendo tu propio mensaje'),
('report', 'Report', 'أبلغ عن', 'Reportar'),
('report_comment', 'Report comment', 'الإبلاغ عن تعليق', 'Reportar comentario'),
('report_post', 'Report post', 'الإبلاغ عن المشاركة', 'Reportar publicación'),
('report_sent', 'Report sent!', 'تم إرسال التقرير!', '¡Reporte enviado!'),
('report_sent_successfully_reviewed', 'Your report was sent successfully, it will be reviewed soon.', 'تم إرسال تقريرك بنجاح ، وستتم مراجعته قريبًا.', 'Su reporte fue enviado con éxito, pronto será revisado.'),
('report_user', 'Report user', 'أبلغ عن مستخدم', 'Reportar usuario'),
('requested_change_email_need_verify', 'You requested to change your email, so you need to verify the new email. If you want to keep the old one, digitize it again.', 'لقد طلبت تغيير بريدك الإلكتروني ، لذلك تحتاج إلى التحقق من البريد الإلكتروني الجديد. إذا كنت تريد الاحتفاظ بالقديم ، قم برقمته مرة أخرى.', 'Solicitaste cambiar tu correo electronico, por lo que debes verificar el nuevo correo electronico. Si deseas conservar el anterior, digitalo nuevamente.'),
('request_not_found', 'Request not found', 'طلب غير موجود', 'Solicitud no encontrada'),
('resend_code', 'Resend code', 'أعد إرسال الرمز', 'Reenviar código'),
('resend_email', 'Resend e-mail', 'إعادة إرسال البريد الإلكتروني', 'Reenviar correo'),
('reset', 'Reset', 'إعادة تعيين', 'Reiniciar'),
('reset_password', 'Restore password', 'استعادة كلمة السر', 'Restablecer contraseña'),
('results_related_to', 'results related to', 'النتائج المتعلقة بـ', 'resultados relacionados con'),
('retrieve_it_here', 'Retrieve it here', 'استرجعها هنا', 'Recuperala aquí'),
('return_to', 'Back to top', 'عد إلى الأعلى', 'Regresar al inicio'),
('rp_copyright', 'It infringes my copyright', 'انها تنتهك حقوق التأليف والنشر بلدي', 'Infringe mis derechos de autor'),
('rp_thumbnail', 'Thumbnail issues', 'قضايا الصورة المصغرة', 'Problemas con la miniatura'),
('rp_writing', 'Writing error', 'خطأ في الكتابة', 'Error de redacción'),
('rss', 'RSS', 'RSS', 'RSS'),
('ru_copyright', 'This user infringes my copyright', 'هذا المستخدم ينتهك حقوق النشر الخاصة بي', 'Este usuario infringe mis derechos de autor'),
('ru_hate', 'Hate speech against a protected group', 'كلام يحض على الكراهية ضد مجموعة محمية', 'Discurso de odio contra un grupo protegido'),
('ru_picture', 'Problems with the profile picture', 'مشاكل في صورة الملف الشخصي', 'Problemas con la foto de perfil'),
('r_none', 'None of the above options correspond to my problem.', 'لا يتوافق أي من الخيارات المذكورة أعلاه مع مشكلتي.', 'Ninguna de las opciones anteriores corresponde a mi problema.'),
('r_spam', 'Spam', 'رسائل إلكترونية مزعجة', 'Spam'),
('saturday', 'Saturday', 'السبت', 'Sábado'),
('save', 'Save', 'يحفظ', 'Guardar'),
('saved', 'Saved', 'أنقذ', 'Guardado'),
('saved_posts', 'Saved posts', 'المشاركات المحفوظة', 'Publicaciones guardadas'),
('save_article_for_reading_later', 'Save article to read later', 'احفظ المقال لقراءته لاحقًا', 'Guardar artículo para leer más tarde'),
('save_newsletter_settings', 'Save newsletter settings', 'حفظ إعدادات الرسائل الإخبارية', 'Guardar configuración del boletín'),
('search', 'Search', 'بحث', 'Buscar'),
('second', 'second', 'ثانيا', 'segundo'),
('seconds', 'seconds', 'ثواني', 'segundos'),
('sections_navigation', 'Navigation sections', 'أقسام التنقل', 'Secciones de navegación'),
('security_settings', 'Security settings', 'اعدادات الامان', 'Configuraciones de seguridad'),
('seems_that_typed_word_not_correct', 'It seems that the typed word is not correct, check if it is in capital letters', 'يبدو أن الكلمة المكتوبة غير صحيحة ، تحقق مما إذا كانت مكتوبة بأحرف كبيرة', 'Parece que la palabra digitada no es correcta, verifique si esta en mayusculas'),
('see_detailed_settings', 'See detailed configuration', 'انظر التكوين التفصيلي', 'Ver configuración detallada'),
('see_publications', 'See publications', 'انظر المنشورات', 'Ver publicaciones'),
('select_all_categories', 'Select all categories', 'حدد كل الفئات', 'Seleccionar todas las categorías'),
('send', 'Send', 'إرسال', 'Enviar'),
('sending_bulletins_email', 'Sending newsletters by email', 'إرسال الرسائل الإخبارية عن طريق البريد الإلكتروني', 'Envío de boletines por correo electrónico'),
('send_message', 'Send Message', 'أرسل رسالة', 'Enviar mensaje'),
('send_message_to', 'Send a message to', 'أرسل رسالة إلى', 'Envía un mensaje a'),
('send_receive_messages', 'Send or receive messages', 'إرسال أو استقبال الرسائل', 'Enviar o recibir mensajes'),
('september', 'September', 'أيلول', 'Septiembre'),
('settings', 'Settings', 'إعدادات', 'Ajustes'),
('setting_allows_decide_receive_messages', 'This setting allows you to decide whether or not you will receive messages from other users.', 'يتيح لك هذا الإعداد تحديد ما إذا كنت ستتلقى رسائل من مستخدمين آخرين أم لا.', 'Esta configuración te permite decidir si recibiras o no mensajes de otros usuarios.'),
('setting_allows_users_followers', 'This setting allows you to decide whether or not other users can see your number of followers.', 'يتيح لك هذا الإعداد تحديد ما إذا كان بإمكان المستخدمين الآخرين رؤية عدد المتابعين لك أم لا.', 'Esta configuración te permite decidir si otros usuarios pueden o no ver tu numero de seguidores.');
INSERT INTO `word` (`word`, `en`, `ar`, `es`) VALUES
('setting_helps_understand_social_network', 'This setting helps us understand which social network you prefer we prioritize for certain features so that readers can connect with you.', 'يساعدنا هذا الإعداد في فهم الشبكة الاجتماعية التي تفضل أن نعطيها الأولوية لميزات معينة حتى يتمكن القراء من الاتصال بك.', 'Esta configuración nos ayuda a entender que red social prefieres que prioricemos para algunas características y que así los lectores puedan conectar contigo.'),
('set_by', 'Set by', 'التي وضعتها', 'Establecido por'),
('share_on_email', 'Share by mail', 'شارك بالبريد', 'Compartir por correo'),
('share_on_facebook', 'Share on Facebook', 'شارك في Facebook', 'Compartir en Facebook'),
('share_on_twitter', 'Share on Twitter', 'شارك في Twitter', 'Compartir en Twitter'),
('share_on_whatsapp', 'Share on WhatsApp', 'شارك في WhatsApp', 'Compartir en WhatsApp'),
('sharing_options', 'Sharing options', 'خيارات المشاركة', 'Opciones para compartir'),
('show', 'Show', 'ليعرض', 'Mostrar'),
('show_answers', 'Show {!count_replies} replies', 'إظهار {!count_replies} من الردود', 'Mostrar {!count_replies} respuestas'),
('show_everything', 'Show everything', 'اعرض كل شيء', 'Mostrar todo'),
('show_on_my_profile', 'Show on my profile', 'تظهر في ملفي الشخصي', 'Mostrar en mi perfil'),
('sitemap_being_generated_may_take_few_minutes', 'The sitemap is being generated, it may take a few minutes', 'يتم إنشاء خريطة الموقع ، وقد يستغرق الأمر بضع دقائق', 'El mapa del sitio se esta generando, quizá tome algunos minutos'),
('social_copy_save_share_article', 'Social Share Button, Copy Link Button and Save Article Button', 'زر المشاركة الاجتماعية وزر نسخ الرابط وزر حفظ المقال', 'Botón Compartir en redes sociales, botón Copiar enlace y botón Guardar artículo'),
('social_media', 'Social networks', 'الشبكات الاجتماعية', 'Redes sociales'),
('someone_has_reset_password', 'Someone (hopefully you) has asked us to reset the password for your {$settings->title} account. Click the button below to do so. If you didn\'t ask to reset your password, you can ignore this message.', 'طلب منا شخص ما (نتمنى أن تكون أنت) إعادة تعيين كلمة المرور لحساب {$settings->title} الخاص بك. انقر فوق الزر أدناه للقيام بذلك. إذا لم تطلب إعادة تعيين كلمة المرور الخاصة بك ، فيمكنك تجاهل هذه الرسالة.', 'Alguien (esperemos que tú) nos ha solicitado restablecer la contraseña de tu cuenta de {$settings->title}. Haz clic en el botón siguiente para hacerlo. Si no solicitaste restablecer la contraseña, puedes ignorar este mensaje.'),
('some_fields_empty', 'Some fields are empty', 'بعض الحقول فارغة', 'Algunos campos están vacíos'),
('sorry_seems_that_there_no_content', 'Sorry, it seems that there is no content to display yet', 'عذرا ، يبدو أنه لا يوجد محتوى لعرضه حتى الآن', 'Lo sentimos, parece que aún no hay contenido para mostrar'),
('sort_by', 'Order by', 'ترتيب حسب', 'Order por'),
('soundcloud', 'Soundcloud', 'Soundcloud', 'Soundcloud'),
('source', 'Source', 'الخط', 'Fuente'),
('sources', 'Sources', 'مصادر', 'Fuentes'),
('so_that_you_well_informed_we_invite', 'So that you are well informed, we invite you to subscribe to our newsletters.', 'حتى تكون على اطلاع جيد ، ندعوك للاشتراك في نشراتنا الإخبارية.', 'Para que estés bien informado, te invitamos a suscribirte a nuestros boletines.'),
('spotify', 'Spotify', 'Spotify', 'Spotify'),
('stories_save_added_reading_list', 'The stories you save are added to your reading list.', 'تتم إضافة القصص التي قمت بحفظها إلى قائمة القراءة الخاصة بك.', 'Las historias que guardas se agregan a tu lista de lectura.'),
('strong', 'Strong', 'قوي', 'Fuerte'),
('subject', 'Subject', 'الموضوع', 'Asunto'),
('subscribe', 'Subscribe', 'الإشتراك', 'Suscribirme'),
('subscribe_the_newsletter', 'Subscribe to the newsletter', 'اشترك في النشرة الإخبارية', 'Suscribirme al boletín informativo'),
('subscribe_to_our_newsletters', 'Subscribe to our newsletters', 'اشترك في نشراتنا الإخبارية', 'Suscríbete a nuestros boletines'),
('suggestions_requests', 'Suggestions and requests', 'الاقتراحات والطلبات', 'Sugerencias y solicitudes'),
('sunday', 'Sunday', 'الأحد', 'Domingo'),
('supported_symbols', 'Supported symbols', 'الرموز المدعومة', 'Supported symbols'),
('surname', 'surname', 'لقب', 'apellido'),
('surname_large_maximum_characters', 'Surname too big, maximum 55 characters allowed', 'اللقب كبير جدًا ، الحد الأقصى المسموح به هو 55 حرفًا', 'Apellido demasiado grande, se permite maximo 55 caracteres'),
('symbol', 'symbol', 'رمز', 'símbolo'),
('tags', 'Tags', 'العلامات', 'Etiquetas'),
('technical_problems', 'Technical problems', 'مشاكل تقنية', 'Problemas técnicos'),
('text', 'Text', 'نص', 'Texto'),
('the_account_been_deactivated', 'The account has been deactivated', 'تم إلغاء تنشيط الحساب', 'La cuenta ha sido desactivada'),
('the_best_digital_magazine', 'The best digital magazine', 'أفضل مجلة رقمية', 'La mejor revista digital'),
('the_items_cannot_fully_displayed', 'The items cannot be fully displayed, the screen is too small.', 'لا يمكن عرض العناصر بالكامل ، فالشاشة صغيرة جدًا.', 'Los elementos no se pueden visualizar completos, la pantalla es demasiado pequeña.'),
('this_email_is_already_subscribed', 'This email is already subscribed', 'هذا البريد الإلكتروني مشترك بالفعل', 'Este correo ya está suscrito'),
('this_field_is_empty', 'This field is empty', 'هذا الحقل فارغ', 'Este campo está vacío'),
('this_link_automatically_hidden', 'This link was automatically hidden', 'تم إخفاء هذا الارتباط تلقائيًا', 'Se ocultó este enlace automáticamente'),
('this_month', 'This month', 'هذا الشهر', 'Este mes'),
('this_user_disabled_messages', 'This user disabled messages.', 'قام هذا المستخدم بتعطيل الرسائل.', 'Este usuario desactivó los mensajes.'),
('this_week', 'This week', 'هذا الأسبوع', 'Esta semana'),
('this_year', 'This year', 'هذه السنة', 'Este año'),
('thursday', 'Thursday', 'يوم الخميس', 'Jueves'),
('tiktok', 'Tiktok', 'Tiktok', 'Tiktok'),
('title', 'Qualification', 'مؤهل', 'Título'),
('today', 'Today', 'اليوم', 'Hoy'),
('to_go', 'To go', 'توجو', 'Ir'),
('try_again', 'Try again', 'حاول مرة أخرى', 'Intentar de nuevo'),
('try_out', 'Try out', 'محاولة', 'Probar'),
('tuesday', 'Tuesday', 'يوم الثلاثاء', 'Martes'),
('tweet', 'Tweet', 'Tweet', 'Tweet'),
('twitch', 'Twitch', 'Twitch', 'Twitch'),
('twitter', 'Twitter user', 'مستخدم Twitter', 'usuario de Twitter'),
('twitter_', 'Twitter', 'Twitter', 'Twitter'),
('types_of_newsletters', 'Types of newsletters', 'أنواع الرسائل الإخبارية', 'Tipos de boletines'),
('type_the_word', 'Type the word', 'اكتب الكلمة', 'Digite la palabra'),
('unanswered', 'Unanswered', 'لم يتم الرد عليها', 'Sin respuesta'),
('unfollow', 'Stop following', 'وقف التالية', 'Dejar de seguir'),
('unlock', 'Unlock', 'لفتح', 'Desbloquear'),
('unpin', 'Unpin', 'فك', 'Desanclar'),
('unsubmit_all', 'Unsubmit for all', 'لا تقدم للجميع', 'Anular el envío para todos'),
('unsubscribe', 'Unsubscribe', 'إلغاء الاشتراك', 'Darme de baja'),
('unsubscribe_the_newsletter', 'Unsubscribe from the newsletter', 'إلغاء الاشتراك من النشرة الإخبارية', 'Cancelar la suscripción del boletín'),
('unverified', 'Unverified', 'لم يتم التحقق منه', 'Sin verificar'),
('update_notifications', 'Update notifications', 'إخطارات التحديث', 'Actualizar notificaciones'),
('upload', 'Upload', 'اذهب للأعلى', 'Subir'),
('upload_an_image', 'Upload an image', 'تحميل صورة', 'Subir una imagen'),
('upload_a_picture', 'Upload a picture', 'قم بتحميل صورة', 'Subir una foto'),
('upload_change_photo_acceptable_file', 'Upload or change your photo. Acceptable file types are .jpeg, .jpg, or .png. All photos will be reduced to 90 x 90 pixels and 200 x 200 pixels. If you already have a photo, uploading a new photo will overwrite the existing one.', 'تحميل أو تغيير الصورة الخاصة بك. أنواع الملفات المقبولة هي .jpeg أو .jpg أو .png. سيتم تقليل جميع الصور إلى 90 × 90 بكسل و 200 × 200 بكسل. إذا كانت لديك صورة بالفعل ، فسيؤدي تحميل صورة جديدة إلى استبدال الصورة الحالية.', 'Sube o cambia tu foto. Los tipos de archivo aceptables son .jpeg, .jpg o .png. Todas las fotos se reducirán a 90 x 90 píxeles y 200 x 200 píxeles. Si ya tienes una foto, subir una nueva foto sobrescribirá la existente.'),
('user', 'User', 'المستعمل', 'Usuario'),
('username', 'username', 'اسم المستخدم', 'nombre de usuario'),
('username_already_exists', 'This username already exists', 'اسم المستخدم هذا موجود بالفعل', 'Ya existe este nombre de usuario'),
('user_without_login', 'User without login', 'مستخدم بدون تسجيل الدخول', 'Usuario sin loguearse'),
('use_email_login_where_will_send', 'Use this email to login. This is also where we will send email communications and newsletters.', 'استخدم هذا البريد الإلكتروني لتسجيل الدخول. هذا هو المكان الذي سنرسل فيه رسائل البريد الإلكتروني والرسائل الإخبارية.', 'Use este correo electrónico para iniciar sesión. Aquí también es donde enviaremos comunicaciones por correo electrónico y boletines.'),
('using_amp_version_platform_comment', 'You are using an <b>AMP</b> version of our platform, if you wish to comment on this post you will need to do so from the full version.', 'أنت تستخدم إصدار <b>AMP</b> من نظامنا الأساسي ، إذا كنت ترغب في التعليق على هذه المشاركة ، فستحتاج إلى القيام بذلك من النسخة الكاملة.', 'Usted esta utilizando una versión <b>AMP</b> de nuestra plataforma, si desea comentar esta publicación necesitara hacerlo desde la versión completa.'),
('using_amp_version_platform_from_full_version', 'You are using an <b>AMP</b> version of our platform, so you cannot do this from here, you can do it from the full version.', 'أنت تستخدم إصدار <b>AMP</b> من نظامنا الأساسي ، لذا لا يمكنك القيام بذلك من هنا ، يمكنك القيام بذلك من الإصدار الكامل.', 'Usted esta utilizando una versión <b>AMP</b> de nuestra plataforma, por lo que no puede realizar esto desde aquí, puede hacerlo desde la versión completa.'),
('value', 'value', 'يستحق', 'valor'),
('verification_email_sent', 'Verification email sent', 'تم إرسال البريد الإلكتروني للتحقق', 'Correo de verificación enviado'),
('verify_email_address', 'Verify my email address', 'تحقق من عنوان بريدي الإلكتروني', 'Verificar mi dirección de correo electrónico'),
('verify_your_account', 'Check your account', 'تحقق من حسابك', 'Verifica tu cuenta'),
('video', 'Video', 'فيديو', 'Video'),
('video_carousel', 'Video carousel', 'مكتبة الفيديو', 'Carrusel de vídeos'),
('views', 'Views', 'الآراء', 'Vistas'),
('view_post_instagram', 'View this post on Instagram', 'عرض هذا المنشور على Instagram', 'Ver esta publicación en Instagram'),
('view_profile_information', 'View profile information', 'عرض معلومات الملف الشخصي', 'Ver información del perfil'),
('vimeo', 'Vimeo', 'Vimeo', 'Vimeo'),
('watch', 'Watch', 'راقب', 'Ver'),
('weak', 'Weak', 'ضعيف', 'Débil'),
('website_uses_cookies_ensure', 'This website uses cookies to ensure you get the best experience.', 'يستخدم هذا الموقع ملفات تعريف الارتباط لضمان حصولك على أفضل تجربة.', 'Este sitio web utiliza cookies para garantizar que obtenga la mejor experiencia.'),
('wednesday', 'Wednesday', 'الأربعاء', 'Miércoles'),
('weekly_summary', 'Weekly summary', 'ملخص أسبوعي', 'Resumen semanal'),
('we_have_sent_code', 'We have sent a 6 digit code to your email', 'لقد أرسلنا رمزًا مكونًا من 6 أرقام إلى بريدك الإلكتروني', 'Hemos enviado un código de 6 dígitos a su correo electrónico'),
('we_recommend_you', 'we recommend you', 'نوصيك', 'te recomendamos'),
('we_sorry_seems_have_lost_page', 'We are sorry, it seems that we have lost this page, but we do not want to lose you.', 'نحن آسفون ، يبدو أننا فقدنا هذه الصفحة ، لكننا لا نريد أن نفقدك.', 'Lo sentimos, parece que hemos perdido esta página, pero no queremos perderte.'),
('what_we_going_look_today', 'What are we going to look for today? :)', 'ما الذي سنبحث عنه اليوم؟ :)', '¿Que vamos a buscar hoy? :)'),
('when_change_username_will_months', 'When you change your username you will have to wait 3 months before you can change it again.', 'عند تغيير اسم المستخدم الخاص بك ، سيتعين عليك الانتظار 3 أشهر قبل أن تتمكن من تغييره مرة أخرى.', 'Al cambiar tu nombre de usuario tendrás que esperar 3 meses hasta poder volver a modificarlo.'),
('who_want_delete_message_for', 'Who do you want to delete this message for?', 'لمن تريد حذف هذه الرسالة؟', '¿Para quién quieres eliminar este mensaje?'),
('why_do_you_want_leave', 'Why do you want to leave?', 'لماذا تريد المغادرة؟', '¿Por qué deseas marcharte?'),
('widget_aside', 'Sticky ad', 'إعلان مثبت', 'Anuncio sticky'),
('widget_home_load', 'Asynchronous loading of home', 'التحميل غير المتزامن للمنزل', 'Carga asíncrona del home'),
('widget_home_top', 'Ad at the top of the home', 'إعلان في الجزء العلوي من المنزل', 'Anuncio al principio del home'),
('widget_horiz_posts', 'Asynchronous loading of horizontal posts', 'التحميل غير المتزامن للوظائف الأفقية', 'Carga asíncrona de publicaciones horizontales'),
('widget_post_body', 'Ad in the body of a post', 'إعلان في نص المنشور', 'Anuncio en el cuerpo de una publicación'),
('widget_post_top', 'Ad at the beginning of a post', 'إعلان في بداية المنشور', 'Anuncio al principio de una publicación'),
('will_able_retrieve_message', 'You will not be able to retrieve this message.', 'لن تتمكن من استرداد هذه الرسالة.', 'No podrás recuperar este mensaje.'),
('write_a_password', 'Type a password', 'اكتب كلمة السر', 'Escribe una contraseña'),
('write_only_numbers_letters', 'Write only numbers and letters', 'اكتب فقط الأرقام والحروف', 'Escribe solo números y letras'),
('write_something', 'Write something..', 'أكتب شيئا..', 'Escribe algo..'),
('write_your_message', 'Write your message..', 'اكتب رسالتك..', 'Escribe tu mensaje..'),
('wrong_confirm_code', 'Wrong confirmation code', 'رمز التأكيد خاطئ', 'Código de confirmación incorrecto'),
('year', 'Year', 'سنة', 'Año'),
('years', 'Years', 'سنوات', 'Años'),
('yes', 'Yes', 'نعم', 'Si'),
('yesterday', 'Yesterday', 'في الامس', 'Ayer'),
('you', 'You', 'أنت', 'Tu'),
('your_email', 'Your e-mail', 'بريدك الالكتروني', 'Tu correo electrónico'),
('your_post_been_successfully_deleted', 'Your post has been successfully deleted!', 'تم حذف منشورك بنجاح!', '¡Su publicación se ha eliminado con éxito!'),
('youtube', 'Youtube', 'Youtube', 'Youtube'),
('you_are_subscribed', 'You are subscribed!', 'أنت مشترك!', '¡Estás suscrito!'),
('you_cant_see_post', 'You can\'t see this post', 'لا يمكنك مشاهدة هذا المنصب', 'No puedes ver esta publicación'),
('you_create_least_entry', 'You must create at least one entry', 'يجب عليك إنشاء إدخال واحد على الأقل', 'Debes crear al menos una entrada'),
('you_have_successfully_subscribed', 'You have successfully subscribed!', 'لقد تم اشتراكك بنجاح!', '¡Te has suscrito con éxito!'),
('you_just_signed_up', 'You just signed up for {$settings->title} with your {$provider} account. Your username is <b>{$user}</b> and your password is <b>{$code}</b>, you can use them to login.', 'لقد اشتركت للتو في {$settings->title} باستخدام حساب {$Provider} الخاص بك. اسم المستخدم الخاص بك هو <b>{$user}</b> وكلمة المرور الخاصة بك هي <b>{$code}</b> ، يمكنك استخدامها لتسجيل الدخول.', 'Te acabas de registrar en {$settings->title} con tu cuenta de {$provider}. Tu nombre de usuario es <b>{$user}</b> y tu contraseña es <b>{$code}</b>, puedes utilizarlas para iniciar sesión.'),
('you_must_add_another_text_input', 'You must add another text input', 'يجب عليك إضافة إدخال نص آخر', 'Debes agregar otra entrada de texto'),
('you_must_have_minimum_paragraphs', 'You must have a minimum of 5 paragraphs in this entry', 'يجب أن يكون لديك ما لا يقل عن 5 فقرات في هذا الإدخال', 'Debes tener un minimo de 5 parrafos en esta entrada'),
('you_need_complete_your', 'you need to complete your', 'تحتاج إلى إكمال الخاص بك', 'necesita completar su'),
('you_need_fill_profile_description', 'you need to fill in your profile description', 'تحتاج إلى ملء وصف ملف التعريف الخاص بك', 'necesita completar la descripción de su perfil'),
('you_replied_own_message', 'You replied to your own message', 'لقد ردت على رسالتك الخاصة', 'Respondiste a tu propio mensaje'),
('you_responded_to', 'you responded to', 'رددت على', 'Respondiste a '),
('you_write_verification_code', 'You can also write this verification code:', 'يمكنك أيضًا كتابة رمز التحقق هذا:', 'También puedes escribir este código de verificación:');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `block`
--
ALTER TABLE `block`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Indices de la tabla `breaking`
--
ALTER TABLE `breaking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indices de la tabla `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Indices de la tabla `collaborator`
--
ALTER TABLE `collaborator`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indices de la tabla `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ucomment` (`user_id`),
  ADD KEY `fk_pcomment` (`post_id`);

--
-- Indices de la tabla `entry`
--
ALTER TABLE `entry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indices de la tabla `follower`
--
ALTER TABLE `follower`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Indices de la tabla `label`
--
ALTER TABLE `label`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `messaan`
--
ALTER TABLE `messaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `answered_id` (`answered_id`);

--
-- Indices de la tabla `messafi`
--
ALTER TABLE `messafi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indices de la tabla `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Indices de la tabla `newscate`
--
ALTER TABLE `newscate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `newsletter_id` (`newsletter_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indices de la tabla `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `notifier_id` (`notified_id`(1024));

--
-- Indices de la tabla `page`
--
ALTER TABLE `page`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indices de la tabla `reaction`
--
ALTER TABLE `reaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`reacted_id`);

--
-- Indices de la tabla `recobo`
--
ALTER TABLE `recobo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `recommended_id` (`recommended_id`);

--
-- Indices de la tabla `reply`
--
ALTER TABLE `reply`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `comment_id` (`comment_id`);

--
-- Indices de la tabla `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`reported_id`);

--
-- Indices de la tabla `saved`
--
ALTER TABLE `saved`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indices de la tabla `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`name`);

--
-- Indices de la tabla `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `tag_id` (`label_id`);

--
-- Indices de la tabla `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `typing`
--
ALTER TABLE `typing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `view`
--
ALTER TABLE `view`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `widget`
--
ALTER TABLE `widget`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `word`
--
ALTER TABLE `word`
  ADD PRIMARY KEY (`word`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `block`
--
ALTER TABLE `block`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `breaking`
--
ALTER TABLE `breaking`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `category`
--
ALTER TABLE `category`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `collaborator`
--
ALTER TABLE `collaborator`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `entry`
--
ALTER TABLE `entry`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `follower`
--
ALTER TABLE `follower`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `label`
--
ALTER TABLE `label`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `language`
--
ALTER TABLE `language`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `messaan`
--
ALTER TABLE `messaan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `messafi`
--
ALTER TABLE `messafi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `message`
--
ALTER TABLE `message`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `newscate`
--
ALTER TABLE `newscate`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `page`
--
ALTER TABLE `page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `post`
--
ALTER TABLE `post`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reaction`
--
ALTER TABLE `reaction`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recobo`
--
ALTER TABLE `recobo`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reply`
--
ALTER TABLE `reply`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `report`
--
ALTER TABLE `report`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `saved`
--
ALTER TABLE `saved`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `session`
--
ALTER TABLE `session`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `token`
--
ALTER TABLE `token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `typing`
--
ALTER TABLE `typing`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `view`
--
ALTER TABLE `view`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `widget`
--
ALTER TABLE `widget`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `block`
--
ALTER TABLE `block`
  ADD CONSTRAINT `fk_ubprofile` FOREIGN KEY (`profile_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ubuser` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `breaking`
--
ALTER TABLE `breaking`
  ADD CONSTRAINT `fk_pbreaking` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `fk_ucprofile` FOREIGN KEY (`profile_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ucuser` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `collaborator`
--
ALTER TABLE `collaborator`
  ADD CONSTRAINT `fk_pcollab` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ucollab` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `fk_pcomment` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ucomment` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `entry`
--
ALTER TABLE `entry`
  ADD CONSTRAINT `fk_pentry` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `follower`
--
ALTER TABLE `follower`
  ADD CONSTRAINT `fk_ufprofile` FOREIGN KEY (`profile_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ufuser` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `messaan`
--
ALTER TABLE `messaan`
  ADD CONSTRAINT `fk_mamessage` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `messafi`
--
ALTER TABLE `messafi`
  ADD CONSTRAINT `fk_mfile` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `fk_cmessage` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_umprofile` FOREIGN KEY (`profile_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_umuser` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `newscate`
--
ALTER TABLE `newscate`
  ADD CONSTRAINT `fk_cnewsletter` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_nnewsletter` FOREIGN KEY (`newsletter_id`) REFERENCES `newsletter` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `fk_unotify` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `fk_cpost` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `fk_upost` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Filtros para la tabla `reaction`
--
ALTER TABLE `reaction`
  ADD CONSTRAINT `fk_ureact` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recobo`
--
ALTER TABLE `recobo`
  ADD CONSTRAINT `fk_precobo` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rrecobo` FOREIGN KEY (`recommended_id`) REFERENCES `post` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reply`
--
ALTER TABLE `reply`
  ADD CONSTRAINT `fk_creply` FOREIGN KEY (`comment_id`) REFERENCES `comment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ureply` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `fk_ureport` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `saved`
--
ALTER TABLE `saved`
  ADD CONSTRAINT `fk_psaved` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_usaved` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `session`
--
ALTER TABLE `session`
  ADD CONSTRAINT `fk_usession` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tag`
--
ALTER TABLE `tag`
  ADD CONSTRAINT `fk_plabel` FOREIGN KEY (`label_id`) REFERENCES `label` (`id`),
  ADD CONSTRAINT `fk_tpost` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `fk_utoken` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `typing`
--
ALTER TABLE `typing`
  ADD CONSTRAINT `fk_uwprofile` FOREIGN KEY (`profile_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_uwuser` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `view`
--
ALTER TABLE `view`
  ADD CONSTRAINT `fk_pview` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
