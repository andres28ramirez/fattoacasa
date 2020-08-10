-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-08-2020 a las 18:49:37
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `calendario`
--

INSERT INTO `calendario` (`id`, `cliente_id`, `proveedor_id`, `trabajador_id`, `start`, `end`, `title`, `descripcion`, `color`, `activo`) VALUES
(2, NULL, NULL, 1, '2020-07-12 13:00:00', '2020-07-12 13:00:00', 'Despacho', 'Entregar a tiempo, antes de las 5pm', '#FF0080', 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id`, `nombre`, `persona_contacto`, `id_zona`, `direccion`, `telefono`, `correo`, `tipo_cid`, `rif_cedula`, `created_at`, `updated_at`) VALUES
(1, 'Andres Ramirez', 'Andres Ramirez', 14, '4145 SW 151st Terrace', '+58-412-794-2183', 'andresramirez2025@gmail.com', 'V -', '23868394', '2020-06-25 18:17:14', '2020-06-25 18:19:16'),
(2, 'Excelsior Gama', 'Vicente Fernandez', 19, '4145 SW 151st Terrace', '0295-2624012', 'soporteexcelsior@gmail.com', 'V -', '11123456', '2020-06-25 18:18:23', '2020-07-13 19:32:28');

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
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_compra_proveedor` (`id_proveedor`),
  KEY `fk_compra_pago` (`id_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `compra`
--

INSERT INTO `compra` (`id`, `id_proveedor`, `id_pago`, `fecha`, `monto`, `credito`, `pendiente`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2020-06-25', 1160, 30, 1, '2020-06-25 19:01:21', '2020-07-16 19:59:24', NULL),
(2, 2, 3, '2020-06-28', 17400, 30, 1, '2020-06-25 19:04:45', '2020-07-16 20:00:43', NULL),
(3, 2, NULL, '2020-06-28', 15000, 30, 0, '2020-06-25 21:25:55', '2020-07-22 20:26:52', '2020-07-22 20:26:52'),
(4, 1, NULL, '2020-07-15', 1740, 30, 0, '2020-07-15 23:25:29', '2020-07-15 23:25:29', NULL),
(5, 1, NULL, '2020-07-14', 1740, 30, 0, '2020-07-15 23:58:06', '2020-07-16 17:09:10', NULL),
(7, 2, 9, '2020-03-14', 121.8, 30, 1, '2020-07-16 16:37:56', '2020-07-16 17:11:15', NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `despacho`
--

INSERT INTO `despacho` (`id`, `id_venta`, `id_trabajador`, `fecha`, `nota`, `entregado`, `created_at`, `updated_at`) VALUES
(1, 3, 1, '2020-06-30', 'Tocar el timbre!', 1, '2020-06-25 19:33:28', '2020-06-25 19:34:03'),
(2, 5, 1, '2020-07-17', 'Tocar el timbre', 0, '2020-07-16 18:36:22', '2020-07-16 18:36:22');

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `desperdicio`
--

INSERT INTO `desperdicio` (`id`, `id_compra`, `id_producto`, `cantidad`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 10, '2020-06-25 19:07:31', '2020-06-25 19:07:31'),
(2, 1, 1, 2, '2020-07-14 23:02:55', '2020-07-14 23:02:55'),
(3, 5, 1, 5, '2020-07-16 19:10:01', '2020-07-16 19:10:01'),
(4, 3, 1, 10, '2020-07-22 20:26:44', '2020-07-22 20:26:44');

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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `egreso`
--

INSERT INTO `egreso` (`id`, `id_compra`, `id_gasto_costo`, `id_pago_nomina`, `monto`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, 1160, '2020-06-25 19:01:21', '2020-06-25 19:01:21'),
(2, 2, NULL, NULL, 17400, '2020-06-25 19:04:45', '2020-06-25 19:04:45'),
(3, NULL, 1, NULL, 8700, '2020-06-25 19:35:10', '2020-06-25 19:35:10'),
(5, 3, NULL, NULL, 15000, '2020-06-25 21:25:55', '2020-06-25 21:25:55'),
(7, 4, NULL, NULL, 1740, '2020-07-15 23:25:29', '2020-07-15 23:25:29'),
(8, NULL, 2, NULL, 5000, '2020-07-15 23:47:48', '2020-07-15 23:47:48'),
(9, 5, NULL, NULL, 1740, '2020-07-15 23:58:06', '2020-07-15 23:58:06'),
(12, NULL, NULL, 5, 400, '2020-07-16 00:41:11', '2020-07-16 01:54:19'),
(14, NULL, NULL, 7, 14000, '2020-07-16 00:44:59', '2020-07-16 01:32:23'),
(15, NULL, NULL, 8, 100, '2020-07-16 16:14:53', '2020-07-16 16:14:53'),
(17, 7, NULL, NULL, 121.8, '2020-07-16 16:37:56', '2020-07-16 16:37:56');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `gasto_costo`
--

INSERT INTO `gasto_costo` (`id`, `fecha`, `descripcion`, `monto`, `tipo`, `created_at`, `updated_at`) VALUES
(1, '2020-06-30', 'Pago de luz', 8700, 'Costo', '2020-06-25 19:35:10', '2020-06-25 19:35:10'),
(2, '2020-07-15', 'Pago de Agua', 5000, 'Gasto', '2020-07-15 23:47:48', '2020-07-15 23:48:18');

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
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `incidencia`
--

INSERT INTO `incidencia` (`id`, `id_user`, `name`, `activity`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Fabiana M', 'Módulo Logística', 'Producto Añadido Código (1)', '2020-06-25 18:11:34', '2020-06-25 18:11:34'),
(2, 1, 'Fabiana M', 'Módulo Logística', 'Producto Añadido Código (2)', '2020-06-25 18:12:25', '2020-06-25 18:12:25'),
(3, 1, 'Fabiana M', 'Módulo Logística', 'Producto Añadido Código (3)', '2020-06-25 18:13:33', '2020-06-25 18:13:33'),
(4, 1, 'Fabiana M', 'Módulo Logística', 'Producto Editado Código (1)', '2020-06-25 18:13:45', '2020-06-25 18:13:45'),
(5, 1, 'Fabiana M', 'Módulo Logística', 'Producto Editado Código (2)', '2020-06-25 18:14:06', '2020-06-25 18:14:06'),
(6, 1, 'Fabiana M', 'Módulo Logística', 'Producto Editado Código (3)', '2020-06-25 18:14:29', '2020-06-25 18:14:29'),
(7, 1, 'Fabiana M', 'Módulo Clientes', 'Cliente Añadido Cédula/Rif (23868394)', '2020-06-25 18:17:14', '2020-06-25 18:17:14'),
(8, 1, 'Fabiana M', 'Módulo Clientes', 'Cliente Añadido Cédula/Rif (11123456)', '2020-06-25 18:18:23', '2020-06-25 18:18:23'),
(9, 1, 'Fabiana M', 'Módulo Clientes', 'Cliente Editado Cédula/Rif (23868394)', '2020-06-25 18:19:16', '2020-06-25 18:19:16'),
(10, 1, 'Fabiana M', 'Módulo Proveedores', 'Proveedor Añadido Cédula/Rif (312546879)', '2020-06-25 18:56:42', '2020-06-25 18:56:42'),
(11, 1, 'Fabiana M', 'Módulo Proveedores', 'Proveedor Añadido Cédula/Rif (879456312)', '2020-06-25 18:57:45', '2020-06-25 18:57:45'),
(12, 1, 'Fabiana M', 'Módulo Proveedores', 'Proveedor Editado Cédula/Rif (879456312)', '2020-06-25 18:58:15', '2020-06-25 18:58:15'),
(13, 1, 'Fabiana M', 'Módulo Compras', 'Compra Añadida - Código (1)', '2020-06-25 19:01:21', '2020-06-25 19:01:21'),
(14, 1, 'Fabiana M', 'Módulo Compras', 'Pago añadido - Código de Compra (1)', '2020-06-25 19:03:37', '2020-06-25 19:03:37'),
(15, 1, 'Fabiana M', 'Módulo Compras', 'Compra Añadida - Código (2)', '2020-06-25 19:04:45', '2020-06-25 19:04:45'),
(16, 1, 'Fabiana M', 'Módulo Compras', 'Compra Editada Código (2)', '2020-06-25 19:05:00', '2020-06-25 19:05:00'),
(17, 1, 'Fabiana M', 'Módulo Compras', 'Compra Editada Código (2)', '2020-06-25 19:05:14', '2020-06-25 19:05:14'),
(18, 1, 'Fabiana M', 'Módulo Compras', 'Despericio añadido - Código de Compra (2)', '2020-06-25 19:07:31', '2020-06-25 19:07:31'),
(19, 1, 'Fabiana M', 'Módulo Logística', 'Suministro Editado - Código (2)', '2020-06-25 19:09:06', '2020-06-25 19:09:06'),
(20, 1, 'Fabiana M', 'Módulo Logística', 'Inventario Añadido Código (3)', '2020-06-25 19:09:31', '2020-06-25 19:09:31'),
(21, 1, 'Fabiana M', 'Módulo Ventas', 'Venta Añadida - Código (1)', '2020-06-25 19:10:42', '2020-06-25 19:10:42'),
(22, 1, 'Fabiana M', 'Módulo Ventas', 'Venta Editada Código (1)', '2020-06-25 19:14:19', '2020-06-25 19:14:19'),
(23, 1, 'Fabiana M', 'Módulo Ventas', 'Venta Editada Código (1)', '2020-06-25 19:14:49', '2020-06-25 19:14:49'),
(24, 1, 'Fabiana M', 'Módulo Compras', 'Pago añadido - Código de Compra (2)', '2020-06-25 19:17:54', '2020-06-25 19:17:54'),
(25, 1, 'Fabiana M', 'Módulo Compras', 'Pago Editado - Código de Compra (2)', '2020-06-25 19:18:41', '2020-06-25 19:18:41'),
(26, 1, 'Fabiana M', 'Módulo Ventas', 'Venta Añadida - Código (3)', '2020-06-25 19:21:03', '2020-06-25 19:21:03'),
(27, 1, 'Fabiana M', 'Módulo Ventas', 'Pago añadido - Código de Venta (3)', '2020-06-25 19:22:03', '2020-06-25 19:22:03'),
(28, 1, 'Fabiana M', 'Módulo Ventas', 'Pago Editado - Código de Venta (3)', '2020-06-25 19:22:25', '2020-06-25 19:22:25'),
(29, 1, 'Fabiana M', 'Módulo Logística', 'Inventario Editado - Código (1)', '2020-06-25 19:22:59', '2020-06-25 19:22:59'),
(30, 1, 'Fabiana M', 'Configuracion', 'Trabajador Añadido - Cedula (26707992)', '2020-06-25 19:28:35', '2020-06-25 19:28:35'),
(31, 1, 'Fabiana M', 'Configuración', 'Trabajador Editado - Cédula (26707992)', '2020-06-25 19:28:57', '2020-06-25 19:28:57'),
(32, 1, 'Fabiana M', 'Configuracion', 'Trabajador Añadido - Cedula (22652343)', '2020-06-25 19:30:03', '2020-06-25 19:30:03'),
(33, 1, 'Fabiana M', 'Módulo Calendario', 'Nuevo Evento añadido (Despacho)', '2020-06-25 19:30:59', '2020-06-25 19:30:59'),
(34, 1, 'Fabiana M', 'Módulo Ventas', 'Despacho añadido - Código de Venta (3)', '2020-06-25 19:33:28', '2020-06-25 19:33:28'),
(35, 1, 'Fabiana M', 'Módulo Ventas', 'Despacho Editado - Código de Venta (3)', '2020-06-25 19:34:03', '2020-06-25 19:34:03'),
(36, 1, 'Fabiana M', 'Módulo Finanzas', 'Costo añadido - Código (1)', '2020-06-25 19:35:10', '2020-06-25 19:35:10'),
(37, 1, 'Fabiana M', 'Módulo Finanzas', 'Pago de Nómina añadido - Código (1)', '2020-06-25 19:36:37', '2020-06-25 19:36:37'),
(38, 1, 'Fabiana M', 'Módulo Finanzas', 'Pago de Nómina Eliminado - Código (1)', '2020-06-25 19:37:10', '2020-06-25 19:37:10'),
(39, 1, 'Fabiana M', 'Configuracion', 'Usuario Añadido - Id de Usuario (8)', '2020-06-25 19:38:42', '2020-06-25 19:38:42'),
(40, 1, 'Fabiana M', 'Configuración', 'Usuario Editado - ID del Usuario (8)', '2020-06-25 19:39:19', '2020-06-25 19:39:19'),
(41, 1, 'Fabiana M', 'Configuración', 'Usuario Editado - ID del Usuario (8)', '2020-06-25 19:41:39', '2020-06-25 19:41:39'),
(42, NULL, 'Andres Ramirez D', 'Módulo Ventas', 'Venta Editada Código (3)', '2020-06-25 19:42:16', '2020-08-10 16:48:22'),
(43, 1, 'Fabiana M', 'Módulo Logística', 'Producto Añadido Código (4)', '2020-06-25 21:25:15', '2020-06-25 21:25:15'),
(44, 1, 'Fabiana M', 'Módulo Compras', 'Compra Añadida - Código (3)', '2020-06-25 21:25:55', '2020-06-25 21:25:55'),
(45, 1, 'Fabiana M', 'Módulo Logística', 'Inventario Añadido Código (4)', '2020-06-25 21:26:23', '2020-06-25 21:26:23'),
(46, 1, 'Fabiana M', 'Módulo Ventas', 'Venta Añadida - Código (4)', '2020-06-25 21:27:06', '2020-06-25 21:27:06'),
(47, 1, 'Fabiana M', 'Módulo Ventas', 'Venta Añadida - Código (5)', '2020-07-12 20:30:13', '2020-07-12 20:30:13'),
(48, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Editada Código (5)', '2020-07-12 22:18:45', '2020-07-12 22:18:45'),
(49, 1, 'Fabiana Menichelli', 'Módulo Calendario', 'Nuevo Evento añadido (Despacho)', '2020-07-13 00:50:55', '2020-07-13 00:50:55'),
(50, 1, 'Fabiana Menichelli', 'Módulo Logística', 'Suministro Editado - Código (2)', '2020-07-13 00:56:48', '2020-07-13 00:56:48'),
(51, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago de Nómina añadido - Código (2)', '2020-07-13 15:12:48', '2020-07-13 15:12:48'),
(52, 1, 'Fabiana Menichelli', 'Módulo Clientes', 'Cliente Editado Cédula/Rif (11123456)', '2020-07-13 19:32:28', '2020-07-13 19:32:28'),
(53, 1, 'Fabiana Menichelli', 'Módulo Proveedores', 'Proveedor Editado Cédula/Rif (879456312)', '2020-07-13 19:32:53', '2020-07-13 19:32:53'),
(54, 1, 'Fabiana Menichelli', 'Módulo Proveedores', 'Proveedor Editado Cédula/Rif (879456312)', '2020-07-13 19:33:22', '2020-07-13 19:33:22'),
(55, 1, 'Fabiana Menichelli', 'Módulo Proveedores', 'Proveedor Editado Cédula/Rif (879456312)', '2020-07-13 19:33:35', '2020-07-13 19:33:35'),
(56, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Despericio añadido - Código de Compra (1)', '2020-07-14 23:02:55', '2020-07-14 23:02:55'),
(57, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (andresramirez2025@gmail.com - Andres Ramirez)', '2020-07-15 17:29:26', '2020-07-15 17:29:26'),
(58, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (soporteexcelsior@gmail.com - Excelsior Gama)', '2020-07-15 17:29:48', '2020-07-15 17:29:48'),
(59, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (andresramirez2025@gmail.com - Andres Ramirez)', '2020-07-15 18:09:54', '2020-07-15 18:09:54'),
(60, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (andresramirez2025@gmail.com - Andres Ramirez)', '2020-07-15 18:13:18', '2020-07-15 18:13:18'),
(61, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (andresramirez2025@gmail.com - Andres Ramirez)', '2020-07-15 19:31:36', '2020-07-15 19:31:36'),
(62, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (andresramirez2025@gmail.com - Andres Ramirez)', '2020-07-15 19:44:19', '2020-07-15 19:44:19'),
(63, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (andresramirez2025@gmail.com - Andres Ramirez)', '2020-07-15 19:44:23', '2020-07-15 19:44:23'),
(64, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (andresramirez2025@gmail.com - Andres Ramirez)', '2020-07-15 19:45:31', '2020-07-15 19:45:31'),
(65, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (andresramirez2025@gmail.com - Andres Ramirez)', '2020-07-15 19:45:54', '2020-07-15 19:45:54'),
(66, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (andresramirez2025@gmail.com - Andres Ramirez)', '2020-07-15 19:49:59', '2020-07-15 19:49:59'),
(67, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (andresramirez2025@gmail.com - Andres Ramirez)', '2020-07-15 19:52:11', '2020-07-15 19:52:11'),
(68, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (andresramirez2025@gmail.com - Andres Ramirez)', '2020-07-15 20:18:10', '2020-07-15 20:18:10'),
(69, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (andresramirez2025@gmail.com - Andres Ramirez)', '2020-07-15 20:18:28', '2020-07-15 20:18:28'),
(70, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Añadida - Código (4)', '2020-07-15 23:25:29', '2020-07-15 23:25:29'),
(71, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Editada Código (4)', '2020-07-15 23:39:43', '2020-07-15 23:39:43'),
(72, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Pago Editado - Código de Compra (2)', '2020-07-15 23:42:03', '2020-07-15 23:42:03'),
(73, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Pago Editado - Código de Compra (2)', '2020-07-15 23:42:12', '2020-07-15 23:42:12'),
(74, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Editada Código (5)', '2020-07-15 23:42:49', '2020-07-15 23:42:49'),
(75, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Pago Editado - Código de Venta (5)', '2020-07-15 23:45:31', '2020-07-15 23:45:31'),
(76, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Pago Editado - Código de Compra (2)', '2020-07-15 23:46:19', '2020-07-15 23:46:19'),
(77, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Gasto añadido - Código (2)', '2020-07-15 23:47:48', '2020-07-15 23:47:48'),
(78, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Gasto Editado - Código (2)', '2020-07-15 23:48:18', '2020-07-15 23:48:18'),
(79, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago Editado - Código de Pago (7)', '2020-07-15 23:49:11', '2020-07-15 23:49:11'),
(80, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Añadida - Código (5)', '2020-07-15 23:58:06', '2020-07-15 23:58:06'),
(81, 1, 'Fabiana Menichelli', 'Módulo Logística', 'Suministro Editado - Código (4)', '2020-07-15 23:58:55', '2020-07-15 23:58:55'),
(82, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Editada Código (4)', '2020-07-15 23:59:07', '2020-07-15 23:59:07'),
(83, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago de Global de Personal añadido - Código (3)', '2020-07-16 00:34:09', '2020-07-16 00:34:09'),
(84, NULL, 'Error en el Sistema', 'Módulo Finanzas', 'Error al almacenar pago global de personal - Código SQL [42S02]', '2020-07-16 00:37:18', '2020-07-16 00:37:18'),
(85, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago de Global de Personal añadido - Código (4)', '2020-07-16 00:38:02', '2020-07-16 00:38:02'),
(86, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago de Nómina Eliminado - Código (2)', '2020-07-16 00:39:27', '2020-07-16 00:39:27'),
(87, NULL, 'Error en el Sistema', 'Eliminación de Registro', 'Mensaje de Error [Internal Server Error [500].]', '2020-07-16 00:39:37', '2020-07-16 00:39:37'),
(88, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago de Nómina Eliminado - Código (4)', '2020-07-16 00:39:56', '2020-07-16 00:39:56'),
(89, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago de Global de Personal añadido - Código (5)', '2020-07-16 00:41:11', '2020-07-16 00:41:11'),
(90, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago de Global de Personal añadido - Código (6)', '2020-07-16 00:41:29', '2020-07-16 00:41:29'),
(91, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago de Nómina Eliminado - Código (6)', '2020-07-16 00:41:37', '2020-07-16 00:41:37'),
(92, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago de Nómina Eliminado - Código (3)', '2020-07-16 00:44:47', '2020-07-16 00:44:47'),
(93, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago de Global de Personal añadido - Código (7)', '2020-07-16 00:44:59', '2020-07-16 00:44:59'),
(94, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago Global de Personal Editado - Código (7)', '2020-07-16 01:32:23', '2020-07-16 01:32:23'),
(95, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago Editado - Código de Pago (7)', '2020-07-16 01:47:33', '2020-07-16 01:47:33'),
(96, 1, 'Fabiana Menichelli', 'Módulo Calendario', 'Nuevo Evento añadido (Despacho)', '2020-07-16 01:49:38', '2020-07-16 01:49:38'),
(97, 1, 'Fabiana Menichelli', 'Configuración', 'Trabajador Eliminado - Código (2)', '2020-07-16 01:49:46', '2020-07-16 01:49:46'),
(98, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago Global de Personal Editado - Código (5)', '2020-07-16 01:53:36', '2020-07-16 01:53:36'),
(99, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago Global de Personal Editado - Código (5)', '2020-07-16 01:54:19', '2020-07-16 01:54:19'),
(100, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago de Global de Personal añadido - Código (8)', '2020-07-16 16:14:53', '2020-07-16 16:14:53'),
(101, 1, 'Fabiana Menichelli', 'Módulo Finanzas', 'Pago Global de Personal Editado - Código (8)', '2020-07-16 16:15:17', '2020-07-16 16:15:17'),
(102, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Añadida - Código (6)', '2020-07-16 16:18:26', '2020-07-16 16:18:26'),
(103, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (andresramirez2025@gmail.com - Andres Ramirez)', '2020-07-16 16:18:40', '2020-07-16 16:18:40'),
(104, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (andresramirez2025@gmail.com - Andres Ramirez)', '2020-07-16 16:19:31', '2020-07-16 16:19:31'),
(105, 1, 'Fabiana Menichelli', 'Nuevo Correo', 'Nuevo Correo Enviado (andresramirez2025@gmail.com - Andres Ramirez)', '2020-07-16 16:20:01', '2020-07-16 16:20:01'),
(106, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Añadida - Código (6)', '2020-07-16 16:25:22', '2020-07-16 16:25:22'),
(107, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Eliminada - Código (6)', '2020-07-16 16:25:39', '2020-07-16 16:25:39'),
(108, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Pago añadido - Código de Compra (6)', '2020-07-16 16:26:42', '2020-07-16 16:26:42'),
(109, NULL, 'Error en el Sistema', 'Eliminación de Registro', 'Mensaje de Error [Internal Server Error [500].]', '2020-07-16 16:27:03', '2020-07-16 16:27:03'),
(110, NULL, 'Error en el Sistema', 'Eliminación de Registro', 'Mensaje de Error [Internal Server Error [500].]', '2020-07-16 16:28:59', '2020-07-16 16:28:59'),
(111, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Descartada - Código (6)', '2020-07-16 16:29:36', '2020-07-16 16:29:36'),
(112, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Descartada - Código (6)', '2020-07-16 16:32:31', '2020-07-16 16:32:31'),
(113, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Descartada - Código (6)', '2020-07-16 16:36:41', '2020-07-16 16:36:41'),
(114, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Añadida - Código (7)', '2020-07-16 16:37:56', '2020-07-16 16:37:56'),
(115, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Pago añadido - Código de Compra (7)', '2020-07-16 16:38:21', '2020-07-16 16:38:21'),
(116, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Descartada - Código (7)', '2020-07-16 16:38:40', '2020-07-16 16:38:40'),
(117, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Descartada - Código (7)', '2020-07-16 16:40:06', '2020-07-16 16:40:06'),
(118, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Descartada - Código (7)', '2020-07-16 16:43:15', '2020-07-16 16:43:15'),
(119, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Descartada - Código (5)', '2020-07-16 17:09:10', '2020-07-16 17:09:10'),
(120, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Descartada - Código (7)', '2020-07-16 17:11:15', '2020-07-16 17:11:15'),
(121, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Eliminada - Código (6)', '2020-07-16 17:18:02', '2020-07-16 17:18:02'),
(122, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Eliminada - Código (6)', '2020-07-16 17:18:56', '2020-07-16 17:18:56'),
(123, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Eliminada - Código (3)', '2020-07-16 17:19:21', '2020-07-16 17:19:21'),
(124, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Eliminada - Código (3)', '2020-07-16 17:21:23', '2020-07-16 17:21:23'),
(125, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Eliminada - Código (5)', '2020-07-16 17:23:05', '2020-07-16 17:23:05'),
(126, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Eliminada - Código (6)', '2020-07-16 18:33:35', '2020-07-16 18:33:35'),
(127, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Eliminada - Código (3)', '2020-07-16 18:33:35', '2020-07-16 18:33:35'),
(128, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Despacho añadido - Código de Venta (5)', '2020-07-16 18:36:22', '2020-07-16 18:36:22'),
(129, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Eliminada - Código (5)', '2020-07-16 18:37:25', '2020-07-16 18:37:25'),
(130, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Eliminada - Código (5)', '2020-07-16 18:45:50', '2020-07-16 18:45:50'),
(131, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Eliminada - Código (4)', '2020-07-16 18:47:52', '2020-07-16 18:47:52'),
(132, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Despericio añadido - Código de Compra (5)', '2020-07-16 19:10:01', '2020-07-16 19:10:01'),
(133, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Eliminada - Código (6)', '2020-07-16 19:16:49', '2020-07-16 19:16:49'),
(134, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Eliminada - Código (1)', '2020-07-16 19:21:57', '2020-07-16 19:21:57'),
(135, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Anexada Nuevamente - Código (6)', '2020-07-16 19:32:19', '2020-07-16 19:32:19'),
(136, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Anexada Nuevamente - Código (1)', '2020-07-16 19:35:40', '2020-07-16 19:35:40'),
(137, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Eliminada - Código (1)', '2020-07-16 19:36:00', '2020-07-16 19:36:00'),
(138, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Descartada - Código (1)', '2020-07-16 19:57:34', '2020-07-16 19:57:34'),
(139, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Descartada - Código (2)', '2020-07-16 19:57:45', '2020-07-16 19:57:45'),
(140, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Anexada Nuevamente - Código (1)', '2020-07-16 19:59:24', '2020-07-16 19:59:24'),
(141, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Anexada Nuevamente - Código (2)', '2020-07-16 20:00:43', '2020-07-16 20:00:43'),
(142, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Descartada - Código (3)', '2020-07-16 20:00:53', '2020-07-16 20:00:53'),
(143, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Anexada Nuevamente - Código (1)', '2020-07-16 20:08:41', '2020-07-16 20:08:41'),
(144, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Eliminada - Código (4)', '2020-07-16 20:09:04', '2020-07-16 20:09:04'),
(145, 1, 'Fabiana Menichelli', 'Módulo Ventas', 'Venta Anexada Nuevamente - Código (4)', '2020-07-16 20:09:16', '2020-07-16 20:09:16'),
(146, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Anexada Nuevamente - Código (3)', '2020-07-22 20:26:33', '2020-07-22 20:26:33'),
(147, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Despericio añadido - Código de Compra (3)', '2020-07-22 20:26:44', '2020-07-22 20:26:44'),
(148, 1, 'Fabiana Menichelli', 'Módulo Compras', 'Compra Descartada - Código (3)', '2020-07-22 20:26:52', '2020-07-22 20:26:52'),
(149, 1, 'Fabiana Menichelli', 'Configuración', 'Usuario Eliminado - ID (8)', '2020-08-10 16:48:22', '2020-08-10 16:48:22');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id`, `id_producto`, `precio`, `cantidad`, `expedicion`, `created_at`, `updated_at`) VALUES
(1, 3, 5000, 6, '2020-08-25', '2020-06-25 19:09:31', '2020-07-16 16:18:26'),
(2, 4, 500, 3, '2020-10-25', '2020-06-25 21:26:23', '2020-06-25 21:27:06');

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `orden_producto`
--

INSERT INTO `orden_producto` (`id`, `id_venta`, `id_compra`, `id_producto`, `cantidad`, `precio`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 1, 10, 100, '2020-06-25 19:01:21', '2020-06-25 19:01:21'),
(2, NULL, 2, 2, 100, 150, '2020-06-25 19:04:45', '2020-06-25 19:04:45'),
(3, 1, NULL, 3, 5, 600, '2020-06-25 19:10:42', '2020-06-25 19:10:42'),
(5, 3, NULL, 3, 4, 1500, '2020-06-25 19:21:03', '2020-06-25 19:21:03'),
(6, NULL, 3, 1, 100, 150, '2020-06-25 21:25:55', '2020-06-25 21:25:55'),
(7, 4, NULL, 4, 2, 2500, '2020-06-25 21:27:06', '2020-06-25 21:27:06'),
(8, 5, NULL, 3, 3, 100, '2020-07-12 20:30:13', '2020-07-12 20:30:13'),
(9, NULL, 4, 2, 15, 100, '2020-07-15 23:25:29', '2020-07-15 23:25:29'),
(10, NULL, 5, 1, 10, 150, '2020-07-15 23:58:06', '2020-07-15 23:58:06'),
(11, 6, NULL, 3, 1, 10, '2020-07-16 16:18:26', '2020-07-16 16:18:26'),
(13, NULL, 7, 1, 10, 10.5, '2020-07-16 16:37:56', '2020-07-16 16:37:56');

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pago`
--

INSERT INTO `pago` (`id`, `banco`, `referencia`, `nota_pago`, `fecha_pago`, `created_at`, `updated_at`) VALUES
(1, 'Otro', '123456789456', 'Pago por Zelle', '2020-06-25', '2020-06-25 19:03:37', '2020-06-25 19:03:37'),
(2, 'BanCaribe', '1593574862', 'Recibido a tiempo', '2020-06-28', '2020-06-25 19:14:49', '2020-06-25 19:14:49'),
(3, 'Banco Activo', '1789456789', 'Recibido a tiempo!!!', '2020-07-14', '2020-06-25 19:17:54', '2020-07-15 23:46:19'),
(4, 'Banco Plaza', '789456231', 'Por pagomovil!', '2020-07-28', '2020-06-25 19:22:03', '2020-06-25 19:22:25'),
(7, 'Otro', NULL, 'Pago por Efectivo (Bs)', '2020-07-14', '2020-07-15 23:42:49', '2020-07-16 01:47:33'),
(9, 'Otro', NULL, 'Pago por Efectivo (Bs)', '2020-07-16', '2020-07-16 16:38:21', '2020-07-16 16:38:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago_nomina`
--

DROP TABLE IF EXISTS `pago_nomina`;
CREATE TABLE IF NOT EXISTS `pago_nomina` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `id_trabajador` int(255) DEFAULT NULL,
  `mes` date NOT NULL,
  `monto` double NOT NULL,
  `id_pago` int(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pago_nomina_trabajador` (`id_trabajador`),
  KEY `fk_pago_nomina_pago` (`id_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pago_nomina`
--

INSERT INTO `pago_nomina` (`id`, `id_trabajador`, `mes`, `monto`, `id_pago`, `created_at`, `updated_at`) VALUES
(5, NULL, '2020-04-01', 400, NULL, '2020-07-16 00:41:11', '2020-07-16 01:54:19'),
(7, NULL, '2020-06-01', 14000, NULL, '2020-07-16 00:44:59', '2020-07-16 01:32:23'),
(8, NULL, '2020-08-01', 100, NULL, '2020-07-16 16:14:53', '2020-07-16 16:15:17');

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
('Leomiguel1907@gmail.com', '$2y$10$iUe91BOtuqMNZG6PGSx6NuA/UsCWYS3Ly0wGAHmr0TIFJMi1tjnzK', '2020-05-31 23:17:51'),
('andresramirez2025@gmail.com', '$2y$10$4qETCLJchhiqAXYPZqZLv.1Ea9KMjztFyhGUYoUcEeNk6OQBQZ4N.', '2020-07-16 20:22:41');

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `nombre`, `id_categoria`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Bolsa Plástica', 3, 'Bolsa Bio-degradable', '2020-06-25 18:11:34', '2020-06-25 18:13:45'),
(2, 'Manzana Roja', 3, 'Manzana de ingrediente para procesar un producto nuevo', '2020-06-25 18:12:25', '2020-06-25 18:14:06'),
(3, 'Manzana Congelada', 1, 'Producto procesado para entregar al público', '2020-06-25 18:13:33', '2020-06-25 18:14:29'),
(4, 'Pie de Manzana', 2, 'Para celebraciones', '2020-06-25 21:25:15', '2020-06-25 21:25:15');

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

--
-- Volcado de datos para la tabla `producto_receta`
--

INSERT INTO `producto_receta` (`id_producto_final`, `id_ingrediente`, `cantidad`, `created_at`, `updated_at`) VALUES
(3, 1, 1, '2020-06-25 18:14:29', '2020-06-25 18:14:29'),
(3, 2, 55, '2020-06-25 18:14:30', '2020-06-25 18:14:30'),
(4, 1, 1, '2020-06-25 21:25:15', '2020-06-25 21:25:15'),
(4, 2, 10, '2020-06-25 21:25:15', '2020-06-25 21:25:15');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id`, `nombre`, `persona_contacto`, `id_zona`, `direccion`, `telefono`, `correo`, `tipo_cid`, `rif_cedula`, `updated_at`, `created_at`) VALUES
(1, 'Agrofrutas', 'El MERO MERO', 16, 'dusseldorf', '0414-3836100', 'atencion@agrofrutas.com', 'J -', '312546879', '2020-06-25 18:56:42', '2020-06-25 18:56:42'),
(2, 'Nestor Semedo', 'N. Semedo', 7, '350 NE 24th St, APT 802', '0424-8624363', 'nsemedo@gmail.com', 'V -', '879456312', '2020-07-13 19:33:35', '2020-06-25 18:57:45');

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `suministro`
--

INSERT INTO `suministro` (`id`, `id_compra`, `id_producto`, `id_proveedor`, `precio`, `cantidad`, `expedicion`, `created_at`, `updated_at`) VALUES
(2, 2, 2, 2, 150, 400, '2020-07-15', '2020-06-25 19:04:45', '2020-07-13 00:56:48'),
(3, 3, 1, 2, 150, 95, NULL, '2020-06-25 21:25:55', '2020-06-25 21:26:23'),
(4, 4, 2, 1, 100, 15, '2020-07-17', '2020-07-15 23:25:29', '2020-07-15 23:58:55'),
(5, 5, 1, 1, 150, 10, NULL, '2020-07-15 23:58:06', '2020-07-15 23:58:06'),
(6, 6, 2, 2, 10.86, 10, '2020-10-16', '2020-07-16 16:25:22', '2020-07-16 16:25:22'),
(7, 7, 1, 2, 10.5, 10, '2020-07-16', '2020-07-16 16:37:56', '2020-07-16 16:37:56');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `trabajador`
--

INSERT INTO `trabajador` (`id`, `tipo`, `cedula`, `nombre`, `apellido`, `telefono`, `banco`, `num_cuenta`, `created_at`, `updated_at`) VALUES
(1, 'Despachador', '26707992', 'Katyan', 'Seekatz', '5612297145', 'Banco Mercantil', '0134-037325-3732-07475-6', '2020-06-25 19:28:35', '2020-06-25 19:28:57');

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `tipo`, `username`, `password`, `name`, `email`, `permiso_logistica`, `permiso_compra`, `permiso_venta`, `permiso_finanzas`, `permiso_cliente`, `permiso_proveedor`, `created_at`, `updated_at`, `remember_token`) VALUES
(1, 'admin', 'fabianam28', '$2y$10$vJx1XYILjUXkK1LcW5004Ok1Yysyt8h9h6zUNGiDTa7eXuKj.pIlu', 'Fabiana Menichelli', 'andresramirez2025@gmail.com', 1, 1, 1, 1, 1, 1, '2020-02-23 23:46:38', '2020-08-10 16:49:04', '7cOWzq849yZ0wquKhMB0HK9mWd3yqnjgQxWDEbywnaAaBfdfBfqNIp8sDarp');

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
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_venta_cliente` (`id_cliente`),
  KEY `fk_venta_pago` (`id_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `venta`
--

INSERT INTO `venta` (`id`, `id_cliente`, `id_pago`, `fecha`, `monto`, `credito`, `nota`, `pendiente`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 2, '2020-06-25', 3480, 30, 'Entregar en una cesta', 1, '2020-06-25 19:10:42', '2020-07-16 20:08:41', NULL),
(3, 1, 4, '2020-06-28', 6960, 30, 'No empaquetar nada!!!!!', 1, '2020-06-25 19:21:03', '2020-07-16 18:33:35', NULL),
(4, 2, NULL, '2020-06-28', 5800, 3, 'Entregar a brevedad', 0, '2020-06-25 21:27:06', '2020-07-16 20:09:16', NULL),
(5, 1, 7, '2020-07-12', 348, 3, 'LLevar sin empaquetar', 1, '2020-07-12 20:30:13', '2020-07-16 18:45:50', NULL),
(6, 1, NULL, '2020-07-16', 11.6, 30, 'LLevar sin empaquetar', 0, '2020-07-16 16:18:26', '2020-07-16 19:32:19', NULL);

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
