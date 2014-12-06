-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Czas wygenerowania: 06 Gru 2014, 19:42
-- Wersja serwera: 5.6.11
-- Wersja PHP: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `comments`
--
CREATE DATABASE IF NOT EXISTS `comments` DEFAULT CHARACTER SET utf8 COLLATE utf8_polish_ci;
USE `comments`;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `admin_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `login` tinytext COLLATE utf8_polish_ci NOT NULL,
  `password` varchar(118) COLLATE utf8_polish_ci NOT NULL,
  `last_visit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-----------------------------------------------------------

--
-- Proces generowania hasla uzytkownika:
--
-- $somestring = bin2hex(openssl_random_pseudo_bytes(16));
-- $salt = '$6$rounds=5000$'.$somestring;
-- $password = crypt('password', $salt);
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_polish_ci NOT NULL,
  `nick` varchar(40) COLLATE utf8_polish_ci DEFAULT NULL,
  `email` tinytext COLLATE utf8_polish_ci,
  `www` tinytext COLLATE utf8_polish_ci,
  `content` text COLLATE utf8_polish_ci NOT NULL,
  `date` datetime NOT NULL,
  `admin_id` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=13 ;

--
-- Zrzut danych tabeli `comments`
--

INSERT INTO `comments` (`id`, `name`, `nick`, `email`, `www`, `content`, `date`, `admin_id`) VALUES
(1, 'tu', 'Franek', NULL, 'http://www.franeksinatra.pl', 'Drugie wydanie Krzyżowca jest już na rynku od prawie trzech tygodniu. Przez ten relatywnie krótki czas, można było spotkać się ze skrajnie różnymi opiniami na temat tej gry. Jedni wytykają jej wiele błędów i złe zbalansowanie rozgrywki, a drudzy chwalą grafikę, nowe jednostki i łatwość obsługi oraz budowy zamków. Chyba najlepszym sposobem na zdecydowanie czy gra przypadnie Ci do gustu, jest kupienie jej i przekonanie się "na własnej skórze". Mimo wszystko, zapraszam do obejrzenia wideo-recenzji Stronghold Crusader 2 z kanału WikiGamesPL.', '2014-11-08 21:48:59', NULL),
(7, 'tu', 'Pan Tadeusz', 'pan.tadeusz@wp.pl', 'http://www.pantadeusz.pl', 'Litwo, ojczyzno moja!<br />\r\nTy jesteś jak zdrowie,<br />\r\nIle trzeba Cię cenić,<br />\r\nTen tylko się dowie,<br />\r\nkto Cię utracił.', '2014-11-11 00:03:36', NULL),
(9, 'owdzie', 'Janusz Korwin-Mikke', NULL, NULL, 'Socjaliści uważają odwrotnie: Człowiek jest idiotą i nie podejmie decyzji rozsądnej czy zapiąć się pasami w samochodzie czy nie, czy się ubezpieczyć czy tak samo nakazać, natomiast ten sam idiota jest w pełni kompetentny, żeby wybrać prezydenta, podjąć decyzję czy przystąpić do Unii Europejskiej czy nie, tu to on jest geniuszem!', '2014-11-23 15:19:45', NULL),
(10, 'owdzie', 'Ewa Zajączkowska', NULL, NULL, 'Polska zachorowała na nowotwór nieważnych głosów.<br />\r\n<br />\r\nProtestowaliśmy dziś razem z przedstawicielami Obozu Narodowo- Radykalnego w Radomiu.', '2014-11-23 15:39:49', NULL),
(11, 'tu', 'Wolni od skazy lewactwa', 'wolni.od.skazy.lewactwa@gmail.com', NULL, 'Reżimowe media stosują niezłą taktykę, by lemingi nie przyjmowały do wiadomości tego, że wybory zostały sfałszowane.<br />\r\n<br />\r\nPokażą na moment wypowiedzi Jarka, który nie zgadza się z wynikami i to już całkowicie wystarczy, by plankton intelektualny z Lemingradu wiedział co i jak.<br />\r\n<br />\r\nNo bo skoro Kaczafi jak zwykle pierdoli, to znaczy że domniemane fałszerstwa miały miejsce tylko w jego bujnej wyobraźni. I można spać spokojnie do czasu, kiedy ci jebani katofaszole nie wyjdą na ulicę.<br />\r\n<br />\r\nMetoda stara jak świat, wykorzystując niechęć i uprzedzenia do jakiejś opcji politycznej, kształtują opinię publiczną w ten sposób by ta w zero-jedynkowy sposób postrzegała otaczającą rzeczywistość.<br />\r\n<br />\r\nNapiszę takiemu jednemu, że również nie zgadzam się z wynikami podanymi przez leśne próchno, nawet zapodam linka do jakiegoś udokumentowanego fałszerstwa - wraz zostanę wyzwany od pisiorów i tych co sieją nienawiść.<br />\r\nA gdy dodam, że pierdolę równo Populizm i Socjalizm i mam swoje własne zdanie, natychmiast staję się "gimbokucem od tego popierdoleńca co zabrałby prawo głosu kobietom i niepełnosprawnym".<br />\r\n<br />\r\nI ten popierdolony, zindoktrynowany naród ma się nadawać do rewolucji?', '2014-11-23 15:44:00', NULL),
(12, 'tu', 'Żelazna Logika', 'info@zelaznalogika.net', 'http://zelaznalogika.net', 'Ogłaszamy nazwisko naszego kolejnego Gościa w Wywiadzie Żelaznej Logiki - jest nim Pan Łukasz Merta!<br />\r\n<br />\r\nNa Wasze pytania czekamy do piątku.<br />\r\n<br />\r\nEdytowano.<br />\r\n<br />\r\nWychodzi na to, że największy odsetek kretynów nie potrafiących postawić krzyżyka jest w matecznikach Platformy - Wlkp, Pomorze, Kujawy, a najbardziej kumate są Pisiory z południa i wschodu.', '2014-11-23 15:45:00', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ip_addresses`
--

CREATE TABLE IF NOT EXISTS `ip_addresses` (
  `ip_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext COLLATE utf8_polish_ci NOT NULL,
  `ip_address` varchar(15) COLLATE utf8_polish_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`ip_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
