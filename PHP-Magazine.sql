-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 20-11-2022 a las 17:10:48
-- Versión del servidor: 5.7.40-0ubuntu0.18.04.1
-- Versión de PHP: 7.2.24-0ubuntu0.18.04.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `admin_magazine`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `block`
--

CREATE TABLE `block` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `profile_id` int(10) UNSIGNED NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `breaking`
--

CREATE TABLE `breaking` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `expiration_at` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0'
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
  `keywords` text COLLATE utf8_unicode_ci,
  `footer` set('f_one','f_two','more') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'more',
  `order` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` set('disabled','enabled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'disabled',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0'
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
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `collaborator`
--

CREATE TABLE `collaborator` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `aorder` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0'
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
  `pinned` tinyint(1) NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entry`
--

CREATE TABLE `entry` (
  `id` int(11) UNSIGNED NOT NULL,
  `post_id` int(11) UNSIGNED NOT NULL,
  `type` set('text','image','carousel','video','embed','soundcloud','facebookpost','instagrampost','tweet','tiktok','spotify') COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8_unicode_ci,
  `frame` text CHARACTER SET utf8mb4,
  `esource` text COLLATE utf8_unicode_ci,
  `eorder` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `follower`
--

CREATE TABLE `follower` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `profile_id` int(10) UNSIGNED NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `label`
--

CREATE TABLE `label` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `language`
--

CREATE TABLE `language` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL DEFAULT 'en',
  `dir` set('rtl','ltr') NOT NULL,
  `status` set('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `language`
--

INSERT INTO `language` (`id`, `name`, `lang`, `dir`, `status`, `created_at`) VALUES
(1, 'English', 'en', 'ltr', 'enabled', 1611596407),
(2, 'Español', 'es', 'ltr', 'enabled', 1611596407),
(3, 'عرب', 'ar', 'rtl', 'enabled', 1611596407);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messaan`
--

CREATE TABLE `messaan` (
  `id` int(10) UNSIGNED NOT NULL,
  `message_id` int(10) UNSIGNED NOT NULL,
  `answered_id` int(10) UNSIGNED NOT NULL,
  `type` set('text','file','image') NOT NULL DEFAULT 'text',
  `created_at` int(11) NOT NULL DEFAULT '0'
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
  `size` int(11) NOT NULL DEFAULT '0',
  `deleted_fuser` int(11) NOT NULL DEFAULT '0',
  `deleted_fprofile` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0'
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
  `text` text CHARACTER SET utf8mb4,
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_fuser` int(11) NOT NULL DEFAULT '0',
  `deleted_fprofile` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `newscate`
--

CREATE TABLE `newscate` (
  `id` int(10) UNSIGNED NOT NULL,
  `newsletter_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(11) UNSIGNED NOT NULL,
  `slug` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `frequency` set('all','now','daily','weekly') NOT NULL DEFAULT 'all',
  `popular` set('off','on') NOT NULL DEFAULT 'off',
  `reason` text,
  `status` set('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0'
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
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0'
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
  `text` text,
  `status` set('disabled','enabled') NOT NULL DEFAULT 'disabled',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `page`
--

INSERT INTO `page` (`id`, `slug`, `description`, `keywords`, `text`, `status`, `updated_at`, `created_at`) VALUES
(1, 'terms_of_use', 'Conozca los Términos de uso de Las2rodillas. Conozca los lineamientos para el tratamiento de la información y el manejo de sus datos personales.', 'terminos y condiciones, aviso de privacidad, terminos de uso, las2rodillas, las dos rodillas', '<p>Estos t&eacute;rminos y condiciones de uso del portal&nbsp;<a href=\"https://las2rodillas.co/\">las2rodillas.com</a> (en adelante, el &quot;Portal&quot;) regulan el uso que Usted puede dar como Visitante al contenido publicado en el Portal, as&iacute; como la conducta que Usted puede desarrollar durante su visita y uso del Portal. Por el solo hecho de visitar el Portal, Usted acepta estos t&eacute;rminos y condiciones y consiente en someterse a los mismos. Usted asume la responsabilidad por el uso que haga del Portal. Es su responsabilidad revisar y cumplir estos T&eacute;rminos y Condiciones de Uso peri&oacute;dicamente.</p>\n\n<ol>\n    <li>\n    <h3><strong>Condiciones de acceso</strong></h3>\n\n    <p>El acceso al Portal por parte de los visitantes es libre y gratuito para personas mayores de edad. En caso de ser Usted menor de edad, debe obtener con anterioridad el consentimiento de sus padres, tutores o representantes legales, quienes ser&aacute;n responsables de los actos que Usted lleve a cabo en contravenci&oacute;n a estos t&eacute;rminos y condiciones de uso del Portal. Se da por entendido que los menores de edad que accedan y usen el Portal cuentan con este consentimiento. El acceso al Portal permite acceder a toda la informaci&oacute;n publicada por las2rodillas.com (en adelante, &quot;LAS2RODILLAS&quot;), por los Ciberperiodistas y a algunos blogs destacados (en adelante, el &quot;Contenido&quot;). El acceso a las funcionalidades de Comunidades requiere un registro previo, en las condiciones que se describen en las Condiciones de Acceso y Uso de las Comunidades.</p>\n    </li>\n    <li>\n    <h3><strong>Condiciones de Uso del Contenido P&uacute;blico</strong></h3>\n\n    <p>El Contenido (que incluye o puede incluir textos, informaci&oacute;n, im&aacute;genes, fotograf&iacute;as, dibujos, logos, dise&ntilde;os, video, multimedia, software, aplicaciones, m&uacute;sica, sonidos, entre otros, as&iacute; como su selecci&oacute;n y disposici&oacute;n), es propiedad exclusiva de LAS2RODILLAS, sus anunciantes, o de terceros que hayan otorgado una licencia a LAS2RODILLAS, con todos los derechos reservados. Como tal, dicho Contenido se encuentra protegido por las leyes y tratados internacionales vigentes en materia de Propiedad Intelectual. LAS2RODILLAS confiere a Usted una licencia para visualizar el Contenido en el Portal, y para realizar una copia cach&eacute; en su computador con dicho fin &uacute;nicamente. Este documento puede ser impreso y almacenado por Usted.&nbsp;<br />\n    <br />\n    Aparte de lo anterior, LAS2RODILLAS no confiere a los Visitantes ninguna licencia para descargar, reproducir, copiar, enmarcar, compilar, cargar o republicar en ning&uacute;n sitio de Internet, Intranet o Extranet, adaptar, modificar, transmitir, vender ni comunicar al p&uacute;blico, total o parcialmente, el Contenido. Cualquiera de estas actividades requiere de la autorizaci&oacute;n previa, expresa y por escrito de LAS2RODILLAS, so pena de incurrir en violaci&oacute;n a los derechos de propiedad industrial e intelectual, y someterse a las consecuencias civiles y penales de tal hecho, as&iacute; como al derecho de LAS2RODILLAS de revocar la licencia aqu&iacute; conferida.&nbsp;<br />\n    <br />\n    Salvo que se indique expresamente lo contrario en el presente Contrato, nada de lo dispuesto en los presentes T&eacute;rminos y Condiciones de Uso del Portal deber&aacute; interpretarse en el sentido de otorgar una licencia sobre derechos de propiedad intelectual, ya sea por impedimento legal, impl&iacute;citamente o de cualquier otra forma. Esta licencia podr&aacute; ser revocada en cualquier momento y sin preaviso, con o sin causa.&nbsp;<br />\n    <br />\n    Usted se compromete a hacer un uso adecuado del Contenido. De manera enunciativa pero no limitativa, Usted se compromete a no:</p>\n\n    <ul>\n        <li>\n        <p>Utilizar el Contenido para incurrir y/o incitar a terceros a incurrir en actividades il&iacute;citas, ilegales o contrarias a la buena fe y al orden p&uacute;blico, o para difundir contenidos o propaganda de car&aacute;cter racista, xen&oacute;fobo, pornogr&aacute;fico-ilegal, de apolog&iacute;a del terrorismo o atentatorio contra los derechos humanos;</p>\n        </li>\n        <li>\n        <p>Usar secuencias de comandos automatizadas para recopilar informaci&oacute;n publicada en el Portal o a trav&eacute;s del Portal o para interactuar de cualquier otro modo con los mismos;</p>\n        </li>\n        <li>\n        <p>provocar da&ntilde;os en los sistemas f&iacute;sicos y l&oacute;gicos de LAS2RODILLAS, de sus proveedores o de terceras personas, introducir o difundir en la red virus inform&aacute;ticos, troyanos, c&oacute;digo malicioso o cualesquiera otros sistemas f&iacute;sicos o l&oacute;gicos que sean susceptibles de provocar da&ntilde;os en y/o est&eacute;n dise&ntilde;ados para interrumpir, destruir o limitar la funcionalidad de cualquier software, hardware o equipo de telecomunicaciones o para da&ntilde;ar, deshabilitar, sobrecargar o perjudicar el Portal de cualquier modo; y</p>\n        </li>\n        <li>\n        <p>intentar acceder, recolectar o almacenar los datos personales de otros Visitantes y/o Usuarios del Portal y, en su caso, utilizar las cuentas de correo electr&oacute;nico de otros Visitantes y/o Usuarios y modificar o manipular sus mensajes.</p>\n        </li>\n    </ul>\n    </li>\n    <li>\n    <h3><strong>Cookies</strong></h3>\n\n    <p>Este portal hace uso de cookies propias y de terceros. Tenga en cuenta que el uso de la cookies va a permitir optimizar su experiencia en este portal.</p>\n\n    <p><strong>&iquest;Qu&eacute; son las cookies?</strong></p>\n\n    <p>Una cookie es un fichero que se descarga en el ordenador/smartphone/tablet del usuario al acceder a determinadas p&aacute;ginas web.</p>\n\n    <p><strong>Finalidades de las cookies</strong></p>\n\n    <p>LAS2RODILLAS har&aacute;&nbsp;uso de las cookies para:</p>\n\n    <p>- Determinar sus preferencias de navegaci&oacute;n&nbsp;<br />\n    - Para efectos promocionales, comerciales y publicitarios&nbsp;<br />\n    - Para efectos estad&iacute;sticos, entre otros fines.</p>\n\n    <p><strong>Aceptaci&oacute;n de uso de Cookies</strong></p>\n\n    <p>Al aceptar estos &ldquo;T&eacute;rminos y condiciones&rdquo;, Usted acepta que LAS2RODILLAS utilice&nbsp;cookies para los fines aqu&iacute; se&ntilde;alados.</p>\n\n    <p>El uso continuo de esta p&aacute;gina web se entender&aacute; como aceptaci&oacute;n de los &ldquo;T&eacute;rminos y Condiciones&rdquo; y como consecuencia, del uso de las cookies.</p>\n\n    <p><strong>Configuraci&oacute;n de Cookies</strong></p>\n\n    <p>Usted podr&aacute; configurar su navegador para que notifique y rechace la instalaci&oacute;n de las cookies enviadas por el Portal, sin que ello impida su acceso a los Contenidos. Sin embargo, tenga en cuenta que al desactivar el uso de cookies usted podr&aacute; experimentar una disminuci&oacute;n en la calidad de funcionamiento de la p&aacute;gina web.</p>\n    </li>\n    <li>\n    <h3><strong>Procedimiento de notificaci&oacute;n de Contenido Violatorio</strong></h3>\n\n    <p>LAS2RODILLAS respeta y promueve la protecci&oacute;n de los derechos de propiedad intelectual de terceros. No obstante, en ocasiones LAS2RODILLAS publicar&aacute; de manera inadvertida y sin mala fe de su parte, contenido cuyos derechos pertenezcan a terceros. Para ello, LAS2RODILLAS ha establecido el siguiente procedimiento de notificaci&oacute;n de contenido violatorio de derechos de terceros.<br />\n    <br />\n    En caso que Usted encuentre en el Portal contenido que considere violatorio de sus derechos, le solicitamos enviar una comunicaci&oacute;n escrita a nuestro Agente Designado, utilizando el&nbsp;<a href=\"https://las2rodillas.com/contactar\">formato</a>&nbsp;establecido para ello , o por fax, email o correo escrito a nuestro Agente Designado, as&iacute;:</p>\n\n    <ul>\n        <li>Dependencia: Servicio al cliente</li>\n        <li>\n        <p>Email: las2rodillas.co@gmail.com</p>\n        </li>\n    </ul>\n\n    <p>Por favor, incluya en la comunicaci&oacute;n la siguiente informaci&oacute;n:</p>\n\n    <ul>\n        <li>\n        <p>La p&aacute;gina o URL en la que aparece el contenido considerado violatorio.</p>\n        </li>\n        <li>\n        <p>Una descripci&oacute;n clara y detallada del contenido considerado violatorio. En caso que en la p&aacute;gina o URL existan varias obras, esta descripci&oacute;n debe ser suficiente para identificar cu&aacute;l de todas es la obra que est&aacute; violando sus derechos.</p>\n        </li>\n        <li>\n        <p>Una explicaci&oacute;n de en qu&eacute; manera el contenido en menci&oacute;n atenta contra sus derechos. En caso de contar con documentos que demuestren la titularidad de sus derechos, le rogamos anexar una copia.</p>\n        </li>\n        <li>\n        <p>Una declaraci&oacute;n bajo la gravedad del juramento, de que la informaci&oacute;n enviada en su comunicaci&oacute;n es correcta.</p>\n        </li>\n        <li>\n        <p>Sus datos de contacto, tales como nombre, identificaci&oacute;n, direcci&oacute;n de correspondencia escrita y electr&oacute;nica, tel&eacute;fono, celular, etc.</p>\n        </li>\n    </ul>\n\n    <p>Por favor tenga en cuenta que en caso que la informaci&oacute;n enviada en su comunicaci&oacute;n sea incorrecta, LAS2RODILLAS no asume responsabilidad por las consecuencias de su retiro. En consecuencia, Usted debe ser consciente de que al enviar su comunicaci&oacute;n asume los da&ntilde;os y perjuicios que pueda ocasionar a terceros de buena fe.&nbsp;<br />\n    <br />\n    LAS2RODILLAS revisar&aacute; el caso, y si encuentra m&eacute;rito en su queja, proceder&aacute; a retirar el material. En caso que la informaci&oacute;n haya sido publicada por un ciberperiodista o est&eacute; contenida en un blog destacado, o en cualquier otro mecanismo que permita a los Usuarios o Visitantes publicar informaci&oacute;n en el Portal como Contenido de acceso al p&uacute;blico, LAS2RODILLAS transmitir&aacute; la queja al Ciberperiodista, Visitante o Usuario que haya publicado el contenido considerado violatorio, quien tendr&aacute; un plazo de quince (15) d&iacute;as corrientes para responder a la queja. Para ello, el Ciberperiodista, Visitante o Usuario deber&aacute; enviar una comunicaci&oacute;n a LAS2RODILLAS con la siguiente informaci&oacute;n:</p>\n\n    <ul>\n        <li>\n        <p>Una explicaci&oacute;n de por qu&eacute; raz&oacute;n el contenido no era violatorio de los derechos alegados. En caso de contar con documentos que lo demuestren, deber&aacute; anexar una copia.</p>\n        </li>\n        <li>\n        <p>Una declaraci&oacute;n bajo la gravedad del juramento, de que la informaci&oacute;n enviada en su comunicaci&oacute;n es correcta.</p>\n        </li>\n        <li>\n        <p>Sus datos de contacto, tales como nombre, identificaci&oacute;n, direcci&oacute;n de correspondencia escrita y electr&oacute;nica, tel&eacute;fono, celular, etc.</p>\n        </li>\n    </ul>\n\n    <p>Tras la respuesta del Ciberperiodista, Visitante o Usuario, LAS2RODILLAS analizar&aacute; el caso y decidir&aacute; si mantiene el bloqueo del contenido, o si lo publica de nuevo.&nbsp;</p>\n    </li>\n    <li>\n    <h3><strong>Limitaci&oacute;n de Responsabilidad</strong></h3>\n\n    <p>LAS2RODILLAS no es responsable por:</p>\n\n    <ul>\n        <li>\n        <p>Las ca&iacute;das del Portal y la falla en el suministro del servicio, quedando exonerada por cualquier tipo de da&ntilde;os y perjuicios causados debido a la no disponibilidad y/o interrupci&oacute;n del servicio ocasionados por fallas o no disponibilidad de las redes y servicios de telecomunicaciones utilizados para soportar el Portal, y que sean ajenos a su voluntad.</p>\n        </li>\n        <li>\n        <p>Los da&ntilde;os y perjuicios causados por virus inform&aacute;ticos, troyanos, c&oacute;digo malicioso o cualesquiera otros sistemas f&iacute;sicos o l&oacute;gicos a los sistemas de los Usuarios.</p>\n        </li>\n        <li>\n        <p>Errores mecanogr&aacute;ficos y/o tipogr&aacute;ficos que aparezcan en el Contenido.</p>\n        </li>\n        <li>\n        <p>El contenido publicitario que aparezca en el Portal, el cual es responsabilidad del anunciante respectivo. Cualquier reclamaci&oacute;n por infracci&oacute;n a la propiedad industrial y de derechos de autor deber&aacute; ser dirigida directamente al Anunciante. Cualquier promoci&oacute;n, incluida la entrega y el pago por bienes y servicios, y cualquier otro t&eacute;rmino, condici&oacute;n, garant&iacute;a o representaci&oacute;n asociados con dichos tratos o promociones, corresponden a una relaci&oacute;n exclusiva entre el anunciante y el Usuario, sin participaci&oacute;n de LAS2RODILLAS.</p>\n        </li>\n        <li>\n        <p>Las opiniones publicadas por los Usuarios a trav&eacute;s de los blogs y otros servicios que ofrezca LAS2RODILLAS al p&uacute;blico.</p>\n        </li>\n        <li>\n        <p>El contenido de los sitios vinculados mediante hiperv&iacute;nculos que aparezcan en el Portal (los &quot;Sitios Vinculados&quot;), incluyendo sin limitaci&oacute;n, cualquier v&iacute;nculo contenido en los Sitios Vinculados, cualquier cambio o actualizaci&oacute;n a los Sitios Vinculados, cualquier tipo de transmisi&oacute;n recibida o enviada desde o hacia Sitios Vinculados, o el funcionamiento incorrecto de los Sitios Vinculados. LAS2RODILLAS proporciona estos Sitios Vinculados s&oacute;lo por comodidad, y la inclusi&oacute;n de cualquiera de ellos no implica aprobaci&oacute;n por parte de LAS2RODILLAS a ninguno de estos sitios ni ninguna asociaci&oacute;n con sus operadores.</p>\n        </li>\n    </ul>\n    </li>\n    <li>\n    <h3><strong>Miscel&aacute;nea</strong></h3>\n\n    <ul>\n        <li>\n        <p>Modificaci&oacute;n de los T&eacute;rminos Legales&nbsp;<br />\n        LAS2RODILLAS podr&aacute; modificar estos T&eacute;rminos y Condiciones de Uso del Portal en cualquier momento y sin previo aviso, tan pronto se publique una nueva versi&oacute;n en el Portal. LAS2RODILLAS publicar&aacute; en todo caso la fecha en que la versi&oacute;n vigente de los T&eacute;rminos y Condiciones fue publicada, para fines informativos de los Usuarios.</p>\n        </li>\n        <li>\n        <p>Legislaci&oacute;n aplicable.&nbsp;<br />\n        Los presentes T&eacute;rminos Legales se regir&aacute;n e interpretar&aacute;n de acuerdo con las leyes de la Rep&uacute;blica de Colombia.</p>\n        </li>\n    </ul>\n    </li>\n</ol>\n', 'enabled', 1665139297, 1664116167);
INSERT INTO `page` (`id`, `slug`, `description`, `keywords`, `text`, `status`, `updated_at`, `created_at`) VALUES
(2, 'habeas_data', 'Conozca las políticas de privacidad de Las2rodillas. Conozca los lineamientos para el tratamiento de la información y el manejo de sus datos personales.', 'habias data, politica de privacidad, aviso de privacidad', '<h2>Manual de&nbsp;pol&iacute;ticas y procedimientos&nbsp;para la proteci&oacute;n&nbsp;de datos&nbsp;personales de las2rodillas.com (en adelante, Las2rodillas)</h2>\n\n<p>El presente manual tiene por objeto el cumplimiento de las disposiciones legales, constitucionales y jurisprudenciales concernientes al desarrollo del derecho constitucional que tienen todas las personas a conocer, actualizar y rectificar la informaci&oacute;n que se haya recogido sobre ellas en bases de datos o archivos relativos al art&iacute;culo 15 de la Constituci&oacute;n Pol&iacute;tica, as&iacute; como el derecho a la informaci&oacute;n consagrado en el art&iacute;culo 20 de la misma.</p>\n\n<p>En resumen, el presente manual establece las pol&iacute;ticas y los procedimientos a trav&eacute;s de los cuales el titular de los datos personales puede hacer efectivos sus derechos relacionados con el tratamiento de sus datos y a su vez, el tratamiento que el responsable debe darle a los datos de terceros, as&iacute; como los mecanismos para instar el cumplimiento de los deberes en cabeza del responsable del tratamiento. As&iacute; mismo, se dan algunas definiciones relativas a t&eacute;rminos necesarios para la correcta aplicaci&oacute;n de las mencionadas pol&iacute;ticas, junto con los principios sobre los que se fundamenta la recolecci&oacute;n y tratamiento de los datos personales</p>\n\n<p>Es de aclarar este manual no aplicar&aacute; ni servir&aacute; de referencia para solicitudes de eliminaci&oacute;n de bases de datos de archivos de informaci&oacute;n period&iacute;stica y otros contenidos editoriales, lo anterior, en virtud del art&iacute;culo segundo (2&deg;) de la Ley 1581 de 2012 que dispuso lo siguiente:</p>\n\n<p><em>&ldquo;El r&eacute;gimen de protecci&oacute;n de datos personales que se establece en la presente ley no ser&aacute; de aplicaci&oacute;n (&hellip;) d) A las bases de datos y archivos de informaci&oacute;n period&iacute;stica y otros contenidos editoriales (&hellip;)&rdquo;.</em></p>\n\n<p>Como puede apreciarse, &eacute;sta exclusi&oacute;n da un tratamiento especial a la informaci&oacute;n period&iacute;stica y de contenidos editoriales de los medios de comunicaci&oacute;n en relaci&oacute;n con sus datos noticiosos. As&iacute; mismo la jurisprudencia interpret&oacute; los alcances de los contenidos sujetos al Habeas Data, la Corte Constitucional en sentencia C-748 de 2011 de control de constitucionalidad del proyecto de ley estatutaria 1581 de 2012 se pronunci&oacute; al punto de la excepci&oacute;n mencionada en los siguientes t&eacute;rminos:</p>\n\n<p>(&hellip;)</p>\n\n<p><strong><em>&ldquo;Constitucionalidad del literal d): la excepci&oacute;n de &ldquo;datos y archivos de informaci&oacute;n period&iacute;stica y otros contenidos editoriales&rdquo;</em></strong></p>\n\n<p><em>&ldquo;Esta restricci&oacute;n es necesaria en la medida en que a trav&eacute;s de ella se est&aacute; asegurando el respeto a la libertad de prensa. La jurisprudencia ha sido enf&aacute;tica en se&ntilde;alar que el &ldquo;&aacute;mbito de protecci&oacute;n de la libertad de expresi&oacute;n en sentido gen&eacute;rico consagrada en el art&iacute;culo 20 Superior, es la libertad de prensa, que se refiere no solo a los medios impresos sino a todos los medios masivos de comunicaci&oacute;n.&rdquo;.</em></p>\n\n<hr />\n<h3><strong>OBJETO</strong></h3>\n\n<p>Reglamentar las pol&iacute;ticas y procedimientos que ser&aacute;n aplicables en el manejo de informaci&oacute;n de datos personales por parte de Las2rodillas, seg&uacute;n las disposiciones contenidas en la Ley 1581 de 2012 y el decreto 1377 de 2013.</p>\n\n<h3><strong>RESPONSABLES DEL TRATAMIENTO</strong></h3>\n\n<p>Tel&eacute;fonos: 6435324<br />\nP&aacute;gina web principal:&nbsp;<a href=\"https://las2rodillas.com/\" target=\"_blank\">las2rodillas.com</a><br />\nEmail:&nbsp;<a href=\"mailto:las2rodillas.co@gmail.com\">las2rodillas.co@gmail.com</a></p>\n\n<h3><strong>ALCANCE</strong></h3>\n\n<p>El presente manual le es aplicable a los datos personales de personas naturales registrados en las bases de datos relativas a Empleados, Potenciales Empleados, Trabajadores Retirados, Accionistas, Proveedores, Potenciales Proveedores, Clientes y Usuarios (en lo pertinente) de Las2rodillas, los cuales sean susceptibles de tratamiento. Aplicar&aacute; a los datos personales que sean objeto de recolecci&oacute;n y manejo por parte de Las2rodillas. Si a futuro, otras personas jur&iacute;dicas entran a formar parte de Las2rodillas, el manual aplicar&aacute; a aquellas.</p>\n\n<p>El presente manual no aplicar&aacute; a:</p>\n\n<ol>\n    <li>A los datos de uso exclusivamente personal o dom&eacute;stico.</li>\n    <li>A los datos que tengan por finalidad la seguridad y defensa nacional, as&iacute; como la prevenci&oacute;n, detecci&oacute;n, monitoreo y control del lavado de activos y el financiamiento del terrorismo.</li>\n    <li>A los datos que contengan informaci&oacute;n de inteligencia y contrainteligencia del Estado.</li>\n    <li>A los datos de informaci&oacute;n period&iacute;stica y otros contenidos editoriales.</li>\n    <li>A las bases de datos y archivos regulados por la Ley Estatutaria 1266 de 2008.</li>\n    <li>A las bases de datos y archivos regulados por la Ley 79 de 1993.</li>\n</ol>\n\n<h3><strong>DEFINICIONES</strong></h3>\n\n<p>Para la aplicaci&oacute;n de las reglas y procedimientos establecidos en el presente manual, y de acuerdo a lo establecido en el art&iacute;culo 3 de la Ley Estatutaria 1581 de 2012, se entender&aacute; por:</p>\n\n<ol>\n    <li><strong>Autorizaci&oacute;n:</strong>&nbsp;Consentimiento previo, expreso e informado del Titular para llevar a cabo el Tratamiento de datos personales.</li>\n    <li><strong>Base de Datos:</strong>&nbsp;Conjunto organizado de datos personales que sea objeto de Tratamiento.</li>\n    <li><strong>Aviso de privacidad:</strong>&nbsp;Documento f&iacute;sico, electr&oacute;nico o en cualquier otro formato, generado por el responsable del Tratamiento que se pone a disposici&oacute;n del Titular para el Tratamiento de sus datos personales. A trav&eacute;s de este, se comunica al Titular de la informaci&oacute;n la existencia de las pol&iacute;ticas aplicables para el tratamiento de sus datos personales, junto con la forma como acceder a las mismas y las caracter&iacute;sticas del tratamiento de los datos personales.</li>\n    <li><strong>Dato personal:</strong>&nbsp;Cualquier informaci&oacute;n vinculada o que pueda asociarse a una o varias personas naturales determinadas o determinables, tales como nombre y apellidos, documento de identidad, edad, domicilio, regi&oacute;n, pa&iacute;s, ciudad, c&oacute;digo postal, n&uacute;mero de tel&eacute;fono fijo, n&uacute;mero de tel&eacute;fono m&oacute;vil, direcci&oacute;n, direcci&oacute;n de correo electr&oacute;nico, preferencias publicitarias, preferencia de consumo, preferencias de canales, quejas y reclamos, novedades de servicio, datos b&aacute;sicos y personales, datos de contacto, datos demogr&aacute;ficos, datos de gustos, preferencias y h&aacute;bitos.</li>\n    <li><strong>Datos sensibles:</strong>&nbsp;Se entiende por datos sensibles aquellos que afectan la intimidad de Titular o cuyo uso indebido puede generar su discriminaci&oacute;n, tales como aquellos revelen el origen racial o &eacute;tnico, la orientaci&oacute;n pol&iacute;tica, las convicciones religiosas o filos&oacute;ficas, la pertenencia a sindicatos, organizaciones sociales, de derechos humanos o que promuevan intereses de cualquier partido pol&iacute;tico o que garanticen los derechos y garant&iacute;as de partidos pol&iacute;ticos de oposici&oacute;n, as&iacute; como los datos relativos a la salud, a la vida sexual y los datos biom&eacute;tricos.</li>\n    <li><strong>Encargado del Tratamiento:</strong>&nbsp;Persona natural o jur&iacute;dica, p&uacute;blica o privada, que por s&iacute; misma o en asocio con otros, realice el Tratamiento de datos personales por cuenta del Responsable del Tratamiento.</li>\n    <li><strong>Responsable del Tratamiento:</strong>&nbsp;Persona natural o jur&iacute;dica, p&uacute;blica o privada, que por s&iacute; misma o en asocio con otros, decida sobre la base de datos y/o el Tratamiento de los datos.</li>\n    <li><strong>Titular:</strong>&nbsp;Persona natural cuyos datos personales sean objeto de Tratamiento.</li>\n    <li><strong>Tratamiento:</strong>&nbsp;Cualquier operaci&oacute;n o conjunto de operaciones sobre datos personales, tales como la recolecci&oacute;n, almacenamiento, uso, circulaci&oacute;n o supresi&oacute;n de datos, en cualquier tecnolog&iacute;a conocida o por conocer.</li>\n</ol>\n\n<h3><strong>PRINCIPIOS</strong></h3>\n\n<p>Los principios que a continuaci&oacute;n se enuncian, constituyen los par&aacute;metros generales mediante los cuales se dar&aacute; aplicaci&oacute;n a lo establecido en el presente manual referente a los datos personales de las personas a las que le es aplicable el tratamiento de sus datos:</p>\n\n<ol>\n    <li><strong>Principio de finalidad:</strong>&nbsp;El Tratamiento de datos personales por parte de Las2rodillas debe obedecer a una finalidad leg&iacute;tima, la cual debe ser informada al Titular.</li>\n    <li><strong>Principio de libertad:</strong>&nbsp;El Tratamiento de datos personales s&oacute;lo podr&aacute; ejercerse mediando con el consentimiento, previo, expreso e informado del Titular de la informaci&oacute;n. Los datos personales no podr&aacute;n ser obtenidos o divulgados sin previa autorizaci&oacute;n, o en ausencia de mandato legal o judicial que releve el consentimiento.</li>\n    <li><strong>Principio de veracidad o calidad:</strong>&nbsp;La informaci&oacute;n sujeta a Tratamiento debe ser veraz, completa, exacta, actualizada, comprobable y comprensible. Se proh&iacute;be el Tratamiento de datos parciales, incompletos, fraccionados o que induzcan a error.</li>\n    <li><strong>Principio de transparencia:</strong>&nbsp;En el Tratamiento debe garantizarse el derecho del Titular a obtener de Las2rodillas, en cualquier momento y sin restricciones, informaci&oacute;n acerca de la existencia de datos que le conciernan.</li>\n    <li><strong>Principio de acceso y circulaci&oacute;n restringida:</strong>&nbsp;Los datos personales, salvo la informaci&oacute;n p&uacute;blica, no podr&aacute;n estar disponibles en Internet u otros medios de divulgaci&oacute;n o comunicaci&oacute;n masiva, salvo que el acceso sea t&eacute;cnicamente controlable para brindar un conocimiento restringido s&oacute;lo a los Titulares o terceros autorizados.</li>\n    <li><strong>Principio de seguridad:</strong>&nbsp;La informaci&oacute;n sujeta a Tratamiento por Las2rodillas, se deber&aacute; manejar con las medidas t&eacute;cnicas, humanas y administrativas que sean necesarias para otorgar seguridad a los registros evitando su adulteraci&oacute;n, p&eacute;rdida, consulta, uso o acceso no autorizado o fraudulento.</li>\n    <li><strong>Principio de confidencialidad:</strong>&nbsp;Todas las personas que intervengan en el Tratamiento de datos personales que no tengan la naturaleza de p&uacute;blicos est&aacute;n obligadas a garantizar la reserva de la informaci&oacute;n, inclusive despu&eacute;s de finalizada su relaci&oacute;n con alguna de las labores que comprende el Tratamiento.</li>\n</ol>\n\n<h3><strong>CONTENIDO</strong></h3>\n\n<p><strong>Tratamiento a que ser&aacute;n sometidos los datos y finalidad del tratamiento.</strong></p>\n\n<p>El tratamiento es cualquier operaci&oacute;n o conjunto de operaciones sobre datos personales, tales como la recolecci&oacute;n, almacenamiento, uso, circulaci&oacute;n o supresi&oacute;n. La informaci&oacute;n que recolecta Las2rodillas en la prestaci&oacute;n de sus servicios y en general en el desarrollo de su objeto social, es utilizada principalmente para identificar, mantener un registro y control de los Empleados, Potenciales Empleados, Trabajadores Retirados, Accionistas, Proveedores, Potenciales Proveedores, Clientes y Usuarios de Las2rodillas.</p>\n\n<p><strong>Tratamientos Generales de la informaci&oacute;n:</strong></p>\n\n<ul>\n    <li>Procesar.</li>\n    <li>Confirmar.</li>\n    <li>Cumplir.</li>\n    <li>Proveer los servicios y/o productos adquiridos directamente o con la participaci&oacute;n de terceros.</li>\n    <li>Promocionar y publicitar nuestras actividades, productos y servicios.</li>\n    <li>Realizar transacciones.</li>\n    <li>Efectuar reportes con las distintas autoridades administrativas de control y vigilancia nacional, policiva o autoridades judiciales, entidades financieras y/o compa&ntilde;&iacute;as de seguros.</li>\n    <li>Fines administrativos internos y/o comerciales tales como: investigaci&oacute;n de mercados, auditorias, reportes contables, an&aacute;lisis estad&iacute;sticos o facturaci&oacute;n.</li>\n    <li>Recolecci&oacute;n.</li>\n    <li>Almacenamiento.</li>\n    <li>Grabaci&oacute;n.</li>\n    <li>Uso.</li>\n    <li>Circulaci&oacute;n.</li>\n    <li>Procesamiento.</li>\n    <li>Supresi&oacute;n.</li>\n    <li>Transmisi&oacute;n y/o transferencia a terceros pa&iacute;ses de los datos suministrados, para la ejecuci&oacute;n de las actividades relacionadas con los servicios y productos adquiridos.</li>\n    <li>Registros contables.</li>\n    <li>Correspondencia.</li>\n    <li>Identificaci&oacute;n de fraudes y prevenci&oacute;n de lavado de activos y de otras actividades delictivas.</li>\n</ul>\n\n<p><strong>Tratamiento General de Informaci&oacute;n de los accionistas:</strong></p>\n\n<ul>\n    <li>Efectuar el pago de dividendos.</li>\n    <li>Cumplimiento de decisiones judiciales y disposiciones administrativas y legales.</li>\n    <li>Contactos.</li>\n    <li>Cumplimiento de decisiones judiciales y disposiciones administrativas y legales, fiscales y regulatorias.</li>\n</ul>\n\n<p><strong>Tratamiento General de Informaci&oacute;n de los proveedores:</strong></p>\n\n<ul>\n    <li>Para fines comerciales.</li>\n    <li>Contabilizaci&oacute;n.</li>\n    <li>Cumplimiento de decisiones judiciales y disposiciones administrativas y legales, fiscales y regulatorias.</li>\n    <li>Cumplimiento de obligaciones contractuales, por lo cual la informaci&oacute;n podr&aacute; ser transferida a terceros, tales como entidades financieras, notar&iacute;as, listas OFAC y de terrorismo, abogados, etc.</li>\n    <li>Para realizar los procesos en que se encuentran vinculados los proveedores.</li>\n    <li>Cualquier otro uso que el proveedor autorice por escrito para el uso de su informaci&oacute;n.</li>\n    <li>Transmisi&oacute;n de informaci&oacute;n y datos personales en procesos de auditor&iacute;as.</li>\n</ul>\n\n<p><strong>Tratamiento General de Informaci&oacute;n de los clientes:</strong></p>\n\n<ul>\n    <li>Para fines comerciales.</li>\n    <li>Ofrecimiento de bienes y servicios.</li>\n    <li>Publicidad y mercadeo.</li>\n    <li>Alianzas comerciales.</li>\n    <li>Contabilizaci&oacute;n.</li>\n    <li>Cumplimiento de obligaciones contractuales, por lo cual la informaci&oacute;n podr&aacute; ser transferida a terceros, tales como entidades financieras, notar&iacute;as, listas OFAC y de terrorismo, abogados, etc.</li>\n    <li>Cumplimiento de decisiones judiciales y disposiciones administrativas y legales, fiscales y regulatorias.</li>\n    <li>Transmisi&oacute;n de informaci&oacute;n y datos personales en procesos de auditor&iacute;as.</li>\n    <li>Facturaci&oacute;n.</li>\n</ul>\n\n<p><strong>Tratamiento General de Informaci&oacute;n de los empleados, trabajadores retirados, pensionados y candidatos a ocupar vacantes:</strong></p>\n\n<ul>\n    <li>Para fines pertinentes a la relaci&oacute;n laboral (EPS, ARL, fondos de pensiones y cesant&iacute;as, cajas de compensaci&oacute;n familiar, etc.)</li>\n    <li>En el caso de los empleados con la suscripci&oacute;n del contrato laboral se entiende autorizaci&oacute;n expresa para darle Tratamiento a la informaci&oacute;n.</li>\n    <li>En el caso de requerimientos judiciales y legales.</li>\n    <li>Contabilizaci&oacute;n y pago de n&oacute;mina.</li>\n    <li>Reclutar y seleccionar personal que ocupar&aacute;n las vacantes.</li>\n    <li>Procesar, confirmar y cumplir con las obligaciones laborales legales y extralegales derivadas del contrato laboral.</li>\n    <li>Realizar transacciones.</li>\n    <li>Pago de beneficios extralegales.</li>\n    <li>Auditorias.</li>\n    <li>An&aacute;lisis estad&iacute;sticos.</li>\n    <li>Mantener base de datos de candidatos.</li>\n    <li>Capacitaci&oacute;n y formaci&oacute;n.</li>\n    <li>Compartir los datos personales con entidades bancarias, empresas que ofrezcan beneficios a nuestros trabajadores activos, entre otros.</li>\n</ul>\n\n<p><strong>Autorizaci&oacute;n.</strong></p>\n\n<p>La compilaci&oacute;n, almacenamiento, consulta, uso, intercambio, transmisi&oacute;n, transferencia y tratamiento de datos personales requiere el consentimiento libre, expreso e informado del Titular de la informaci&oacute;n. Basado en lo anterior y a trav&eacute;s de este manual, se implementa los mecanismos que permitan la consulta posterior por parte del titular de la informaci&oacute;n.</p>\n\n<p><strong>Mecanismos para otorgar Autorizaci&oacute;n</strong></p>\n\n<p>La autorizaci&oacute;n por parte del titular podr&aacute; constar en un documento f&iacute;sico, electr&oacute;nico o cualquier otro formato que permita concluir de forma razonable que el Titular otorg&oacute; la autorizaci&oacute;n.</p>\n\n<p>Teniendo en cuenta lo anterior, Las2rodillas deja de presente que la autorizaci&oacute;n en todo caso ser&aacute; mediante documento f&iacute;sico y/o digital, el cual deber&aacute; contar con la firma del Titular de la informaci&oacute;n, lo cual no obsta que m&aacute;s adelante se establezcan mecanismos diferentes para otorgar la autorizaci&oacute;n.</p>\n\n<p>A trav&eacute;s de la autorizaci&oacute;n se pondr&aacute; en conocimiento del Titular de la informaci&oacute;n o de su representante en el caso de infantes (ni&ntilde;os y ni&ntilde;as) y adolescentes, el hecho que la informaci&oacute;n ser&aacute; recolectada, incluyendo la finalidad, las modificaciones, almacenamiento y el uso especifico que se dar&aacute; a los mismos, y adem&aacute;s:</p>\n\n<ol>\n    <li>La persona quien recopila la informaci&oacute;n (especificando si es el Responsable o el Encargado del tratamiento).</li>\n    <li>Los datos que ser&aacute;n recopilados, incluyendo si se recopilan Datos Sensibles.</li>\n    <li>La finalidad del tratamiento de los datos.</li>\n    <li>Los mecanismos a trav&eacute;s de los cuales pueden ejercer sus derechos como Titulares de la informaci&oacute;n (acceso, correcci&oacute;n, actualizaci&oacute;n o supresi&oacute;n de los datos).</li>\n</ol>\n\n<p>&nbsp;</p>\n\n<p><strong>Prueba de la Autorizaci&oacute;n.</strong></p>\n\n<p>Las2rodillas en su calidad de Responsable y de Encargado del Tratamiento dispondr&aacute; de los medios necesarios para mantener los registros t&eacute;cnicos y tecnol&oacute;gicos de cu&aacute;ndo y c&oacute;mo se obtuvo la autorizaci&oacute;n por parte del Titular de la informaci&oacute;n para el tratamiento de los mismos.</p>\n\n<p><strong>Aviso de privacidad.</strong></p>\n\n<p>El aviso de privacidad es un documento f&iacute;sico, electr&oacute;nico o cualquier otro formato, mediante el cual se informa al titular de la informaci&oacute;n sobre la existencia de pol&iacute;ticas que le ser&aacute;n aplicables, as&iacute; como la forma en la que pueden acceder a las mismas y las caracter&iacute;sticas del tratamiento que se le dar&aacute; a los datos personales.</p>\n\n<p><strong>Contenido del aviso de privacidad.</strong></p>\n\n<ol>\n    <li>La identidad, domicilio y datos de contacto del Responsable o del Encargado del Tratamiento.</li>\n    <li>El Tratamiento al que ser&aacute;n sometidos los datos y la finalidad del mismo.</li>\n    <li>Los mecanismos dispuestos Las2rodillas para que el Titular conozca la pol&iacute;tica de tratamiento de la informaci&oacute;n y los cambios sustanciales que se produzcan en ella o en el aviso de privacidad correspondiente. En todos los casos, debe informar al Titular c&oacute;mo acceder o consultar la pol&iacute;tica de tratamiento de informaci&oacute;n.</li>\n</ol>\n\n<p>Se conservar&aacute; el modelo del aviso de privacidad que se transmiti&oacute; a los Titulares de la informaci&oacute;n mientras se lleve a cabo el tratamiento de los datos personales y perduren las obligaciones que de &eacute;ste se deriven. Para el almacenamiento del modelo, se podr&aacute;n emplear medios inform&aacute;ticos, electr&oacute;nicos o cualquier otra tecnolog&iacute;a a elecci&oacute;n de Las2rodillas.</p>\n\n<p>Seg&uacute;n el grupo de personas cuyos datos personales se recaban, habr&aacute; un &uacute;nico modelo de aviso de privacidad, en el cual se especificar&aacute; detalladamente los puntos anteriormente descritos para cada uno de los mismos.</p>\n\n<p><strong>Derechos de los Titulares de la informaci&oacute;n.</strong></p>\n\n<p>De conformidad con el art&iacute;culo 8 de la Ley Estatutaria 1581 de 2012, el Titular de los datos personales tiene los siguientes derechos:</p>\n\n<ol>\n    <li>Conocer, actualizar y rectificar sus datos personales Las2rodillas en su calidad de Responsable y Encargado del tratamiento.</li>\n    <li>Solicitar prueba de la autorizaci&oacute;n otorgada a Las2rodillas.</li>\n    <li>Ser informado por Las2rodillas&nbsp;respecto del uso que le ha dado a sus datos personales.</li>\n    <li>Presentar ante la Superintendencia de Industria y Comercio quejas por infracciones a lo dispuesto en la Ley Estatutaria 1581 de 2012, habi&eacute;ndose agotado el tr&aacute;mite de consulta o reclamo seg&uacute;n lo indicado en la mencionada Ley.</li>\n    <li>Revocar la autorizaci&oacute;n y/o solicitar la supresi&oacute;n del dato cuando en el Tratamiento no se respeten los principios, derechos y garant&iacute;as constitucionales y legales.</li>\n    <li>Acceder en forma gratuita a sus datos personales que hayan sido objeto de Tratamiento.</li>\n</ol>\n\n<p><strong>Deberes de Las2rodillas con relaci&oacute;n al tratamiento de datos personales en su calidad de Responsables y Encargado del Tratamiento.</strong></p>\n\n<p>Se deja de presente que los datos personales objeto del tratamiento son de propiedad de las personas a las que se refieren y ellas son las facultadas para disponer los mismos. Basado en lo anterior, solo har&aacute; uso de los datos personales conforme a las finalidades establecidas en la Ley y respetando lo establecido en la Ley Estatutaria 1581 de 2012.</p>\n\n<p>De conformidad con el art&iacute;culo 17 de la Ley Estatutaria 1581 de 2012, se comprometen a cumplir los siguientes deberes:</p>\n\n<ol>\n    <li>Garantizar al Titular, en todo tiempo, el pleno y efectivo ejercicio del derecho de h&aacute;beas data.</li>\n    <li>Solicitar y conservar copia de la respectiva autorizaci&oacute;n otorgada por el Titular.</li>\n    <li>Realizar en los t&eacute;rminos previstos en los art&iacute;culos 14 y 15 de la Ley Estatutaria 1581 de 2012, la actualizaci&oacute;n, rectificaci&oacute;n o supresi&oacute;n de los datos.</li>\n    <li>Tramitar las consultas y reclamos formulados por los titulares en los t&eacute;rminos se&ntilde;alados en el art&iacute;culo 14 de la Ley Estatutaria 1581 de 2012.</li>\n    <li>Conservar la informaci&oacute;n bajo las condiciones de seguridad necesarias para impedir su adulteraci&oacute;n, p&eacute;rdida, consulta, uso o acceso no autorizado o fraudulento.</li>\n    <li>Insertar en las bases de datos la leyenda &ldquo;informaci&oacute;n en discusi&oacute;n judicial&rdquo; una vez notificada por parte de la autoridad competente sobre procesos judiciales relacionados con la calidad o detalles del dato personal.</li>\n    <li>Informar a la Superintendencia de Industria y Comercio cuando se presenten violaciones a los c&oacute;digos de seguridad y existan riesgos en la administraci&oacute;n de la informaci&oacute;n de los titulares.</li>\n    <li>Tramitar las consultas y reclamos formulados por los titulares de la informaci&oacute;n.</li>\n    <li>Cumplir las instrucciones y requerimientos que imparta la Superintendencia de Industria y Comercio.</li>\n    <li>Aplicar las normas que reglamenten la Ley Estatutaria 1581 de 2012.</li>\n</ol>\n\n<p><strong>Deberes respecto del Tratamiento de datos de Infantes y Adolescentes.</strong></p>\n\n<p>Las2rodillas&nbsp;en su calidad de Responsable y Encargado de Tratamiento de los datos personales de los mencionados grupos deber&aacute;n tener especial cuidado en asegurar el cumplimiento de la Ley respecto a estos grupos y el respeto de los derechos de los mismos, en especial respecto a datos personales que no encuadren en la categor&iacute;a de datos de naturaleza p&uacute;blica (nombre, sexo, fecha de nacimiento, etc.).</p>\n\n<p><strong>Procedimientos para acceso, consulta y reclamaci&oacute;n.</strong></p>\n\n<p>Puntos aplicables para todos los Procedimientos:</p>\n\n<p><strong>(i)</strong>&nbsp;Para el ejercicio de los derechos indicados en este punto por parte de los causahabientes, y tambi&eacute;n para evitar acceso a la informaci&oacute;n por personas no autorizadas legalmente, se deber&aacute; verificar previamente y de acuerdo con la Ley, la documentaci&oacute;n que permita concluir que la persona que solicita la informaci&oacute;n s&iacute; es un causahabiente del Titular.</p>\n\n<p><strong>(ii)</strong>&nbsp;En caso de existir alguna duda en cuanto a la aplicaci&oacute;n de los procedimientos ac&aacute; indicados, la misma ser&aacute; informada por el &aacute;rea responsable de la base de datos que es objeto de la aplicaci&oacute;n del procedimiento y resuelta por la Direcci&oacute;n Jur&iacute;dica, quien resolver&aacute; el tema teniendo en cuenta la Ley, los Decretos y dem&aacute;s normas reglamentarias o instructivas, y las jurisprudencias que en la materia se emitan.</p>\n\n<p><strong>Acceso.</strong></p>\n\n<p>Teniendo en cuenta que la facultad de disponer o de decidir sobre los datos personales est&aacute; en cabeza del Titular de la informaci&oacute;n, esta facultad implica necesariamente el derecho del titular a acceder y conocer la informaci&oacute;n personal que est&aacute; siendo objeto de tratamiento, incluyendo en este aspecto el alcance, condiciones y generalidades del tratamiento.</p>\n\n<p>Teniendo en cuenta lo anterior, se garantiza este derecho en cabeza del Titular, el cual incluye.</p>\n\n<ol>\n    <li>El conocimiento de la existencia del tratamiento de sus datos personales.</li>\n    <li>El acceso a sus datos personales.</li>\n    <li>Las circunstancias del tratamiento de los datos personales.</li>\n</ol>\n\n<p><strong>Consulta.</strong></p>\n\n<p>De conformidad con el art&iacute;culo 14 de la Ley Estatutaria 1581 de 2012, los Titulares o sus causahabientes podr&aacute;n consultar la informaci&oacute;n personal del Titular que repose en cualquier base de datos. Basado en esto, se garantiza este derecho suministrando a estos toda la informaci&oacute;n contenida en el registro individual o que est&eacute; vinculada con la identificaci&oacute;n del Titular.</p>\n\n<p>Seg&uacute;n la naturaleza de la base de datos personales, la consulta ser&aacute; gestionada por el &aacute;rea responsable de la atenci&oacute;n a la misma al interior de Las2rodillas.</p>\n\n<p>Las consultas ser&aacute;n atendidas en un t&eacute;rmino m&aacute;ximo de diez (10) d&iacute;as h&aacute;biles contados a partir de la fecha de recibo de la misma. Cuando no fuere posible atender la consulta dentro de dicho t&eacute;rmino, se informar&aacute; al interesado dentro del primer t&eacute;rmino conferido, en donde se expresar&aacute; los motivos de la demora y se&ntilde;alando la fecha en que se atender&aacute; su consulta, la cual en ning&uacute;n caso podr&aacute; superar los cinco (5) d&iacute;as h&aacute;biles siguientes al vencimiento del primer t&eacute;rmino.</p>\n\n<p><strong>Reclamo.</strong></p>\n\n<p>De conformidad con el art&iacute;culo 15 de la Ley Estatutaria 1581 de 2012, el Titular o sus causahabientes que consideren que la informaci&oacute;n contenida en una base de datos debe ser objeto de correcci&oacute;n, actualizaci&oacute;n o supresi&oacute;n, o cuando adviertan el presunto incumplimiento de cualquiera de los deberes contenidos en la Ley Estatutaria 1581 de 2012, podr&aacute;n presentar un reclamo el cual ser&aacute; tramitado bajo las siguientes reglas:</p>\n\n<ol>\n    <li>El reclamo se formular&aacute; mediante comunicaci&oacute;n realizada por el titular o sus causahabientes dirigida a Las2rodillas responsable o el encargado del Tratamiento, la cual debe incluir la informaci&oacute;n se&ntilde;alada en el art&iacute;culo 15 de la Ley Estatutaria 1581 de 201Si el reclamo resulta incompleto, se requerir&aacute; al interesado dentro de los cinco (5) d&iacute;as siguientes a la recepci&oacute;n del reclamo para que subsane las fallas. Transcurridos dos (2) meses desde la fecha del requerimiento, sin que el solicitante presente la informaci&oacute;n requerida, se entender&aacute; que ha desistido del reclamo. En todo caso si la comunicaci&oacute;n es dirigida a Las2rodillas y no tiene la calidad para responder la comunicaci&oacute;n, Las2rodillas, sin necesidad de comunicarlo a la persona que realiza la reclamaci&oacute;n, dar&aacute; conocimiento de la misma a la sociedad que deba dar respuesta.</li>\n    <li>En caso de que Las2rodillas reciba un reclamo que no sea competente para resolver, dar&aacute; traslado a quien corresponda en un t&eacute;rmino m&aacute;ximo de dos (2) d&iacute;as h&aacute;biles e informar&aacute; de la situaci&oacute;n al interesado.</li>\n    <li>Una vez recibido el reclamo completo, se incluir&aacute; en la base de datos una leyenda que diga &quot;reclamo en tr&aacute;mite&quot; y el motivo del mismo, en un t&eacute;rmino no mayor a dos (2) d&iacute;as h&aacute;biles. Dicha leyenda deber&aacute; mantenerse hasta que el reclamo sea decidido.</li>\n    <li>El t&eacute;rmino m&aacute;ximo para atender el reclamo ser&aacute; de quince (15) d&iacute;as h&aacute;biles contados a partir del d&iacute;a siguiente a la fecha de su recibo. Cuando no fuere posible atender el reclamo dentro de dicho t&eacute;rmino, se informar&aacute; al interesado los motivos de la demora y la fecha en que se atender&aacute; su reclamo, la cual en ning&uacute;n caso podr&aacute; superar los ocho (8) d&iacute;as h&aacute;biles siguientes al vencimiento del primer t&eacute;rmino.</li>\n    <li>En cualquier tiempo y gratuitamente, la persona natural Titular de los datos personales o su representante podr&aacute; solicitar la rectificaci&oacute;n, actualizaci&oacute;n o supresi&oacute;n de sus datos personales previa acreditaci&oacute;n de su identidad.</li>\n</ol>\n\n<p>La solicitud de rectificaci&oacute;n, actualizaci&oacute;n o supresi&oacute;n de sus datos personales debe ser presentada a trav&eacute;s de los medios proporcionados se&ntilde;alados en el aviso de privacidad y deber&aacute; contener como m&iacute;nimo la siguiente informaci&oacute;n:</p>\n\n<ol>\n    <li>El nombre y domicilio del Titular o representante o cualquier otro medio para recibir la respuesta a su solicitud.</li>\n    <li>Los documentos que acrediten la identidad o la representaci&oacute;n del Titular de los datos personales.</li>\n    <li>La descripci&oacute;n clara y precisa de los datos personales y de los hechos que dan lugar al reclamo.</li>\n    <li>Los documentos que se desean hacer valer en la reclamaci&oacute;n.</li>\n</ol>\n\n<p>La supresi&oacute;n implica la eliminaci&oacute;n total o parcial de la informaci&oacute;n personal de acuerdo por lo solicitado por el Titular, de los registros, archives y bases de datos o tratamientos realizados por Las2rodillas.</p>\n\n<p>Seg&uacute;n la naturaleza de la Base de datos personales, la reclamaci&oacute;n ser&aacute; gestionada por el &aacute;rea responsable de la atenci&oacute;n a la misma al interior de Las2rodillas.</p>\n\n<p><strong>Requisito de procedibilidad.</strong></p>\n\n<p>El Titular o causahabiente s&oacute;lo podr&aacute; elevar queja ante la Superintendencia de Industria y Comercio una vez haya agotado el tr&aacute;mite de consulta o reclamo ante Las2rodillas.</p>\n\n<p><strong>Revocatoria de la autorizaci&oacute;n.</strong></p>\n\n<p>De acuerdo con lo establecido con la Ley, en el supuesto en que en el Tratamiento no se respeten los principios, derechos y garant&iacute;as constitucionales y legales, los Titulares o sus representantes (como es el caso de padres que ejerzan la patria potestad de un infante o adolescente) podr&aacute;n solicitar la revocatoria de la autorizaci&oacute;n otorgada para el Tratamiento de los mismos, salvo que por disposici&oacute;n legal o contractual se impida dicha revocatoria, indicando en dicho caso, las razones concretas con base en las cuales considera que se est&aacute; dando la situaci&oacute;n de no respecto a los mencionados alcances.</p>\n\n<p>Las2rodillas al ser responsable o el encargado del Tratamiento, seg&uacute;n el caso, deber&aacute; confirmar haber recibido la solicitud de revocatoria de autorizaci&oacute;n, incluyendo su fecha de recepci&oacute;n. Se podr&aacute; objetar la misma si a juicio de Las2rodillas no se presentan el supuesto indicado por el Titular o si tal revocatoria implica una afectaci&oacute;n para el seguimiento o cumplimiento de derechos o de obligaciones por parte de la entidad y respecto del Titular, caso en el cual deber&aacute; informarlo al mismo por escrito para que &eacute;ste tome las medidas ante las autoridades que considere pertinentes.</p>\n\n<p>La solicitud de revocatoria de la autorizaci&oacute;n puede ser total o parcial. Ser&aacute; total cuando se solicite la revocatoria de la totalidad de las finalidades consentidas a trav&eacute;s de la autorizaci&oacute;n; ser&aacute; parcial cuando se solicite la revocatoria de algunas finalidades dependiendo de la solicitud de revocatoria. Este calificativo deber&aacute; ser expresado de manera clara en la solicitud de revocatoria de la autorizaci&oacute;n.</p>\n\n<h3><strong>Seguridad de la informaci&oacute;n</strong></h3>\n\n<p><strong>Medidas de seguridad de la informaci&oacute;n.</strong></p>\n\n<p>En desarrollo del principio de seguridad establecido en la Ley Estatutaria 1581 de 2012, Las2rodillas implementara las medidas t&eacute;cnicas, humanas y administrativas adicionales en caso de requerirse, que sean necesarias para otorgar seguridad a los registros, mediante los cuales se evitar&aacute; su adulteraci&oacute;n, p&eacute;rdida, consulta, uso o acceso no autorizado o fraudulento.</p>\n\n<p><strong>Registro de las Bases.</strong></p>\n\n<p>Las2rodillas en su calidad de Responsable y Encargado de Tratamiento deber&aacute;n proceder al registro de las bases en los t&eacute;rminos indicados por las normas colombianas.</p>\n\n<p><strong>Aceptaci&oacute;n.</strong></p>\n\n<p>Los Titulares de la informaci&oacute;n aceptan el tratamiento de sus datos personales conforme los t&eacute;rminos de este Manual, al momento de proporcionar sus datos.</p>\n\n<p><strong>Vigencia:</strong></p>\n\n<p>Esta Pol&iacute;tica General de Privacidad es efectiva desde la fecha de su publicaci&oacute;n</p>', 'enabled', 0, 1664116167),
(4, 'about_us', 'Debido a las preguntas constantes en nuestras redes sociales, vamos a responder en esta sección nuestros ideales y todo aquello que queremos llegar a representar.', 'about-us', '<p><em>Nuestros ideales no concuerdan con el comunismo, socialismo o cualquier otra denominaci&oacute;n que le asignen a esto&nbsp;que crearon&nbsp;para destruir pueblos y robar a manos llenas. Eso s&iacute;, debemos aclarar que en el capitalismo, la corrupci&oacute;n y el robo de los recursos p&uacute;blicos, siempre existir&aacute;; no es cuesti&oacute;n de ideales, es cuestion de etica.</em></p>\n\n<p>Desde que Colombia dej&oacute; atr&aacute;s el yugo espa&ntilde;ol, los medios de comunicaci&oacute;n han hecho gala de su amistad con el poder en el pa&iacute;s para confluir en ideas e intereses. Es por eso que grandes representantes de la pol&iacute;tica nacional, han salido del periodismo y su influencia en las decisiones que nos afectan a los ciudadanos, ha llegado al punto de colocar presidentes o guiar el legislativo.</p>\n\n<p>Hasta hace unos a&ntilde;os el periodismo y los medios cumpl&iacute;an una funci&oacute;n que los colombianos no consideraban como informativa, ya que se sent&iacute;an enga&ntilde;ados y la informaci&oacute;n que recib&iacute;an ten&iacute;a detr&aacute;s alg&uacute;n fin pol&iacute;tico, seg&uacute;n manifestaban diariamente. La llegada de Internet cambi&oacute; todo lo que hoy conocemos como comunicaci&oacute;n; nuevas herramientas, nuevos medios, nuevas maneras de enviarle mensajes a la gente y sobre todo, una nueva forma de confrontar todo lo que nos dicen los gobernantes con diferentes fuentes en tiempo real.</p>\n\n<p>El internet nos ha brindado la facilidad de &ldquo;no comer entero&rdquo; lo que los medios tradicionales nos obligaban a consumir, por esa raz&oacute;n muchos emprendimientos digitales buscando abrirle los ojos al pueblo y entregar datos veraces con mirada cr&iacute;tica, surgieron desde sus primeros a&ntilde;os de incursi&oacute;n.</p>\n\n<p>En ese sentido, naci&oacute; la Revista <em>Las2rodillas</em> en junio del 2017, a&ntilde;o en que comenzaban las campa&ntilde;as pol&iacute;ticas para que alguno de los posibles candidatos llegaran a la Presidencia de la Rep&uacute;blica. Nosotros vimos la necesidad de mostrarle a los colombianos el otro&nbsp;lado de la noticia, algo que contrastara con lo que los grandes conglomerados informativos enviaban diariamente; busc&aacute;bamos traer a colaci&oacute;n y poner sobre la mesa,&nbsp;los temas que nadie tocaba porque los politicos o&nbsp;padrinos del poder, no les pemitian publicar.</p>\n\n<p><em>Las2rodillas</em>&nbsp;desde sus inicios entr&oacute; rompiendo los esquemas y por eso hoy en d&iacute;a somos uno de los medios independientes m&aacute;s le&iacute;dos en Colombia, como lo corroboran los millones de visitantes que mensualmente leen nuestros cr&iacute;ticas, argumentos e informaci&oacute;n precisa sobre el acontecer de nuestro pa&iacute;s.</p>\n\n<p>Somos personas&nbsp;como t&uacute;, personas que decidieron crear algo diferente, algo que no se pudiese corromper, algo puro, y aunque&nbsp;legalmente no somos periodistas; algunos nos graduamos como ingenieros, otros como contadores y&nbsp;abogados, pero tratamos de entregar todo como si perteneci&eacute;ramos al honorable grupo de comunicadores colombianos que ofrecen su vida por llevarle la verdad al pueblo. Cada d&iacute;a aprendemos sobre este oficio.</p>\n\n<p>No nos hemos aliado con ning&uacute;n poder pol&iacute;tico y&nbsp;jam&aacute;s lo haremos. Nuestra independencia es nuestra arma, por eso podemos hablar sobre todos los temas sin pensar en el &ldquo;jal&oacute;n de orejas&rdquo; com&uacute;n en los medios tradicionales.</p>\n\n<p>Tratamos de expresar diariamente lo que en la calle se murmura; nuestras dudas e inconformismos con el poder y la pol&iacute;tica. El objetivo es mirar todos los puntos y contrastar cada una de la informaci&oacute;n que nos encontramos en el espectro nacional y gubernamental.</p>', 'enabled', 0, 1664116167),
(5, 'contact', 'Si tienes alguna pregunta, inquietud o necesitas hacer alguna propuesta, puedes contactarnos y responderemos lo mas pronto posible.', 'contactar, las2rodillas, las dos rodillas, como contactar', '', 'enabled', 0, 1664116167),
(6, 'sitemap', 'Un índice de todas las historias publicadas por Las2rodillas.', 'sitemap, mapa de las2rodillas, las2rodillas, las dos rodillas', '', 'enabled', 0, 1664116167);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post`
--

CREATE TABLE `post` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `thumbnail` varchar(128) CHARACTER SET latin1 DEFAULT 'default-holder',
  `views` int(11) NOT NULL DEFAULT '0',
  `likes` int(11) NOT NULL DEFAULT '0',
  `dislikes` int(11) NOT NULL DEFAULT '0',
  `post_sources` text COLLATE utf8_unicode_ci,
  `thumb_sources` text COLLATE utf8_unicode_ci,
  `type` set('normal','video') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
  `status` set('approved','rejected','pending','deleted') CHARACTER SET utf8 NOT NULL DEFAULT 'pending',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `published_at` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0'
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
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recobo`
--

CREATE TABLE `recobo` (
  `id` int(10) UNSIGNED NOT NULL,
  `recommended_id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `rorder` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0'
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
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `report`
--

CREATE TABLE `report` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `reported_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `type` set('r_spam','r_none','rp_writing','rp_thumbnail','rp_copyright','rc_offensive','rc_abusive','rc_disagree','rc_marketing','ru_hate','ru_picture','ru_copyright') NOT NULL,
  `place` set('user','post','comment','reply') NOT NULL,
  `description` text,
  `status` set('unanswered','answered','archived','removed') NOT NULL DEFAULT 'unanswered',
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `saved`
--

CREATE TABLE `saved` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `post_id` int(11) UNSIGNED NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `session`
--

CREATE TABLE `session` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `token` varchar(150) NOT NULL,
  `details` text,
  `created_at` int(11) NOT NULL DEFAULT '0'
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
('censored_words', 'prueba, oscar'),
('contact_email', 'hi@soyvillareal.com'),
('dark_palette', '{\"background\":{\"white\":\"#181818\",\"blue\":\"#265070\",\"grely\":\"#303030\",\"black\":\"#e4e6eb\"},\"color\":{\"blackly\":\"#b0b3b8\",\"black\":\"#e4e6eb\",\"white\":\"#181818\",\"grey\":\"#aaa\",\"blue\":\"#265070\"},\"border\":{\"blue\":\"#265070\",\"focus-blue\":\"#265070\",\"grely\":\"#606060\",\"grey\":\"#aaa\",\"black\":\"#a5a6ab\"},\"hover\":{\"blue\":{\"type\":\"color\",\"value\":\"#265070\"},\"background\":{\"type\":\"background-color\",\"value\":\"#222222\"}}}'),
('description', 'The best digital magazine for newspapers or bloggers'),
('dir_pages', 'ltr'),
('dismiss_cookie', 'on'),
('email', 'support@phpmagazine.soyvillareal.com'),
('facebook_page', 'https://facebook.com/elegirco'),
('fb_app_id', '981453882030339'),
('fb_comments', 'on'),
('fb_secret_id', '9e5a2efec3d3f1aefef5c387bbf6e2dd'),
('file_size_limit', '26214400'),
('google_analytics', 'UA-127104007-1'),
('go_app_id', '344500601804-dch7u6ekcfasts7rjkak0b83jin34qce.apps.googleusercontent.com'),
('go_secret_id', 'AIzaSyBXxQsoVpbKX7IbrYIfstZmlI0YFa-I7tE'),
('hidden_domains', 'metatube.com, facebook.com'),
('instagram_page', 'https://instagram.com/elegirco'),
('keyword', 'PHP Magazine, Magazine, PHP Script, Nyt clone, Open Source'),
('language', 'es'),
('last_sitemap', '0'),
('light_palette', '{\"background\":{\"white\":\"#fff\",\"blue\":\"#326891\",\"grely\":\"#e9e9e9\",\"redly\":\"#dd6e68\",\"red\":\"#cb423b\",\"black\":\"#000\",\"blackly\":\"rgba(0,0,0,.5)\",\"green\":\"#61a125\"},\"color\":{\"blackly\":\"#333\",\"black\":\"#000\",\"white\":\"#fff\",\"grey\":\"#909090\",\"blue\":\"#326891\",\"red\":\"#cb0e0b\",\"green\":\"#61a125\",\"orange\":\"#f29f18\"},\"border\":{\"blue\":\"#326891\",\"focus-blue\":\"#326891\",\"grely\":\"#cdcdcd\",\"grey\":\"#909090\",\"black\":\"#000\",\"red\":\"#cb0e0b\"},\"hover\":{\"blue\":{\"type\":\"color\",\"value\":\"#326891\"},\"background\":{\"type\":\"background-color\",\"value\":\"#ebebeb\"}}}'),
('max_words_about', '800'),
('max_words_comments', '1000'),
('max_words_report', '500'),
('max_words_unsub_newsletter', '600'),
('nodejs', 'off'),
('node_hostname', '192.168.1.52'),
('node_server_port', '3000'),
('number_labels', '8'),
('number_of_fonts', '8'),
('recaptcha', 'on'),
('recaptcha_private_key', '6LcpOG4iAAAAAOvgi2ctony_gHbrVCJuwK02Qf_S'),
('recaptcha_public_key', '6LcpOG4iAAAAAJ3d4AQnMbECkQbItEN7oyeV80ql'),
('server_type', 'smtp'),
('show_palette', 'on'),
('smtp_encryption', 'ssl'),
('smtp_host', 'smtp.soyvillareal.com'),
('smtp_password', 'Oscar1980O$'),
('smtp_port', '465'),
('smtp_username', 'no-reply@phpmagazine.soyvillareal.com'),
('switch_mode', 'on'),
('system_comments', 'on'),
('theme', 'default'),
('theme_mode', 'light'),
('title', 'PHP Magazine'),
('token_expiration_attempts', '7'),
('token_expiration_hours', '1'),
('twitter_page', 'https://twitter.com/elergirco'),
('tw_app_id', 'VDBJTEdjRU1Qc0Y4ekxOWlVQSDI6MTpjaQ'),
('tw_secret_id', '3n98ATynegf1rtneYljpJIuIIwKCwMpaMgCJfELALXTI_ahpQN'),
('verify_email', 'on');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tag`
--

CREATE TABLE `tag` (
  `id` int(11) UNSIGNED NOT NULL,
  `post_id` int(11) UNSIGNED NOT NULL,
  `label_id` int(11) UNSIGNED NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `token`
--

CREATE TABLE `token` (
  `id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `verify_email` varchar(50) NOT NULL,
  `change_email` varchar(50) NOT NULL,
  `reset_password` varchar(50) NOT NULL,
  `unlink_email` varchar(50) NOT NULL,
  `2check` varchar(50) NOT NULL,
  `expires` text,
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `token`
--

INSERT INTO `token` (`id`, `user_id`, `verify_email`, `change_email`, `reset_password`, `unlink_email`, `2check`, `expires`, `created_at`) VALUES
(1, 1, 'a9f4c86c0ac253939e115d70d64219b7', 'd57224c48899e843dfdd2170ae54a92e', '7aab9a1437468c2dcf12ff87aac6787b', '5a54e63b9bf1ba39f8181d82dec87e533b374df5', 'a6741e6ae72646d5e512876e74aef2a1', '{\"2check\":{\"repeat\":2,\"updated_at\":1668544298},\"verify_email\":{\"updated_at\":1667867722,\"repeat\":1},\"reset_password\":{\"repeat\":2,\"updated_at\":1668882765},\"change_email\":{\"repeat\":4,\"updated_at\":1667859479}}', 1649983196);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `typing`
--

CREATE TABLE `typing` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `profile_id` int(10) UNSIGNED NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(40) CHARACTER SET utf8 NOT NULL,
  `user_changed` int(11) NOT NULL DEFAULT '0',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `new_email` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `ip` varchar(43) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `surname` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `birthday` int(11) NOT NULL DEFAULT '0',
  `birthday_changed` int(11) NOT NULL DEFAULT '0',
  `gender` set('male','female') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'male',
  `language` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en',
  `darkmode` tinyint(1) NOT NULL DEFAULT '0',
  `avatar` varchar(128) CHARACTER SET latin1 NOT NULL DEFAULT 'default-holder',
  `about` text COLLATE utf8_unicode_ci,
  `facebook` varchar(50) CHARACTER SET latin1 NOT NULL,
  `twitter` varchar(50) CHARACTER SET latin1 NOT NULL,
  `instagram` varchar(100) CHARACTER SET latin1 NOT NULL,
  `main_sonet` set('facebook','twitter','instagram') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'twitter',
  `contact_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` set('pending','active','deactivated','deleted') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `role` set('admin','moderator','publisher','viewer') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'viewer',
  `2check` set('activated','deactivated') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'deactivated',
  `type` set('normal','facebook','twitter','google') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
  `notifications` text COLLATE utf8_unicode_ci,
  `shows` text COLLATE utf8_unicode_ci,
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `username`, `user_changed`, `email`, `new_email`, `ip`, `password`, `name`, `surname`, `birthday`, `birthday_changed`, `gender`, `language`, `darkmode`, `avatar`, `about`, `facebook`, `twitter`, `instagram`, `main_sonet`, `contact_email`, `status`, `role`, `2check`, `type`, `notifications`, `shows`, `created_at`) VALUES
(1, 'soyvillareal', 0, 'hi@soyvillareal.com', NULL, '::1', '$2y$12$ZMbS5cCobmw2IrcmMr0tLuCh.8sa.ZwBzw03612rKsDAeKo2L19u6', 'Oscar', 'Garcés', 930009600, 0, 'male', 'es', 0, 'PHPMagazine-12b4c375fc1d5cf8060b9042e3cc944d51b67cbd', 'Periodista, apasionado por la historia, la geopolítica y los documentales. Hago preguntas desde que tengo uso de razón. Egresado de la Universidad Eafit.', 'elegirco', 'elergirco', 'elegirco', 'twitter', 'i1409886@gmail.com', 'active', 'admin', 'deactivated', 'normal', '[\"followers\",\"post\",\"collab\",\"react\",\"pcomment\",\"preply\",\"ucomment\",\"ureply\",\"21\"]', '{\"birthday\":\"on\",\"gender\":\"on\",\"contact_email\":\"on\",\"messages\":\"on\",\"followers\":\"on\"}', 1611596407);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `view`
--

CREATE TABLE `view` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `post_id` int(11) UNSIGNED NOT NULL,
  `fingerprint` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `widget`
--

CREATE TABLE `widget` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `status` set('enabled','disabled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'disabled',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `widget`
--

INSERT INTO `widget` (`id`, `name`, `content`, `type`, `status`, `updated_at`, `created_at`) VALUES
(7, 'post_body', '<ins class=\"adsbygoogle\" style=\"display:block\" data-ad-client=\"ca-pub-5730259165592897\" data-ad-slot=\"5293450553\" data-ad-format=\"auto\" data-full-width-responsive=\"true\"></ins>\n    <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>', 'pbody', 'enabled', 1560990818, 1559974643),
(8, 'aside', '<ins class=\"adsbygoogle\"\n     style=\"display:block; text-align:center;\"\n     data-ad-layout=\"in-article\"\n     data-ad-format=\"fluid\"\n     data-ad-client=\"ca-pub-5730259165592897\"\n     data-ad-slot=\"9414759438\"></ins>\n<script>\n     (adsbygoogle = window.adsbygoogle || []).push({});\n</script>', 'aside', 'enabled', 1561003881, 1561003881),
(9, 'post_top', '<ins class=\"adsbygoogle\" style=\"display:block\" data-ad-client=\"ca-pub-5730259165592897\" data-ad-slot=\"5293450553\" data-ad-format=\"auto\" data-full-width-responsive=\"true\"></ins>\r\n    <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>', 'ptop', 'enabled', 1560990818, 1559974643),
(10, 'home_top', '<ins class=\"adsbygoogle\" style=\"display:block\" data-ad-client=\"ca-pub-5730259165592897\" data-ad-slot=\"5293450553\" data-ad-format=\"auto\" data-full-width-responsive=\"true\"></ins>\r\n    <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>', 'htop', 'enabled', 1560990818, 1559974643),
(11, 'home_load', '<ins class=\"adsbygoogle\" style=\"display:block\" data-ad-client=\"ca-pub-5730259165592897\" data-ad-slot=\"5293450553\" data-ad-format=\"auto\" data-full-width-responsive=\"true\"></ins>\r\n    <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>', 'hload', 'enabled', 1560990818, 1559974643),
(12, 'horiz_posts', '<ins class=\"adsbygoogle\"\n     style=\"display:block; text-align:center;\"\n     data-ad-layout=\"in-article\"\n     data-ad-format=\"fluid\"\n     data-ad-client=\"ca-pub-5730259165592897\"\n     data-ad-slot=\"9414759438\"></ins>\n<script>\n     (adsbygoogle = window.adsbygoogle || []).push({});\n</script>', 'horizposts', 'enabled', 1561003881, 1561003881);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `word`
--

CREATE TABLE `word` (
  `word` varchar(160) NOT NULL,
  `en` text,
  `ar` text,
  `es` text
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
('footer_message', 'We are a different medium, independent and very dedicated. We try to cover news of general interest that are not biased for one reason or another, we are different journalists, we are part of the future.', 'نحن وسيط مختلف ومستقل ومخلص للغاية. نحاول تغطية أخبار المصلحة العامة غير المنحازة لسبب أو لآخر ، فنحن صحفيون مختلفون ، نحن جزء من المستقبل.', 'Somos un medio diferente, independiente y muy dedicado. Intentamos cubrir noticias de interés general que no estén sesgadas por una u otra razón, somos periodistas diferentes, somos parte del futuro.'),
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
('latest_news_colombia_world', 'Latest News from Colombia and the World', 'آخر الأخبار من كولومبيا والعالم', 'Últimas Noticias de Colombia y el Mundo'),
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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `follower`
--
ALTER TABLE `follower`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `label`
--
ALTER TABLE `label`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `page`
--
ALTER TABLE `page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `session`
--
ALTER TABLE `session`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `token`
--
ALTER TABLE `token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `typing`
--
ALTER TABLE `typing`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `view`
--
ALTER TABLE `view`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `widget`
--
ALTER TABLE `widget`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
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
  ADD CONSTRAINT `fk_pentry` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

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
  ADD CONSTRAINT `fk_cpost` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_upost` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE NO ACTION;

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
  ADD CONSTRAINT `fk_psaved` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_usaved` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `session`
--
ALTER TABLE `session`
  ADD CONSTRAINT `fk_usession` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tag`
--
ALTER TABLE `tag`
  ADD CONSTRAINT `fk_plabel` FOREIGN KEY (`label_id`) REFERENCES `label` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_tpost` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `fk_utoken` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

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
  ADD CONSTRAINT `fk_pview` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
