# README_CONTROL_COSTO.md

## Módulo: Control de Costos, Cuentas por Pagar y Flujo Financiero

Este documento resume las mejoras realizadas al módulo de proyectos para convertir el control de costos en un módulo financiero funcional dentro del CRM Construcción / VerticeSoft.

## Resumen general de lo implementado

Se trabajó sobre el módulo de **Proyectos**, especialmente en el control de costos y su integración con **Cuentas por Pagar**. El objetivo fue que los costos del proyecto no queden aislados, sino que puedan generar deuda pendiente, permitir pagos parciales, manejar vencimientos, exportar reportes y visualizar flujo de caja.

El flujo final queda así:

```text
Proyecto
  ├─ Costos
  │   ├─ pendiente/parcial → genera cuenta por pagar
  │   └─ pagado            → no genera cuenta por pagar
  ├─ Cuentas por pagar
  │   ├─ saldo pendiente
  │   ├─ pagos parciales
  │   ├─ historial de pagos
  │   ├─ alertas por vencimiento
  │   ├─ exportación Excel
  │   ├─ reporte por proveedor
  │   └─ flujo de caja
```

---

## Archivos principales modificados o creados

### Controladores

```text
app/Http/Controllers/Admin/ProyectoCostoController.php
app/Http/Controllers/Admin/CuentaPorPagarController.php
```

### Modelos

```text
app/Models/CuentaPorPagar.php
app/Models/CuentaPago.php
```

### Exportación Excel

```text
app/Exports/CuentasExport.php
```

### Vistas

```text
resources/views/admin/cuentas/index.blade.php
resources/views/admin/cuentas/show.blade.php
resources/views/admin/cuentas/reporte_proveedores.blade.php
resources/views/admin/cuentas/flujo_caja.blade.php
resources/views/partials/sidebar.blade.php
```

### Rutas

```text
routes/web.php
```

---

## Base de datos

Se agregaron dos tablas principales:

```text
cuentas_por_pagar
cuenta_pagos
```

También se reforzó la tabla de costos con campos relacionados al pago:

```text
proyecto_costos.proveedor
proyecto_costos.requiere_pago
proyecto_costos.estado_pago
```

La tabla `cuentas_por_pagar` incluye campos de vínculo automático con costos:

```text
origen_tipo
origen_id
```

Estos campos permiten identificar cuándo una cuenta por pagar nació desde un costo del proyecto.

---

## Cuentas por pagar

### Funcionalidades implementadas

- Listado general de cuentas por pagar.
- Dashboard financiero.
- Registro de pagos parciales.
- Historial de pagos por cuenta.
- Edición de datos básicos de la cuenta.
- Eliminación controlada.
- Alertas de cuentas vencidas.
- Alertas de cuentas próximas a vencer.
- Filtro por proveedor.
- Filtro por proyecto.
- Filtro por estado.
- Filtro de solo vencidas.
- Exportación a Excel.
- Reporte por proveedor.
- Flujo de caja.

### Estados manejados

```text
pendiente
parcial
pagado
```

### Reglas de pago

- Si se registra un pago menor al saldo, la cuenta pasa a `parcial`.
- Si se registra un pago igual al saldo, la cuenta pasa a `pagado`.
- No se permite pagar más que el saldo pendiente.
- Cada pago queda guardado en `cuenta_pagos`.

---

## Automatización desde costos

Se automatizó la creación de cuentas por pagar desde los costos del proyecto.

### Regla implementada

```text
Costo pendiente → crea cuenta por pagar
Costo parcial   → crea o actualiza cuenta por pagar
Costo pagado    → no crea cuenta por pagar
```

Si un costo cambia de estado a pagado:

- Si la cuenta no tiene pagos, puede eliminarse.
- Si ya tiene pagos, se conserva y se marca como pagada según corresponda.

Si un costo se elimina:

- Si la cuenta asociada no tiene pagos, se elimina.
- Si tiene pagos, se desvincula del costo para no perder historial.

---

## Dashboard financiero de cuentas por pagar

La vista principal de cuentas muestra:

- Total generado.
- Total pagado.
- Saldo pendiente.
- Saldo vencido.
- Porcentaje pagado.
- Cantidad de cuentas vencidas.
- Cuentas por vencer en los próximos 7 días.

---

## Alertas de vencimiento

Se agregó lógica visual para:

- Cuentas vencidas.
- Cuentas por vencer en los próximos 7 días.

