-- =========================================================
-- CRM CONSTRUCCION V2 - SQL compatible con InnoDB 767 bytes
-- (ajustes: varchar indexados a 191, descripcion a 191, model_type a 191, etc.)
-- =========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------
-- almacenes
-- -----------------------------
DROP TABLE IF EXISTS `almacenes`;
CREATE TABLE `almacenes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `codigo` varchar(30) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `ubicacion` varchar(200) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `almacenes_empresa_codigo_unique` (`empresa_id`,`codigo`),
  KEY `almacenes_empresa_id_index` (`empresa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `almacenes` (`id`, `empresa_id`, `codigo`, `nombre`, `ubicacion`, `activo`, `created_at`, `updated_at`) VALUES
(3, 1, 'ALM-001', 'Almacen Principal', 'EDIFICIO VERDE', 1, '2026-01-02 07:35:31', '2026-01-02 20:41:54'),
(4, 1, 'ALM-002', 'Almacén Capital', 'EDIFICIO VERDE', 1, '2026-01-02 07:44:35', '2026-01-11 00:45:42'),
(7, 2, 'ALM-001', 'ALMACEN 01', 'BODEA', 1, '2026-01-11 09:31:05', '2026-01-11 09:31:05');

-- -----------------------------
-- cache (key 191)
-- -----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:38:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:9:\"admin.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:13:\"dashboard.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:12:\"usuarios.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:14:\"usuarios.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:15:\"usuarios.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:17:\"usuarios.eliminar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:9:\"roles.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:11:\"roles.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:12:\"roles.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:14:\"roles.eliminar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:12:\"permisos.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:14:\"permisos.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:15:\"permisos.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:17:\"permisos.eliminar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:14;a:3:{s:1:\"a\";i:15;s:1:\"b\";s:12:\"empresas.ver\";s:1:\"c\";s:3:\"web\";}i:15;a:3:{s:1:\"a\";i:16;s:1:\"b\";s:14:\"empresas.crear\";s:1:\"c\";s:3:\"web\";}i:16;a:3:{s:1:\"a\";i:17;s:1:\"b\";s:15:\"empresas.editar\";s:1:\"c\";s:3:\"web\";}i:17;a:3:{s:1:\"a\";i:18;s:1:\"b\";s:17:\"empresas.eliminar\";s:1:\"c\";s:3:\"web\";}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:13:\"proyectos.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:15:\"proyectos.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:16:\"proyectos.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:18:\"proyectos.eliminar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:14:\"inventario.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:16:\"inventario.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:10:\"kardex.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:14:\"materiales.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:16:\"materiales.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:17:\"materiales.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:19:\"materiales.eliminar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:13:\"almacenes.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:15:\"almacenes.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:16:\"almacenes.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:18:\"almacenes.eliminar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:13:\"miempresa.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:16:\"miempresa.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:17:\"movimientos.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:18:\"movimientos.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:15:\"movimientos.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}}s:5:\"roles\";a:3:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:10:\"SuperAdmin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:5:\"Admin\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:10:\"Supervisor\";s:1:\"c\";s:3:\"web\";}}}', 1768192265);

-- -----------------------------
-- cache_locks (key 191)
-- -----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------
-- clases_construccion
-- -----------------------------
DROP TABLE IF EXISTS `clases_construccion`;
CREATE TABLE `clases_construccion` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clases_construccion_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------
-- users (email 191)
-- -----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_empresa_id_foreign` (`empresa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `empresa_id`, `name`, `email`, `email_verified_at`, `password`, `activo`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Super Admin', 'superadmin@crm.local', NULL, '$2y$12$ppok5p7THLTZGTEm9yLH5OCivDObb2wmg6dqZ2CaPvq0PGK3Rd8K2', 1, 'KastNFPiy2Gs8eMx8O7Sy6z81LGqSF47kzvf85x2L01KYM6yRCqpJKAOmcPu', '2025-12-30 09:33:38', '2025-12-30 09:33:38'),
(2, 1, 'Adan Noel', 'admin@evadan.com', NULL, '$2y$12$wg/lOuklv4vjZ3Je2wQG3uNhnTf8oUc2qkPSPsMunjHBnrTfM69LK', 1, 'dKTkcYKwaj7BAAmPu6iu1IWWNrXahKkWxLiX7RCflgZ75D74DhGf99Hp89rR', '2025-12-31 06:15:16', '2025-12-31 07:43:39'),
(3, 1, 'Eva Smith', 'supervisor@evadan.com', NULL, '$2y$12$wKVrYhSawGQUbSdJAobeOe7xK8xeVVnWyEUTVZoSZ/NBqrXbB1vea', 1, 'v2V5UQ5tdQNSCTSwErPUIcdfecKokiciDLLoAU0C0LKAmQhHSXPoByfRxvk4', '2025-12-31 08:38:21', '2026-01-08 04:29:57'),
(4, 2, 'Carlos Javier', 'admin@suplidorajc.com', NULL, '$2y$12$K.K3qDBXWpw2lzQEZtEU8O3isrmA7zLdtuaZ85C6q/vzzO2cp1ZFa', 1, 'xXwwtnxOyINwPnJje6XnPimuPHpENknucRGrLS5dn1rK5YRPiptDamWguD4a', '2026-01-11 07:13:44', '2026-01-11 07:58:37');

