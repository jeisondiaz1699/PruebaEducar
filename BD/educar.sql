-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-05-2021 a las 20:55:18
-- Versión del servidor: 10.4.14-MariaDB
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `educar`
--
CREATE DATABASE IF NOT EXISTS `educar` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `educar`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transacciones`
--

CREATE TABLE `transacciones` (
  `ID_TRANSACCION` int(11) NOT NULL,
  `TIPO_DE_TRANSACCION` varchar(45) NOT NULL,
  `FECHA_TRANSACCION` date NOT NULL,
  `CANTIDAD` varchar(45) DEFAULT NULL,
  `USUARIO_ID_USUARIO` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `transacciones`
--

INSERT INTO `transacciones` (`ID_TRANSACCION`, `TIPO_DE_TRANSACCION`, `FECHA_TRANSACCION`, `CANTIDAD`, `USUARIO_ID_USUARIO`) VALUES
(1, 'Aumento de saldo 50000', '2021-05-25', '50000', 1),
(2, 'Aumento de saldo 200', '2021-05-25', '200', 1),
(3, 'Aumento de saldo 20', '2021-05-25', '20', 2),
(4, 'Aumento de saldo ', '2021-05-25', '', 1),
(5, 'Envio de saldo a usuario id 1', '2021-05-25', '20', 2),
(6, 'recivido de dinero del usuario 2', '2021-05-25', '20', 1),
(7, 'Envio de saldo a usuario id 2', '2021-05-25', '20', 1),
(8, 'recivido de dinero del usuario 1', '2021-05-25', '20', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `ID_USUARIO` int(11) NOT NULL,
  `NOMBRE` varchar(45) NOT NULL,
  `APELLIDO` varchar(45) NOT NULL,
  `SALDO` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`ID_USUARIO`, `NOMBRE`, `APELLIDO`, `SALDO`) VALUES
(1, 'usuario 1', 'usuario 1', '550200'),
(2, 'usuario 2', 'usuario 2', '2000020'),
(3, 'usuario 2 3', 'apellido 2 3', '30000');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  ADD PRIMARY KEY (`ID_TRANSACCION`),
  ADD KEY `fk_TRANSACCIONES_USUARIO1_idx` (`USUARIO_ID_USUARIO`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`ID_USUARIO`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  MODIFY `ID_TRANSACCION` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `ID_USUARIO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