Las cuentas vencidas se resaltan en rojo y las próximas a vencer se resaltan en ámbar.

La campana de notificaciones queda pendiente para una etapa final del sistema.

---

## Reporte por proveedor

Se agregó una vista para agrupar cuentas por proveedor.

Este reporte muestra por cada proveedor:

- Total generado.
- Total pagado.
- Saldo pendiente.
- Cantidad de cuentas.
- Cuentas asociadas por proyecto.

Ruta esperada:

```text
admin.cuentas.reporte.proveedores
```

---

## Flujo de caja

Se agregó una vista de flujo de caja mensual.

Muestra:

- Ingresos por pagos registrados.
- Egresos por costos/cuentas generadas.
- Flujo neto mensual.

Ruta esperada:

```text
admin.cuentas.flujo
```

---

## Exportación Excel

Se integró exportación con Laravel Excel.

### Paquete requerido

```bash
composer require maatwebsite/excel:^3.1 --with-all-dependencies
```

### Extensiones PHP requeridas

En XAMPP/servidor deben estar activas:

```ini
extension=gd
extension=zip
```

### Archivo exportador

```text
app/Exports/CuentasExport.php
```

### Ruta esperada

```text
admin.cuentas.exportar
```

---

## Rutas agregadas o utilizadas

Dentro del grupo:

```php
Route::prefix('admin')->name('admin.')->group(function () {
```

Se agregaron rutas como:

```php
Route::get('cuentas', [CuentaPorPagarController::class, 'index'])
    ->middleware('permission:proyectos.ver')
    ->name('cuentas.index');

Route::post('cuentas/store', [CuentaPorPagarController::class, 'store'])
    ->middleware('permission:proyectos.editar')
    ->name('cuentas.store');

Route::post('cuentas/{id}/pagar', [CuentaPorPagarController::class, 'pagar'])
    ->middleware('permission:proyectos.editar')
    ->name('cuentas.pagar');

Route::put('cuentas/{id}', [CuentaPorPagarController::class, 'update'])
    ->middleware('permission:proyectos.editar')
    ->name('cuentas.update');

Route::delete('cuentas/{id}', [CuentaPorPagarController::class, 'destroy'])
    ->middleware('permission:proyectos.editar')
    ->name('cuentas.destroy');

Route::get('cuentas/exportar', [CuentaPorPagarController::class, 'exportar'])
    ->middleware('permission:proyectos.ver')
    ->name('cuentas.exportar');

Route::get('cuentas/reporte/proveedores', [CuentaPorPagarController::class, 'reporteProveedores'])
    ->middleware('permission:proyectos.ver')
    ->name('cuentas.reporte.proveedores');

Route::get('cuentas/flujo-caja', [CuentaPorPagarController::class, 'flujoCaja'])
    ->middleware('permission:proyectos.ver')
    ->name('cuentas.flujo');
```

---

## Sidebar

Se integró **Cuentas por pagar** dentro del grupo de **Proyectos** para mantener el orden del sistema.

Estructura esperada:

```text
Proyectos
  ├─ Listado
  └─ Cuentas por pagar
```

---

## Comandos recomendados al subir al servidor

Después de subir archivos y ejecutar SQL:

```bash
composer install --no-dev --optimize-autoloader
composer dump-autoload
php artisan optimize:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear
```

Si se usa Laravel Excel en el servidor, verificar que PHP tenga:

```bash
php -m
```

Y confirmar:

```text
gd
zip
```

---

## Checklist para producción

Antes de subir:

- [ ] Hacer backup de la base de datos.
- [ ] Subir controladores actualizados.
- [ ] Subir modelos nuevos.
- [ ] Subir vistas nuevas.
- [ ] Subir exportador `CuentasExport.php`.
- [ ] Actualizar `routes/web.php`.
- [ ] Ejecutar SQL de cambios.
- [ ] Ejecutar comandos de limpieza Laravel.
- [ ] Probar crear costo pendiente.
- [ ] Confirmar creación automática de cuenta por pagar.
- [ ] Registrar pago parcial.
- [ ] Confirmar historial de pagos.
- [ ] Exportar Excel.
- [ ] Revisar reporte por proveedor.
- [ ] Revisar flujo de caja.

---

## Pendientes sugeridos para última etapa

- Campana de notificaciones para cuentas vencidas.
- Gráficos de flujo de caja.
- Excel premium con colores y totales.
- Reportes PDF.
- Dashboard financiero global del sistema.