-- -----------------------------
-- empresas (FK a users)
-- -----------------------------
DROP TABLE IF EXISTS `empresas`;
CREATE TABLE `empresas` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(160) NOT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `ruc` varchar(80) DEFAULT NULL,
  `dv` varchar(5) DEFAULT NULL,
  `contacto` varchar(160) DEFAULT NULL,
  `direccion` varchar(220) DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `telefono` varchar(60) DEFAULT NULL,
  `email` varchar(160) DEFAULT NULL,
  `admin_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `empresas_admin_user_id_foreign` (`admin_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `empresas` (`id`, `nombre`, `logo_path`, `settings`, `ruc`, `dv`, `contacto`, `direccion`, `activa`, `telefono`, `email`, `admin_user_id`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'evadan constructora', 'empresas/logos/2u26anpjv7LfANCZsmlYTdQlrJkkZMzB354JLh9G.webp', NULL, '123456-15-123456', '15', 'Adan Noel', 'Panama', 1, '62440000', 'ventas@evadan.com', 2, 1, '2025-12-31 07:41:57', '2026-01-03 01:37:19'),
(2, 'SuplidoraJC', NULL, NULL, '100199-12-887755', NULL, NULL, NULL, 1, NULL, NULL, 4, 1, '2026-01-11 07:58:37', '2026-01-11 07:58:37');

-- -----------------------------
-- unidades
-- -----------------------------
DROP TABLE IF EXISTS `unidades`;
CREATE TABLE `unidades` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) NOT NULL,
  `descripcion` varchar(80) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unidades_codigo_unique` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `unidades` (`id`, `codigo`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'UND', 'Unidad', NULL, NULL),
(2, 'SACO', 'Saco', NULL, NULL),
(3, 'KG', 'Kilogramo', NULL, NULL),
(4, 'LBS', 'Libra', NULL, NULL),
(5, 'LTS', 'Litro', NULL, NULL),
(6, 'M', 'Metro', NULL, NULL),
(7, 'M2', 'Metro cuadrado', NULL, NULL),
(8, 'M3', 'Metro cúbico', NULL, NULL),
(9, 'CJ', 'Caja', NULL, NULL),
(10, 'GLN', 'Galón', NULL, NULL);

-- -----------------------------
-- materiales (descripcion 191 para UNIQUE empresa_id+descripcion)
-- -----------------------------
DROP TABLE IF EXISTS `materiales`;
CREATE TABLE `materiales` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `empresa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sku` varchar(50) NOT NULL,
  `descripcion` varchar(191) NOT NULL,
  `unidad` varchar(30) NOT NULL,
  `unidad_id` bigint(20) UNSIGNED NOT NULL,
  `clase_construccion_id` bigint(20) UNSIGNED DEFAULT NULL,
  `costo_estandar` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `materiales_sku_unique` (`sku`),
  UNIQUE KEY `materiales_empresa_codigo_unique` (`empresa_id`,`codigo`),
  UNIQUE KEY `materiales_empresa_descripcion_unique` (`empresa_id`,`descripcion`),
  KEY `materiales_unidad_id_foreign` (`unidad_id`),
  KEY `materiales_clase_construccion_id_foreign` (`clase_construccion_id`),
  KEY `materiales_empresa_id_index` (`empresa_id`),
  KEY `materiales_empresa_id_codigo_index` (`empresa_id`,`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `materiales` (`id`, `codigo`, `empresa_id`, `sku`, `descripcion`, `unidad`, `unidad_id`, `clase_construccion_id`, `costo_estandar`, `activo`, `created_at`, `updated_at`) VALUES
