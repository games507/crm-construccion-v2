<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\InventarioExistenciasController;

use App\Http\Controllers\Inventario\AlmacenController;
use App\Http\Controllers\Inventario\MaterialController;
use App\Http\Controllers\Inventario\MovimientosController;
use App\Http\Controllers\Inventario\InventarioController;
use App\Http\Controllers\Inventario\KardexController;

// ADMIN
use App\Http\Controllers\Admin\UsuariosController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\PermisosController;
use App\Http\Controllers\Admin\EmpresasController;
use App\Http\Controllers\Admin\ProyectosController;
use App\Http\Controllers\Admin\MiEmpresaController;

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| AUTH (GUEST)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| APP (AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // API Existencias (React Island) - protegido
    Route::get('/inventario/existencias/api', [InventarioExistenciasController::class, 'api'])
        ->middleware('permission:inventario.ver')
        ->name('inventario.existencias.api');

    /*
    |--------------------------------------------------------------------------
    | MI EMPRESA (Admin de empresa - Configuración propia)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->name('admin.')
        ->middleware(['permission:miempresa.ver'])
        ->group(function () {

            Route::get('mi-empresa', [MiEmpresaController::class, 'edit'])
                ->name('mi_empresa.edit');

            Route::put('mi-empresa', [MiEmpresaController::class, 'update'])
                ->middleware('permission:miempresa.editar')
                ->name('mi_empresa.update');
        });

    /*
    |--------------------------------------------------------------------------
    | ADMIN / CONFIGURACIÓN GLOBAL (SuperAdmin / Admin del sistema)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->name('admin.')
        ->middleware(['permission:admin.ver'])
        ->group(function () {

            // USUARIOS
            Route::get('usuarios', [UsuariosController::class, 'index'])
                ->middleware('permission:usuarios.ver')
                ->name('usuarios');

            Route::get('usuarios/create', [UsuariosController::class, 'create'])
                ->middleware('permission:usuarios.crear')
                ->name('usuarios.create');

            Route::post('usuarios', [UsuariosController::class, 'store'])
                ->middleware('permission:usuarios.crear')
                ->name('usuarios.store');

            Route::get('usuarios/{user}/edit', [UsuariosController::class, 'edit'])
                ->middleware('permission:usuarios.editar')
                ->name('usuarios.edit');

            Route::put('usuarios/{user}', [UsuariosController::class, 'update'])
                ->middleware('permission:usuarios.editar')
                ->name('usuarios.update');

            // ROLES
            Route::get('roles', [RolesController::class, 'index'])
                ->middleware('permission:roles.ver')
                ->name('roles');

            Route::get('roles/create', [RolesController::class, 'create'])
                ->middleware('permission:roles.crear')
                ->name('roles.create');

            Route::post('roles', [RolesController::class, 'store'])
                ->middleware('permission:roles.crear')
                ->name('roles.store');

            Route::get('roles/{role}/edit', [RolesController::class, 'edit'])
                ->middleware('permission:roles.editar')
                ->name('roles.edit');

            Route::put('roles/{role}', [RolesController::class, 'update'])
                ->middleware('permission:roles.editar')
                ->name('roles.update');

            Route::delete('roles/{role}', [RolesController::class, 'destroy'])
                ->middleware('permission:roles.eliminar')
                ->name('roles.destroy');

            // PERMISOS
            Route::get('permisos', [PermisosController::class, 'index'])
                ->middleware('permission:permisos.ver')
                ->name('permisos');

            Route::get('permisos/create', [PermisosController::class, 'create'])
                ->middleware('permission:permisos.crear')
                ->name('permisos.create');

            Route::post('permisos', [PermisosController::class, 'store'])
                ->middleware('permission:permisos.crear')
                ->name('permisos.store');

            Route::get('permisos/{permission}/edit', [PermisosController::class, 'edit'])
                ->middleware('permission:permisos.editar')
                ->name('permisos.edit');

            Route::put('permisos/{permission}', [PermisosController::class, 'update'])
                ->middleware('permission:permisos.editar')
                ->name('permisos.update');

            Route::delete('permisos/{permission}', [PermisosController::class, 'destroy'])
                ->middleware('permission:permisos.eliminar')
                ->name('permisos.destroy');

            // EMPRESAS
            Route::get('empresas', [EmpresasController::class, 'index'])
                ->middleware('permission:empresas.ver')
                ->name('empresas');

            Route::get('empresas/create', [EmpresasController::class, 'create'])
                ->middleware('permission:empresas.crear')
                ->name('empresas.create');

            Route::post('empresas', [EmpresasController::class, 'store'])
                ->middleware('permission:empresas.crear')
                ->name('empresas.store');

            Route::get('empresas/{empresa}/edit', [EmpresasController::class, 'edit'])
                ->middleware('permission:empresas.editar')
                ->name('empresas.edit');

            Route::put('empresas/{empresa}', [EmpresasController::class, 'update'])
                ->middleware('permission:empresas.editar')
                ->name('empresas.update');

            Route::delete('empresas/{empresa}', [EmpresasController::class, 'destroy'])
                ->middleware('permission:empresas.eliminar')
                ->name('empresas.destroy');

            // PROYECTOS
            Route::get('proyectos', [ProyectosController::class, 'index'])
                ->middleware('permission:proyectos.ver')
                ->name('proyectos');

            Route::get('proyectos/create', [ProyectosController::class, 'create'])
                ->middleware('permission:proyectos.crear')
                ->name('proyectos.create');

            Route::post('proyectos', [ProyectosController::class, 'store'])
                ->middleware('permission:proyectos.crear')
                ->name('proyectos.store');

            Route::get('proyectos/{proyecto}/edit', [ProyectosController::class, 'edit'])
                ->middleware('permission:proyectos.editar')
                ->name('proyectos.edit');

            Route::put('proyectos/{proyecto}', [ProyectosController::class, 'update'])
                ->middleware('permission:proyectos.editar')
                ->name('proyectos.update');
        });

    /*
    |--------------------------------------------------------------------------
    | INVENTARIO
    |--------------------------------------------------------------------------
    */
    Route::prefix('inventario')
        ->name('inventario.')
        ->group(function () {

            // EXISTENCIAS
            Route::get('existencias', [InventarioController::class, 'existencias'])
                ->middleware('permission:inventario.ver')
                ->name('existencias');

            // ALMACENES (CRUD)
            Route::get('almacenes', [AlmacenController::class, 'index'])
                ->middleware('permission:almacenes.ver')
                ->name('almacenes');

            Route::get('almacenes/create', [AlmacenController::class, 'create'])
                ->middleware('permission:almacenes.crear')
                ->name('almacenes.create');

            Route::post('almacenes', [AlmacenController::class, 'store'])
                ->middleware('permission:almacenes.crear')
                ->name('almacenes.store');

            Route::get('almacenes/{almacen}/edit', [AlmacenController::class, 'edit'])
                ->middleware('permission:almacenes.editar')
                ->name('almacenes.edit');

            Route::put('almacenes/{almacen}', [AlmacenController::class, 'update'])
                ->middleware('permission:almacenes.editar')
                ->name('almacenes.update');

            Route::delete('almacenes/{almacen}', [AlmacenController::class, 'destroy'])
                ->middleware('permission:almacenes.eliminar')
                ->name('almacenes.destroy');

            // Desactivar almacén (NO rompe FK)
            Route::post('almacenes/{almacen}/deactivate', [AlmacenController::class, 'deactivate'])
                ->middleware('permission:almacenes.eliminar')
                ->name('almacenes.deactivate');

            // MATERIALES (CRUD)
            Route::get('materiales', [MaterialController::class, 'index'])
                ->middleware('permission:materiales.ver')
                ->name('materiales');

            Route::get('materiales/create', [MaterialController::class, 'create'])
                ->middleware('permission:materiales.crear')
                ->name('materiales.create');

            Route::post('materiales', [MaterialController::class, 'store'])
                ->middleware('permission:materiales.crear')
                ->name('materiales.store');

            Route::get('materiales/{material}/edit', [MaterialController::class, 'edit'])
                ->middleware('permission:materiales.editar')
                ->name('materiales.edit');

            Route::put('materiales/{material}', [MaterialController::class, 'update'])
                ->middleware('permission:materiales.editar')
                ->name('materiales.update');

            Route::delete('materiales/{material}', [MaterialController::class, 'destroy'])
                ->middleware('permission:materiales.eliminar')
                ->name('materiales.destroy');

            // KARDEX
            Route::get('kardex', [KardexController::class, 'index'])
                ->middleware('permission:kardex.ver')
                ->name('kardex');

            Route::get('kardex/ver', [KardexController::class, 'kardexVer'])
                ->middleware('permission:kardex.ver')
                ->name('kardex.ver');

            // MOVIMIENTOS
            Route::get('movimientos', [MovimientosController::class, 'index'])
                ->middleware('permission:inventario.ver')
                ->name('movimientos');

            Route::get('movimientos/create', [MovimientosController::class, 'create'])
                ->middleware('permission:inventario.crear')
                ->name('movimientos.create');

            Route::post('movimientos', [MovimientosController::class, 'store'])
                ->middleware('permission:inventario.crear')
                ->name('movimientos.store');
        });
});
