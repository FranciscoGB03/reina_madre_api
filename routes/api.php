<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdjuntosController;
use App\Http\Controllers\ArticulosController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogosController;
use App\Http\Controllers\ComentariosController;
use App\Http\Controllers\PropiedadesController;
use App\Http\Controllers\SeguridadController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RelDepartamentoEmpresaController;

//|------Auth/Seguridad------|//
Route::post('login/attempt', [AuthController::class, 'attempt']);
Route::post('renuevaAccessToken', [AuthController::class, 'renuevaAccessToken']);

Route::prefix('seguridad')->middleware(['jwt'])->group(function () {
    Route::post('getPermisos', [SeguridadController::class, 'getPermisos']);
});

//|------Admin------|//
Route::prefix('admin')->middleware(['jwt'])->group(function () {
    Route::post('desbloquearArticulos', [AdminController::class, 'desbloquearArticulos']);
    Route::post('getConfiguraciones', [AdminController::class, 'getConfiguraciones']);
    Route::post('guardarCps', [AdminController::class, 'guardarCps']);
    Route::post('guardarRol', [AdminController::class, 'guardarRol']);
    Route::post('guardarRolPermisos', [AdminController::class, 'guardarRolPermisos']);
    Route::post('guardarConfiguraciones', [AdminController::class, 'guardarConfiguraciones']);
});

Route::prefix('admin')->group(function () {
    Route::post('getConfiguracionesActivas', [AdminController::class, 'getConfiguracionesActivas']);
});

//|------Catálogos------|//
Route::prefix('catalogos')->middleware(['jwt'])->group(function () {
    Route::post('eliminarGenerico/{clave}', [CatalogosController::class, 'eliminarGenerico']);
    Route::post('getActivosGenerico/{clave}', [CatalogosController::class, 'getActivosGenerico']);
    Route::post('getAllGenerico/{clave}', [CatalogosController::class, 'getAllGenerico']);
    Route::post('getGenerico/{clave}', [CatalogosController::class, 'getGenerico']);
    Route::post('getPorCampo/{clave}', [CatalogosController::class, 'getPorCampo']);
    Route::post('getMultiAllGenerico', [CatalogosController::class, 'getMultiAllGenerico']);
    Route::post('getMultiActivosGenerico', [CatalogosController::class, 'getMultiActivosGenerico']);
    Route::post('guardarGenerico/{clave}', [CatalogosController::class, 'guardarGenerico']);
});

//|------Usuarios------|//
Route::prefix('usuarios')->group(function () {
    Route::post('activarCuenta', [UsuariosController::class, 'activarCuenta']);
    Route::post('consultar', [UsuariosController::class, 'consultar']);
    Route::post('olvidePassword', [UsuariosController::class, 'olvidePassword']);
    Route::post('registrarUsuario', [UsuariosController::class, 'registrarUsuario']);
    Route::post('restablecerPassword', [UsuariosController::class, 'restablecerPassword']);
    Route::post('validarHash', [UsuariosController::class, 'validarHash']);
});
Route::prefix('usuarios')->middleware(['jwt'])->group(function () {
    Route::post('actualizar', [UsuariosController::class, 'actualizar']);
    Route::post('cambiarPassword', [UsuariosController::class, 'cambiarPassword']);
    Route::post('eliminarUsuario', [UsuariosController::class, 'eliminarUsuario']);
    Route::post('getAllUsuarios', [UsuariosController::class, 'getAllUsuarios']);
    Route::post('getMiPerfil', [UsuariosController::class, 'getMiPerfil']);
});
//|------EmpresaDepartamento------|//
Route::prefix('departamentoempresa')->middleware(['jwt'])->group(function () {
    Route::post('guardarDepartamentoEmpresa', [RelDepartamentoEmpresaController::class, 'guardarDepartamentoEmpresa']);
    Route::post('consultar', [RelDepartamentoEmpresaController::class, 'consultar']);
});