(2, 'MAT-001', 1, 'E1-MAT-001', 'cemento', 'Saco', 2, NULL, 0.0000, 1, '2026-01-02 03:40:46', '2026-01-11 03:11:01'),
(3, 'MAT-002', 1, 'E1-MAT-002', 'Ladrillos', 'Unidad', 1, NULL, 0.0000, 1, '2026-01-02 10:36:32', '2026-01-02 10:36:32'),
(4, 'MAT-100', 2, 'E2-MAT-100', 'Bloques 6', 'Unidad', 1, NULL, 0.0000, 1, '2026-01-11 09:14:47', '2026-01-11 09:14:47');

-- -----------------------------
-- inv_existencias
-- -----------------------------
DROP TABLE IF EXISTS `inv_existencias`;
CREATE TABLE `inv_existencias` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `almacen_id` bigint(20) UNSIGNED NOT NULL,
  `stock` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `costo_promedio` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inv_existencias_empresa_material_almacen_unique` (`empresa_id`,`material_id`,`almacen_id`),
  KEY `inv_existencias_almacen_id_foreign` (`almacen_id`),
  KEY `inv_existencias_empresa_id_index` (`empresa_id`),
  KEY `inv_existencias_material_id_index` (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `inv_existencias` (`id`, `empresa_id`, `material_id`, `cantidad`, `almacen_id`, `stock`, `costo_promedio`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 0.0000, 3, 640.0000, 8.4231, '2026-01-02 08:26:49', '2026-01-02 10:17:12'),
(2, 1, 2, 0.0000, 4, 10.0000, 0.0000, '2026-01-02 10:17:12', '2026-01-02 10:17:12'),
(3, 1, 3, 0.0000, 3, 510.0000, 0.8500, '2026-01-02 10:37:06', '2026-01-02 20:43:15'),
(4, 2, 4, 0.0000, 7, 100.0000, 0.4200, '2026-01-11 09:31:54', '2026-01-11 09:31:54');

-- -----------------------------
-- inv_movimientos
-- -----------------------------
DROP TABLE IF EXISTS `inv_movimientos`;
CREATE TABLE `inv_movimientos` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha` date NOT NULL,
  `tipo` enum('entrada','salida','traslado','ajuste') NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `almacen_origen_id` bigint(20) UNSIGNED DEFAULT NULL,
  `almacen_destino_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `costo_unitario` decimal(14,4) DEFAULT NULL,
  `referencia` varchar(80) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inv_movimientos_almacen_origen_id_foreign` (`almacen_origen_id`),
  KEY `inv_movimientos_almacen_destino_id_foreign` (`almacen_destino_id`),
  KEY `inv_movimientos_material_id_fecha_index` (`material_id`,`fecha`),
  KEY `inv_movimientos_empresa_id_index` (`empresa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `inv_movimientos` (`id`, `empresa_id`, `fecha`, `tipo`, `material_id`, `almacen_origen_id`, `almacen_destino_id`, `cantidad`, `costo_unitario`, `referencia`, `meta`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-01-02', 'entrada', 2, NULL, 3, 100, 8.0000, 'OC-001', NULL, '2026-01-02 08:26:49', '2026-01-02 08:26:49'),
(2, 1, '2026-01-02', 'entrada', 2, NULL, 3, 100, 8.5000, 'OC-001', NULL, '2026-01-02 08:30:46', '2026-01-02 08:30:46'),
(3, 1, '2026-01-02', 'entrada', 2, NULL, 3, 100, 8.5000, NULL, NULL, '2026-01-02 09:48:43', '2026-01-02 09:48:43'),
(4, 1, '2026-01-02', 'entrada', 2, NULL, 3, 100, 8.5000, 'oc-001', NULL, '2026-01-02 09:50:22', '2026-01-02 09:50:22'),
(5, 1, '2026-01-02', 'entrada', 2, NULL, 3, 100, 8.5000, 'oc-001', NULL, '2026-01-02 10:00:06', '2026-01-02 10:00:06'),
(6, 1, '2026-01-02', 'entrada', 2, NULL, 3, 100, 8.5000, NULL, NULL, '2026-01-02 10:08:41', '2026-01-02 10:08:41'),
(7, 1, '2026-01-02', 'entrada', 2, NULL, 3, 50, 8.5000, 'oc-002', NULL, '2026-01-02 10:15:33', '2026-01-02 10:15:33'),
(8, 1, '2026-01-02', 'traslado', 2, 3, 4, 10, NULL, NULL, NULL, '2026-01-02 10:17:12', '2026-01-02 10:17:12'),
(9, 1, '2026-01-02', 'entrada', 3, NULL, 3, 500, 0.8500, 'oc-003', NULL, '2026-01-02 10:37:06', '2026-01-02 10:37:06'),
(10, 1, '2026-01-02', 'ajuste', 3, NULL, 3, 10, 0.8500, NULL, NULL, '2026-01-02 20:43:15', '2026-01-02 20:43:15'),
(11, 2, '2026-01-11', 'ajuste', 4, NULL, 7, 100, 0.4200, NULL, NULL, '2026-01-11 09:31:54', '2026-01-11 09:31:54');

-- -----------------------------
-- proyectos
-- -----------------------------
DROP TABLE IF EXISTS `proyectos`;
CREATE TABLE `proyectos` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(40) DEFAULT NULL,
  `nombre` varchar(160) NOT NULL,
  `ubicacion` varchar(220) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` varchar(30) NOT NULL DEFAULT 'activo',
  `presupuesto` decimal(14,2) NOT NULL DEFAULT 0.00,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proyectos_empresa_id_estado_index` (`empresa_id`,`estado`),
  KEY `proyectos_empresa_id_codigo_index` (`empresa_id`,`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------
-- permissions (name/guard 191)
-- -----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin.ver', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(2, 'dashboard.ver', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(3, 'usuarios.ver', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(4, 'usuarios.crear', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(5, 'usuarios.editar', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(6, 'usuarios.eliminar', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(7, 'roles.ver', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(8, 'roles.crear', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(9, 'roles.editar', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(10, 'roles.eliminar', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(11, 'permisos.ver', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(12, 'permisos.crear', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(13, 'permisos.editar', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(14, 'permisos.eliminar', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(15, 'empresas.ver', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(16, 'empresas.crear', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(17, 'empresas.editar', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(18, 'empresas.eliminar', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(19, 'proyectos.ver', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(20, 'proyectos.crear', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(21, 'proyectos.editar', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(22, 'proyectos.eliminar', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(23, 'inventario.ver', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(24, 'inventario.crear', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(25, 'kardex.ver', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(26, 'materiales.ver', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(27, 'materiales.crear', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(28, 'materiales.editar', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(29, 'materiales.eliminar', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(30, 'almacenes.ver', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(31, 'almacenes.crear', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(32, 'almacenes.editar', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(33, 'almacenes.eliminar', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(34, 'miempresa.ver', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(35, 'miempresa.editar', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(36, 'movimientos.crear', 'web', '2026-01-02 08:28:47', '2026-01-02 08:28:47'),
(37, 'movimientos.editar', 'web', '2026-01-02 08:29:15', '2026-01-02 08:29:15'),
(38, 'movimientos.ver', 'web', '2026-01-02 08:29:40', '2026-01-02 08:29:40');

-- -----------------------------
-- roles (name/guard 191)
-- -----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'SuperAdmin', 'web', '2025-12-30 09:33:37', '2025-12-30 09:33:37'),
(2, 'Admin', 'web', '2025-12-30 09:33:38', '2025-12-30 09:33:38'),
(3, 'Supervisor', 'web', '2025-12-31 08:36:42', '2025-12-31 08:36:42');

