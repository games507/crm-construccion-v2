-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-01-2026 a las 05:37:26
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `crm_construccion_v2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `empresa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `empresa_id`, `name`, `email`, `email_verified_at`, `password`, `activo`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Super Admin', 'superadmin@crm.local', NULL, '$2y$12$ppok5p7THLTZGTEm9yLH5OCivDObb2wmg6dqZ2CaPvq0PGK3Rd8K2', 1, 'KastNFPiy2Gs8eMx8O7Sy6z81LGqSF47kzvf85x2L01KYM6yRCqpJKAOmcPu', '2025-12-30 09:33:38', '2025-12-30 09:33:38'),
(2, 1, 'Adan Noel', 'admin@evadan.com', NULL, '$2y$12$wg/lOuklv4vjZ3Je2wQG3uNhnTf8oUc2qkPSPsMunjHBnrTfM69LK', 1, 'dKTkcYKwaj7BAAmPu6iu1IWWNrXahKkWxLiX7RCflgZ75D74DhGf99Hp89rR', '2025-12-31 06:15:16', '2025-12-31 07:43:39'),
(3, 1, 'Eva Smith', 'supervisor@evadan.com', NULL, '$2y$12$wKVrYhSawGQUbSdJAobeOe7xK8xeVVnWyEUTVZoSZ/NBqrXbB1vea', 1, 'v2V5UQ5tdQNSCTSwErPUIcdfecKokiciDLLoAU0C0LKAmQhHSXPoByfRxvk4', '2025-12-31 08:38:21', '2026-01-08 04:29:57'),
(4, 2, 'Carlos Javier', 'admin@suplidorajc.com', NULL, '$2y$12$K.K3qDBXWpw2lzQEZtEU8O3isrmA7zLdtuaZ85C6q/vzzO2cp1ZFa', 1, 'xXwwtnxOyINwPnJje6XnPimuPHpENknucRGrLS5dn1rK5YRPiptDamWguD4a', '2026-01-11 07:13:44', '2026-01-11 07:58:37');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_empresa_id_foreign` (`empresa_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
