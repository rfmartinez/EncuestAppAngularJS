-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-05-2014 a las 02:54:22
-- Versión del servidor: 5.6.14
-- Versión de PHP: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `encuesta`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opcion`
--

CREATE TABLE IF NOT EXISTS `opcion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `opcion` varchar(45) NOT NULL,
  `respuesta` varchar(5) DEFAULT '0',
  `pregunta_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_opcion_pregunta1_idx` (`pregunta_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=72 ;

--
-- Volcado de datos para la tabla `opcion`
--

INSERT INTO `opcion` (`id`, `opcion`, `respuesta`, `pregunta_id`) VALUES
(52, '44,2Â°', '1', 91),
(53, '15,7Â°', '0', 91),
(54, '11,2Â°', '0', 91),
(55, '17,3Â°', '0', 91),
(56, '165,1 kilÃ³metros', '1', 92),
(57, '165,3 kilÃ³metros', '0', 92),
(58, '152,4 kilÃ³metros', '0', 92),
(59, '60', '0', 93),
(60, '61', '0', 93),
(61, '51', '1', 93),
(62, '$   21.900', '0', 94),
(63, '$   29.000', '0', 94),
(64, '$ 116.800', '0', 94),
(65, '$   14.500', '0', 94),
(66, '$   65.700', '1', 94),
(67, '$ 10.200', '0', 95),
(68, '$ 11.200', '1', 95),
(69, '$ 12.200', '0', 95),
(70, '$ 15.200', '0', 95),
(71, '$ 13.200', '0', 95);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pregunta`
--

CREATE TABLE IF NOT EXISTS `pregunta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pregunta` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=96 ;

--
-- Volcado de datos para la tabla `pregunta`
--

INSERT INTO `pregunta` (`id`, `pregunta`) VALUES
(91, 'En ViÃ±a del Mar, en un dÃ­a de verano, la temperatura mÃ¡xima fue de 28,5Â° y la temperatura mÃ­nima de 11,2Â°. Â¿CuÃ¡l fue la diferencia de temperatura ese dÃ­a?'),
(92, 'Un taxi debe hacer un viaje de 528 kilÃ³metros en tres etapas. En la primera etapa recorre 210,5 kilÃ³metros y en la tercera etapa recorre 165,1 kilÃ³metros. Â¿CuÃ¡ntos kilÃ³metros debe recorrer en la segunda etapa?'),
(93, 'El resultado final de 9:3+12*4  es:'),
(94, 'Cuatro libros de igual valor, cuestan $ 87.600, Â¿CuÃ¡nto costarÃ¡n tres de esos mismos libros?'),
(95, 'Pedro recorre en su auto 18 kilÃ³metros por cada litro de bencina. Si el valor del litro de bencina es $ 630, Â¿cuÃ¡nto gasta Pedro en  bencina en un viaje de 320 kilÃ³metros?');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta`
--

CREATE TABLE IF NOT EXISTS `respuesta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `opcion_id` int(11) NOT NULL,
  `pregunta_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_respuesta_usuario_idx` (`usuario_id`),
  KEY `fk_respuesta_opcion1_idx` (`opcion_id`),
  KEY `fk_respuesta_pregunta1_idx` (`pregunta_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resultado`
--

CREATE TABLE IF NOT EXISTS `resultado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tiempo` float NOT NULL,
  `promedio` float NOT NULL,
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_resultado_usuario1_idx` (`usuario_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(45) NOT NULL,
  `codigo` bigint(20) NOT NULL,
  `permiso` int(11) DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `user`, `codigo`, `permiso`) VALUES
(1, 'rfmartinez', 1025400009, 1);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `opcion`
--
ALTER TABLE `opcion`
  ADD CONSTRAINT `fk_opcion_pregunta1` FOREIGN KEY (`pregunta_id`) REFERENCES `pregunta` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `respuesta`
--
ALTER TABLE `respuesta`
  ADD CONSTRAINT `fk_respuesta_opcion1` FOREIGN KEY (`opcion_id`) REFERENCES `opcion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_respuesta_pregunta1` FOREIGN KEY (`pregunta_id`) REFERENCES `pregunta` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_respuesta_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `resultado`
--
ALTER TABLE `resultado`
  ADD CONSTRAINT `fk_resultado_usuario1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