-- -----------------------------
-- role_has_permissions
-- -----------------------------
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),(1, 2),
(2, 1),(2, 2),(2, 3),
(3, 1),(3, 2),
(4, 1),(4, 2),
(5, 1),(5, 2),
(6, 1),
(7, 1),(7, 2),
(8, 1),(8, 2),
(9, 1),(9, 2),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(19, 1),(19, 2),(19, 3),
(20, 1),(20, 2),(20, 3),
(21, 1),(21, 2),(21, 3),
(22, 1),(22, 3),
(23, 1),(23, 2),(23, 3),
(24, 1),(24, 2),(24, 3),
(25, 1),(25, 2),(25, 3),
(26, 1),(26, 2),(26, 3),
(27, 1),(27, 2),(27, 3),
(28, 1),(28, 2),(28, 3),
(29, 1),(29, 2),(29, 3),
(30, 1),(30, 2),(30, 3),
(31, 1),(31, 2),(31, 3),
(32, 1),(32, 2),(32, 3),
(33, 1),(33, 2),(33, 3),
(34, 1),(34, 2),
(35, 1),(35, 2),
(36, 2),
(37, 2),
(38, 2);

-- -----------------------------
-- model_has_permissions (model_type 191)
-- -----------------------------
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------
-- model_has_roles (model_type 191)
-- -----------------------------
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 3);

