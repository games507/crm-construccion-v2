/*
===============================================================================
 CRM Construcción / VerticeSoft
 Módulo: Control de Costos + Cuentas por Pagar
 Archivo: CONTROL_COSTO_CAMBIOS_SERVIDOR.sql
 Uso: ejecutar en el servidor antes/después de subir los archivos PHP/Blade.
 Fecha: 2026-04-25
===============================================================================

IMPORTANTE:
- Haz backup de la base de datos antes de ejecutar.
- Este script está pensado para MySQL/MariaDB.
- Es idempotente en lo principal: usa CREATE TABLE IF NOT EXISTS y ALTER con
  validación mediante INFORMATION_SCHEMA para evitar errores por columnas existentes.
- Ajusta nombres si tu base usa prefijos diferentes.
===============================================================================
*/

SET @db := DATABASE();

/* -----------------------------------------------------------------------------
 1) Tabla principal de cuentas por pagar
----------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `cuentas_por_pagar` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `proyecto_id` BIGINT UNSIGNED NOT NULL,
  `proveedor` VARCHAR(180) NOT NULL,
  `descripcion` TEXT NULL,
  `monto_total` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `monto_pagado` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `saldo` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `fecha` DATE NULL,
  `fecha_vencimiento` DATE NULL,
  `estado` VARCHAR(30) NOT NULL DEFAULT 'pendiente',
  `origen_tipo` VARCHAR(50) NULL,
  `origen_id` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cpp_proyecto_id` (`proyecto_id`),
  KEY `idx_cpp_estado` (`estado`),
  KEY `idx_cpp_vencimiento` (`fecha_vencimiento`),
  KEY `idx_cpp_origen` (`origen_tipo`, `origen_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* -----------------------------------------------------------------------------
 2) Asegurar columnas nuevas si la tabla ya existía parcialmente
----------------------------------------------------------------------------- */

SET @sql := IF(
  NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='cuentas_por_pagar' AND COLUMN_NAME='origen_tipo'),
  'ALTER TABLE `cuentas_por_pagar` ADD COLUMN `origen_tipo` VARCHAR(50) NULL AFTER `estado`',
  'SELECT "origen_tipo ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='cuentas_por_pagar' AND COLUMN_NAME='origen_id'),
  'ALTER TABLE `cuentas_por_pagar` ADD COLUMN `origen_id` BIGINT UNSIGNED NULL AFTER `origen_tipo`',
  'SELECT "origen_id ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='cuentas_por_pagar' AND COLUMN_NAME='fecha_vencimiento'),
  'ALTER TABLE `cuentas_por_pagar` ADD COLUMN `fecha_vencimiento` DATE NULL AFTER `fecha`',
  'SELECT "fecha_vencimiento ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* Índices opcionales para tablas existentes */
SET @sql := IF(
  NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='cuentas_por_pagar' AND INDEX_NAME='idx_cpp_origen'),
  'ALTER TABLE `cuentas_por_pagar` ADD INDEX `idx_cpp_origen` (`origen_tipo`, `origen_id`)',
  'SELECT "idx_cpp_origen ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='cuentas_por_pagar' AND INDEX_NAME='idx_cpp_vencimiento'),
  'ALTER TABLE `cuentas_por_pagar` ADD INDEX `idx_cpp_vencimiento` (`fecha_vencimiento`)',
  'SELECT "idx_cpp_vencimiento ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* -----------------------------------------------------------------------------
 3) Tabla de historial de pagos de cuentas por pagar
----------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `cuenta_pagos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cuenta_id` BIGINT UNSIGNED NOT NULL,
  `monto` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `fecha` DATE NULL,
  `observacion` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cuenta_pagos_cuenta_id` (`cuenta_id`),
  KEY `idx_cuenta_pagos_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* -----------------------------------------------------------------------------
 4) Asegurar columnas esperadas en proyecto_costos si el módulo ya existía
----------------------------------------------------------------------------- */
SET @sql := IF(
  NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='proyecto_costos' AND COLUMN_NAME='proveedor'),
  'ALTER TABLE `proyecto_costos` ADD COLUMN `proveedor` VARCHAR(180) NULL AFTER `fecha`',
  'SELECT "proyecto_costos.proveedor ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='proyecto_costos' AND COLUMN_NAME='requiere_pago'),
  'ALTER TABLE `proyecto_costos` ADD COLUMN `requiere_pago` TINYINT(1) NOT NULL DEFAULT 0 AFTER `proveedor`',
  'SELECT "proyecto_costos.requiere_pago ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='proyecto_costos' AND COLUMN_NAME='estado_pago'),
  'ALTER TABLE `proyecto_costos` ADD COLUMN `estado_pago` VARCHAR(30) NOT NULL DEFAULT ''pendiente'' AFTER `requiere_pago`',
  'SELECT "proyecto_costos.estado_pago ya existe"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* -----------------------------------------------------------------------------
 5) Backfill opcional: crear cuentas por pagar desde costos existentes
    - Solo crea cuentas para costos pendientes/parciales.
    - Evita duplicar si ya existe origen_tipo='costo' y origen_id=costo.id.
----------------------------------------------------------------------------- */
INSERT INTO `cuentas_por_pagar` (
  `proyecto_id`,
  `proveedor`,
  `descripcion`,
  `monto_total`,
  `monto_pagado`,
  `saldo`,
  `fecha`,
  `fecha_vencimiento`,
  `estado`,
  `origen_tipo`,
  `origen_id`,
  `created_at`,
  `updated_at`
)
SELECT
  pc.`proyecto_id`,
  COALESCE(NULLIF(pc.`proveedor`, ''), 'Sin proveedor') AS proveedor,
  pc.`descripcion`,
  COALESCE(pc.`monto`, 0) AS monto_total,
  0 AS monto_pagado,
  COALESCE(pc.`monto`, 0) AS saldo,
  pc.`fecha`,
  pc.`fecha` AS fecha_vencimiento,
  'pendiente' AS estado,
  'costo' AS origen_tipo,
  pc.`id` AS origen_id,
  NOW(),
  NOW()
FROM `proyecto_costos` pc
LEFT JOIN `cuentas_por_pagar` cp
  ON cp.`origen_tipo` = 'costo'
 AND cp.`origen_id` = pc.`id`
WHERE cp.`id` IS NULL
  AND COALESCE(pc.`estado_pago`, 'pendiente') IN ('pendiente', 'parcial')
  AND COALESCE(pc.`monto`, 0) > 0;

/* -----------------------------------------------------------------------------
 6) Comandos Laravel recomendados después de subir archivos
----------------------------------------------------------------------------- */
/*
php artisan optimize:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
*/
