-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-06-2020 a las 19:50:11
-- Versión del servidor: 10.4.8-MariaDB
-- Versión de PHP: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `manual_fatto`
--
CREATE DATABASE IF NOT EXISTS `manual_fatto` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `manual_fatto`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calendario`
--

DROP TABLE IF EXISTS `calendario`;
CREATE TABLE IF NOT EXISTS `calendario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `trabajador_id` int(11) DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `proveedor_id` (`proveedor_id`),
  KEY `trabajador_id` (`trabajador_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

DROP TABLE IF EXISTS `categoria`;
CREATE TABLE IF NOT EXISTS `categoria` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `nombre`) VALUES
(1, 'Productos Frescos'),
(2, 'Productos Procesados'),
(3, 'Producto para empaquetar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE IF NOT EXISTS `cliente` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `persona_contacto` varchar(255) DEFAULT NULL,
  `id_zona` int(255) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `tipo_cid` varchar(255) NOT NULL,
  `rif_cedula` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cliente_zona` (`id_zona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

DROP TABLE IF EXISTS `compra`;
CREATE TABLE IF NOT EXISTS `compra` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `id_proveedor` int(255) NOT NULL,
  `id_pago` int(255) DEFAULT NULL,
  `fecha` date NOT NULL,
  `monto` double NOT NULL,
  `credito` int(255) NOT NULL,
  `pendiente` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_compra_proveedor` (`id_proveedor`),
  KEY `fk_compra_pago` (`id_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `despacho`
--

DROP TABLE IF EXISTS `despacho`;
CREATE TABLE IF NOT EXISTS `despacho` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `id_venta` int(255) NOT NULL,
  `id_trabajador` int(255) DEFAULT NULL,
  `fecha` date NOT NULL,
  `nota` varchar(255) NOT NULL,
  `entregado` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_despacho_venta` (`id_venta`),
  KEY `fk_despacho_trabajador` (`id_trabajador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `desperdicio`
--

DROP TABLE IF EXISTS `desperdicio`;
CREATE TABLE IF NOT EXISTS `desperdicio` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `id_compra` int(255) NOT NULL,
  `id_producto` int(255) NOT NULL,
  `cantidad` double NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_desperdicio_compra` (`id_compra`),
  KEY `fk_desperdicio_producto` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egreso`
--

DROP TABLE IF EXISTS `egreso`;
CREATE TABLE IF NOT EXISTS `egreso` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `id_compra` int(255) DEFAULT NULL,
  `id_gasto_costo` int(255) DEFAULT NULL,
  `id_pago_nomina` int(255) DEFAULT NULL,
  `monto` double NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_egreso_compra` (`id_compra`),
  KEY `fk_egreso_gasto_costo` (`id_gasto_costo`),
  KEY `fk_egreso_pago_nomina` (`id_pago_nomina`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gasto_costo`
--

DROP TABLE IF EXISTS `gasto_costo`;
CREATE TABLE IF NOT EXISTS `gasto_costo` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `monto` double NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidencia`
--

DROP TABLE IF EXISTS `incidencia`;
CREATE TABLE IF NOT EXISTS `incidencia` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `id_user` int(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_incidencia_users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `indicador`
--

DROP TABLE IF EXISTS `indicador`;
CREATE TABLE IF NOT EXISTS `indicador` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(255) NOT NULL,
  `referencia` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

DROP TABLE IF EXISTS `inventario`;
CREATE TABLE IF NOT EXISTS `inventario` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `id_producto` int(255) NOT NULL,
  `precio` double NOT NULL,
  `cantidad` double NOT NULL,
  `expedicion` date NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_inventario_producto` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_resets_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_producto`
--

DROP TABLE IF EXISTS `orden_producto`;
CREATE TABLE IF NOT EXISTS `orden_producto` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `id_venta` int(255) DEFAULT NULL,
  `id_compra` int(255) DEFAULT NULL,
  `id_producto` int(255) NOT NULL,
  `cantidad` double NOT NULL,
  `precio` double NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_orden_producto_venta` (`id_venta`),
  KEY `fk_orden_producto_compra` (`id_compra`),
  KEY `fk_orden_producto_producto` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

DROP TABLE IF EXISTS `pago`;
CREATE TABLE IF NOT EXISTS `pago` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `banco` varchar(255) NOT NULL,
  `referencia` varchar(255) DEFAULT NULL,
  `nota_pago` varchar(255) NOT NULL,
  `fecha_pago` date NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago_nomina`
--

DROP TABLE IF EXISTS `pago_nomina`;
CREATE TABLE IF NOT EXISTS `pago_nomina` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `id_trabajador` int(255) NOT NULL,
  `mes` date NOT NULL,
  `monto` double NOT NULL,
  `id_pago` int(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pago_nomina_trabajador` (`id_trabajador`),
  KEY `fk_pago_nomina_pago` (`id_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('lguilarte1907@gmail.com', '$2y$10$zbNKd70YN5UywI11D/Vs4urXXyTL5U8ao2Z79.r8lfVkeo.EzCbOK', '2020-05-31 22:54:07'),
('Leomiguel1907@gmail.com', '$2y$10$iUe91BOtuqMNZG6PGSx6NuA/UsCWYS3Ly0wGAHmr0TIFJMi1tjnzK', '2020-05-31 23:17:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

DROP TABLE IF EXISTS `producto`;
CREATE TABLE IF NOT EXISTS `producto` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `id_categoria` int(255) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_producto_categoria` (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_receta`
--

DROP TABLE IF EXISTS `producto_receta`;
CREATE TABLE IF NOT EXISTS `producto_receta` (
  `id_producto_final` int(255) NOT NULL,
  `id_ingrediente` int(255) NOT NULL,
  `cantidad` double NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  KEY `fk_producto_receta_producto_final` (`id_producto_final`),
  KEY `fk_producto_receta_ingrediente` (`id_ingrediente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

DROP TABLE IF EXISTS `proveedor`;
CREATE TABLE IF NOT EXISTS `proveedor` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `persona_contacto` varchar(255) DEFAULT NULL,
  `id_zona` int(255) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `tipo_cid` varchar(255) NOT NULL,
  `rif_cedula` varchar(255) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_proveedor_zona` (`id_zona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reporte`
--

DROP TABLE IF EXISTS `reporte`;
CREATE TABLE IF NOT EXISTS `reporte` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `id_user` int(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `tipo` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reporte_users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suministro`
--

DROP TABLE IF EXISTS `suministro`;
CREATE TABLE IF NOT EXISTS `suministro` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `id_compra` int(255) NOT NULL,
  `id_producto` int(255) NOT NULL,
  `id_proveedor` int(255) DEFAULT NULL,
  `precio` double NOT NULL,
  `cantidad` double NOT NULL,
  `expedicion` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_suministro_producto` (`id_producto`),
  KEY `fk_suministro_proveedor` (`id_proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajador`
--

DROP TABLE IF EXISTS `trabajador`;
CREATE TABLE IF NOT EXISTS `trabajador` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(255) NOT NULL,
  `cedula` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `banco` varchar(255) NOT NULL,
  `num_cuenta` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `permiso_logistica` tinyint(1) DEFAULT NULL,
  `permiso_compra` tinyint(1) DEFAULT NULL,
  `permiso_venta` tinyint(1) DEFAULT NULL,
  `permiso_finanzas` tinyint(1) DEFAULT NULL,
  `permiso_cliente` tinyint(1) DEFAULT NULL,
  `permiso_proveedor` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `tipo`, `username`, `password`, `name`, `email`, `permiso_logistica`, `permiso_compra`, `permiso_venta`, `permiso_finanzas`, `permiso_cliente`, `permiso_proveedor`, `created_at`, `updated_at`, `remember_token`) VALUES
(1, 'admin', 'andres28ramirez', '$2y$10$YF3VWmYUuFnV42lsf.snvue5JE1jlmSt1HzsU.s0pLm8ugZ/dvTh2', 'Andres Ramirez', 'andresramirez2025@gmail.com', 1, 1, 1, 1, 1, 1, '2020-02-23 23:46:38', '2020-06-01 19:48:30', 'Ub5MgJhwYttTqq05DulQSvDmqNiF0GGOQiNeK0y890vqepfNYceNkodu3oYm');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

DROP TABLE IF EXISTS `venta`;
CREATE TABLE IF NOT EXISTS `venta` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(255) NOT NULL,
  `id_pago` int(255) DEFAULT NULL,
  `fecha` date NOT NULL,
  `monto` double NOT NULL,
  `credito` int(255) NOT NULL,
  `nota` varchar(255) NOT NULL,
  `pendiente` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_venta_cliente` (`id_cliente`),
  KEY `fk_venta_pago` (`id_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zona`
--

DROP TABLE IF EXISTS `zona`;
CREATE TABLE IF NOT EXISTS `zona` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `zona`
--

INSERT INTO `zona` (`id`, `nombre`) VALUES
(1, 'Acevedo'),
(2, 'Andres Bello'),
(3, 'Baruta'),
(4, 'Brión'),
(5, 'Bolívar'),
(6, 'Buroz'),
(7, 'Carrizal'),
(8, 'Chacao'),
(9, 'El Hatillo'),
(10, 'Guaicaipuro'),
(11, 'Gual'),
(12, 'Independencia'),
(13, 'Lander'),
(14, 'Los Salias'),
(15, 'Páez'),
(16, 'Paz Castillo'),
(17, 'Plaza'),
(18, 'Rojas'),
(19, 'Sucre'),
(20, 'Urdaneta'),
(21, 'Zamora');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calendario`
--
ALTER TABLE `calendario`
  ADD CONSTRAINT `calendario_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`),
  ADD CONSTRAINT `calendario_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`id`),
  ADD CONSTRAINT `calendario_ibfk_3` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajador` (`id`);

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `fk_cliente_zona` FOREIGN KEY (`id_zona`) REFERENCES `zona` (`id`);

--
-- Filtros para la tabla `compra`
--
ALTER TABLE `compra`
  ADD CONSTRAINT `fk_compra_pago` FOREIGN KEY (`id_pago`) REFERENCES `pago` (`id`),
  ADD CONSTRAINT `fk_compra_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id`);

--
-- Filtros para la tabla `despacho`
--
ALTER TABLE `despacho`
  ADD CONSTRAINT `fk_despacho_trabajador` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajador` (`id`),
  ADD CONSTRAINT `fk_despacho_venta` FOREIGN KEY (`id_venta`) REFERENCES `venta` (`id`);

--
-- Filtros para la tabla `desperdicio`
--
ALTER TABLE `desperdicio`
  ADD CONSTRAINT `fk_desperdicio_compra` FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id`),
  ADD CONSTRAINT `fk_desperdicio_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`);

--
-- Filtros para la tabla `egreso`
--
ALTER TABLE `egreso`
  ADD CONSTRAINT `fk_egreso_compra` FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id`),
  ADD CONSTRAINT `fk_egreso_gasto_costo` FOREIGN KEY (`id_gasto_costo`) REFERENCES `gasto_costo` (`id`),
  ADD CONSTRAINT `fk_egreso_pago_nomina` FOREIGN KEY (`id_pago_nomina`) REFERENCES `pago_nomina` (`id`);

--
-- Filtros para la tabla `incidencia`
--
ALTER TABLE `incidencia`
  ADD CONSTRAINT `fk_incidencia_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `fk_inventario_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`);

--
-- Filtros para la tabla `orden_producto`
--
ALTER TABLE `orden_producto`
  ADD CONSTRAINT `fk_orden_producto_compra` FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id`),
  ADD CONSTRAINT `fk_orden_producto_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`),
  ADD CONSTRAINT `fk_orden_producto_venta` FOREIGN KEY (`id_venta`) REFERENCES `venta` (`id`);

--
-- Filtros para la tabla `pago_nomina`
--
ALTER TABLE `pago_nomina`
  ADD CONSTRAINT `fk_pago_nomina_pago` FOREIGN KEY (`id_pago`) REFERENCES `pago` (`id`),
  ADD CONSTRAINT `fk_pago_nomina_trabajador` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajador` (`id`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`);

--
-- Filtros para la tabla `producto_receta`
--
ALTER TABLE `producto_receta`
  ADD CONSTRAINT `fk_producto_receta_ingrediente` FOREIGN KEY (`id_ingrediente`) REFERENCES `producto` (`id`),
  ADD CONSTRAINT `fk_producto_receta_producto_final` FOREIGN KEY (`id_producto_final`) REFERENCES `producto` (`id`);

--
-- Filtros para la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD CONSTRAINT `fk_proveedor_zona` FOREIGN KEY (`id_zona`) REFERENCES `zona` (`id`);

--
-- Filtros para la tabla `reporte`
--
ALTER TABLE `reporte`
  ADD CONSTRAINT `fk_reporte_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `suministro`
--
ALTER TABLE `suministro`
  ADD CONSTRAINT `fk_suministro_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`),
  ADD CONSTRAINT `fk_suministro_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id`);

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `fk_venta_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id`),
  ADD CONSTRAINT `fk_venta_pago` FOREIGN KEY (`id_pago`) REFERENCES `pago` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