-- -----------------------------
-- migrations
-- -----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '0001_01_01_000003_create_empresas_table', 1),
(5, '0001_01_01_000004_create_proyectos_table', 1),
(6, '0001_01_01_000005_add_empresa_to_users_table', 1),
(7, '0001_01_01_000006_alter_empresas_add_fields', 1),
(8, '0001_01_01_000007_add_empresa_id_to_core_tables', 1),
(9, '2025_01_01_000000_add_config_to_empresas_table', 1),
(10, '2025_12_24_223733_create_unidades_table', 1),
(11, '2025_12_24_223734_create_clases_construccion_table', 1),
(12, '2025_12_24_223735_create_materiales_table', 1),
(13, '2025_12_24_223736_create_almacenes_table', 1),
(14, '2025_12_24_223737_create_inv_existencias_table', 1),
(15, '2025_12_24_223738_create_inv_movimientos_table', 1),
(16, '2025_12_25_000002_add_empresa_id_to_users_table', 2),
(17, '2025_12_25_040629_create_permission_tables', 2),
(18, '2025_12_25_150156_create_empresas_table', 5),
(19, '2025_12_25_150156_create_proyectos_table', 6),
(20, '2025_12_26_024111_create_empresas_table', 7),
(21, '2025_12_26_032750_add_dv_to_empresas_table', 7),
(22, '2025_12_26_033805_add_dv_activa_to_empresas_table', 7),
(23, '2025_12_26_033954_add_empresa_id_to_users_table', 7),
(24, '2025_12_26_034619_add_dv_activa_to_empresas_table', 8),
(25, '2025_12_26_235939_add_empresa_id_to_inv_existencias_table', 9),
(26, '2025_12_27_021124_add_codigo_to_proyectos_table', 9),
(27, '2025_12_29_000001_add_admin_user_id_to_empresas_table', 9),
(28, '2025_12_31_022153_add_campos_to_empresas_table', 9),
(29, '2025_12_30_000001_add_empresa_id_to_materiales_table', 10),
(30, '2025_12_30_000002_add_empresa_id_to_inv_movimientos_table', 10),
(31, '2025_12_30_000003_add_empresa_id_to_almacenes_table', 11),
(32, '2025_12_31_051643_add_codigo_to_materiales_table', 12),
(33, '2025_12_31_053005_add_unidad_to_materiales_table', 13),
(34, '2026_01_01_202125_patch_materiales_add_empresa_codigo_unidad_activo', 14),
(35, '2026_01_02_025333_add_empresa_id_and_cantidad_to_inv_existencias_table', 15),
(36, '2026_01_02_030329_add_unique_index_to_inv_existencias_table', 16);

-- -----------------------------
-- password_reset_tokens (email 191)
-- -----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------
-- sessions (id 191)
-- -----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------
-- jobs (queue 191 por index)
-- -----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------
-- job_batches (id 191)
-- -----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------
-- failed_jobs (uuid 191)
-- -----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- FKs (ya con tablas creadas)
-- =========================================================

ALTER TABLE `users`
  ADD CONSTRAINT `users_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`);

ALTER TABLE `empresas`
  ADD CONSTRAINT `empresas_admin_user_id_foreign` FOREIGN KEY (`admin_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `almacenes`
  ADD CONSTRAINT `almacenes_empresa_id_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL;

ALTER TABLE `materiales`
  ADD CONSTRAINT `materiales_clase_construccion_id_foreign` FOREIGN KEY (`clase_construccion_id`) REFERENCES `clases_construccion` (`id`),
  ADD CONSTRAINT `materiales_empresa_id_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `materiales_unidad_id_foreign` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`);

ALTER TABLE `inv_existencias`
  ADD CONSTRAINT `inv_existencias_almacen_id_foreign` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  ADD CONSTRAINT `inv_existencias_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inv_existencias_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materiales` (`id`);

ALTER TABLE `inv_movimientos`
  ADD CONSTRAINT `inv_movimientos_almacen_destino_id_foreign` FOREIGN KEY (`almacen_destino_id`) REFERENCES `almacenes` (`id`),
  ADD CONSTRAINT `inv_movimientos_almacen_origen_id_foreign` FOREIGN KEY (`almacen_origen_id`) REFERENCES `almacenes` (`id`),
  ADD CONSTRAINT `inv_movimientos_empresa_id_fk` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inv_movimientos_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materiales` (`id`);

ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

ALTER TABLE `proyectos`
  ADD CONSTRAINT `proyectos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`);

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
