# CRM Construcción

Sistema **CRM + ERP ligero** para empresas constructoras, desarrollado en **Laravel**, con soporte **multiempresa**, **roles y permisos avanzados (Spatie)**, y módulos de **inventario, proyectos y configuración**.

---

## Objetivo del Proyecto

Centralizar la gestión de:
- Empresas constructoras
- Usuarios y roles
- Permisos por módulo
- Inventario (existencias, movimientos, kárdex)
- Proyectos
- Configuración por empresa

Con una **arquitectura escalable**, visual moderna tipo **ERP PRO**, y control de acceso granular.

---

## Tecnologías Utilizadas

- **Laravel 10+**
- **PHP 8.2**
- **MySQL**
- **Spatie Laravel Permission**
- **Blade**
- **CSS custom (ERP PRO UI)**
- **Auth Laravel (Session / CSRF)**

---

##  Arquitectura General

###  Multiempresa
- Todas las entidades principales están relacionadas con `empresa_id`
- Un usuario puede:
  - No tener empresa (SuperAdmin)
  - Tener una empresa asignada (Admin Empresa / Usuario)

###  Control de Acceso
- Basado en **Roles + Permisos**
- Middleware `permission:*`
- Menú dinámico usando `@can`

---

##  Tipos de Usuario

###  SuperAdmin
- **NO pertenece a ninguna empresa**
- Control total del sistema
- Funciones:
  - Crear empresas
  - Crear usuarios
  - Asignar roles
  - Administrar permisos
  - Ver todo el inventario
  - Configurar sistema

Ejemplo:
Email: superadmin@crm.com

Clave: 911Panama
Rol: SuperAdmin


---

###  Administrador de Empresa
- Pertenece a **una empresa**
- Puede:
  - Configurar su empresa
  - Subir logo
  - Administrar usuarios de su empresa
  - Gestionar inventario
  - Gestionar proyectos

No puede:
- Ver otras empresas
- Administrar permisos globales

---

### Usuario Normal
- Acceso limitado según permisos asignados
- Solo ve lo que su rol permite

---

## Roles y Permisos (Spatie)

### Ejemplo de permisos:
```txt
dashboard.ver
admin.ver

usuarios.ver
usuarios.crear
usuarios.editar
usuarios.eliminar

roles.ver
roles.crear
roles.editar
roles.eliminar

permisos.ver
permisos.crear
permisos.editar
permisos.eliminar

empresas.ver
empresas.crear
empresas.editar
empresas.eliminar

proyectos.ver
proyectos.crear
proyectos.editar

inventario.ver
inventario.crear
kardex.ver

empresa.config.ver
empresa.config.editar
Módulos Implementados
Dashboard

Acceso controlado por permiso dashboard.ver

Visible según rol

Menú dinámico

Usuarios

CRUD completo

Asignación de:

Empresa (solo SuperAdmin)

Rol

Campo Activo / Inactivo

Seguridad con validaciones

Roles

CRUD completo

Asignación masiva de permisos

Permisos agrupados por módulo

UI con:

Marcar todo

Marcar por grupo

Permisos

CRUD completo desde UI

Formato recomendado: modulo.accion

Ejemplos rápidos

Cache limpiado automáticamente

Empresas

CRUD completo

Campos:

Nombre

RUC

Teléfono

Email

Dirección

Activa / Inactiva

Visual estilo ERP PRO

Proyectos

Asociados a empresa

Campos:

Código

Nombre

Ubicación

Fechas

Estado

Presupuesto

Activo

Inventario
Existencias

Stock por material y almacén

Multiempresa

Movimientos

Tipos:

Entrada

Salida

Traslado

Ajuste

Validación de stock negativo

Transacciones DB seguras

Kárdex

Entradas / Salidas / Saldo

Filtro por material y almacén

Totales calculados

UI / UX (ERP PRO)

Sidebar dinámico

Submenús animados

Componentes modernos:

Inputs

Selects

Botones

Alertas

Diseño responsive

Visual consistente en todos los módulos
Rutas

Rutas organizadas por:

/admin/*

/inventario/*

Middleware:

auth

permission:*

Seguridad

CSRF activo

Validaciones en todos los formularios

Transacciones en inventario

Cache de permisos controlado
app/
 └── Http/
     └── Controllers/
         ├── Admin/
         └── Inventario/

resources/
 └── views/
     ├── layouts/
     ├── admin/
     └── inventario/
Estado Actual

Sistema funcional
Roles y permisos operativos
Multiempresa estable
Inventario operativo
UI PRO consolidada

🔜 Próximos Pasos (Opcional)

Dashboard con métricas

Auditoría de movimientos

Reportes PDF / Excel

Configuración visual por empresa (colores)

Notificaciones

API REST

Autor

Proyecto desarrollado y diseñado Luis Robles
Arquitectura pensada para escala empresarial real


---

Si quieres, en el próximo paso podemos:
- Versionarlo como **v1.0**
- Preparar **instalación limpia**
- Crear **seeder oficial**
- Diseñar **Dashboard PRO**

Si quieres, el siguiente paso puede ser:
1️⃣ Pantalla Mi Empresa PRO (logo, colores, datos)
2️⃣ Forzar que Admin de empresa NO vea Empresas globales
3️⃣ Dashboard distinto para SuperAdmin vs Empresa
4️⃣ Seed automático de permisos y roles

Dime qué seguimos 💪
