-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3308
-- Tiempo de generación: 09-06-2024 a las 21:49:29
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `lacomanda1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `idpedido` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `idmesa` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `estado` varchar(250) COLLATE utf8_unicode_ci DEFAULT 'En preparacion',
  `nombrecliente` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `nombreimagen` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tiempoestimado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



--
-- Volcado de datos para la tabla `pedido`
--
INSERT INTO `pedido` (`idmesa`, `idproducto`,`nombrecliente`) VALUES
(1, 2, 'ErnestoPerez'),
(2, 3, 'JuanPaez');


-- Índices para tablas volcadas
--

--


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
