# 📊 VerticeSoft CRM – Módulo Control de Costos

## 🧠 Descripción General

Se incorpora el módulo **Control de Costos**, diseñado para gestionar los gastos asociados a proyectos sin convertir el sistema en un ERP contable completo.

Este módulo permite:

* Registrar costos por proyecto
* Controlar deudas con proveedores (cuentas por pagar)
* Registrar pagos parciales
* Generar reportes de ejecución y deuda

👉 Enfocado en proyectos de construcción y operaciones reales.

---

## 🎯 Objetivo

Brindar control financiero práctico dentro del CRM, permitiendo responder:

* ¿Cuánto se ha gastado en el proyecto?
* ¿Cuánto queda del presupuesto?
* ¿A quién le debo?
* ¿Cuánto debo?
* ¿Qué pagos están pendientes o vencidos?

---

## 🏗️ Estructura del Módulo

### 📁 Menú principal

```
Control de Costos
 ├── Costos
 ├── Cuentas por Pagar
 ├── Pagos
 └── Reportes
```

---

## 🧩 COMPONENTES

### 1. 💸 Costos del Proyecto

Registro de gastos asociados a proyectos.

#### Ejemplos:

* Materiales
* Mano de obra
* Subcontratos
* Alquiler de maquinaria
* Combustible
* Mantenimiento
* Administrativos

---

### 2. 📄 Cuentas por Pagar

Control de deudas con proveedores.

#### Permite:

* Saber a quién se le debe
* Registrar montos
* Controlar pagos parciales
* Ver saldos pendientes
* Manejar estados

#### Estados:

* pendiente
* parcial
* pagado
* vencido

---

### 3. 💰 Pagos

Registro de abonos o cancelaciones.

* pagos parciales
* múltiples pagos por deuda
* historial completo

---

### 4. 📊 Reportes

* cuentas por pagar generales
* cuentas por pagar por proyecto
* cuentas por pagar por proveedor
* vencimientos próximos

---

## 🗄️ BASE DE DATOS

### 📌 Tabla: proyecto_costos

```sql
id
proyecto_id
tipo
categoria
descripcion
monto
fecha
proveedor
requiere_pago (boolean)
estado_pago
created_at
updated_at
```

---

### 📌 Tabla: cuentas_por_pagar

```sql
id
proyecto_id
proyecto_costo_id
acreedor
concepto
monto_total
monto_pagado
saldo
fecha_registro
fecha_vencimiento
estado
observacion
created_at
updated_at
```

---

### 📌 Tabla: cuentas_por_pagar_pagos

```sql
id
cuenta_por_pagar_id
fecha_pago
monto
metodo_pago
referencia
observacion
created_at
updated_at
```

---

## 🔁 FLUJO DEL SISTEMA

1. Se registra un costo en el proyecto
2. Si requiere pago:
   → se genera automáticamente una cuenta por pagar
3. Se realizan pagos parciales o totales
4. Se actualiza:

   * monto_pagado
   * saldo
   * estado

---

## 🧠 EJEMPLO REAL

**Caso:**
Alquiler de retroexcavadora (Pedrito)

```
Monto: 800
Pagado: 300
Saldo: 500
Estado: Parcial
```

---

## 🎨 INTERFAZ (UI)

Dentro del proyecto:

* Tarjeta de resumen:

  * Presupuesto
  * Ejecutado
  * Disponible
  * % consumo

* Listado de costos

* Sección de cuentas por pagar

---

## 🔐 ROLES Y PERMISOS

⚠️ IMPORTANTE: Este módulo requiere integración con el sistema de permisos.

### Nuevos permisos recomendados:

```
costos.ver
costos.crear
costos.editar
costos.eliminar

cuentas_pagar.ver
cuentas_pagar.crear
cuentas_pagar.editar
cuentas_pagar.pagar

pagos.ver
pagos.crear

reportes.costos
```

---

### Roles sugeridos:

#### 👨‍💼 Administrador

* acceso total

#### 👷 Operador / Proyecto

* registrar costos
* ver cuentas por pagar
* registrar pagos

#### 👁️ Supervisor

* solo lectura
* acceso a reportes

---

## ⚠️ ALCANCE (IMPORTANTE)

Este módulo NO incluye:

* contabilidad formal
* plan de cuentas completo
* libro diario
* balance general
* estados financieros

👉 Eso queda para una **fase futura (ERP contable)**

---

## 🚀 FASE FUTURA (NO IMPLEMENTADO)

* Plan de cuentas (Activo, Pasivo, Capital, etc.)
* Cuentas por cobrar
* Estados financieros
* Integración contable completa

---

## 🧠 DECISIÓN ARQUITECTÓNICA

Se opta por:

✔ Control financiero enfocado en proyectos
✔ Simplicidad operativa
✔ Alta utilidad práctica

En lugar de:

❌ Sistema contable completo (complejo y fuera de alcance actual)

---

## 🏁 ESTADO

✔ Diseño aprobado
⏳ Pendiente implementación

---

## 🔥 NOTA FINAL

Este módulo convierte el CRM en un sistema **vendible**, ya que permite controlar dinero, no solo tareas.

> “Gestionas el proyecto y también controlas cuánto cuesta”

---
