<?php

use App\Http\Controllers\DevController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::prefix('system')->middleware(['authBasica'])->group(function(){
    Route::get('configCache', [SystemController::class, 'configCache']);
    Route::get('migrationPretend', [SystemController::class, 'migrationPretend']);
    Route::get('version', [SystemController::class, 'version']);
});

//borrar al subir a prod
Route::prefix('dev')->group(function(){
    Route::get('crearUsuario/{usuario}/{password}', [DevController::class, 'crearUsuario']);
    Route::get('fueTokenAlterado/{token}', [DevController::class, 'fueTokenAlterado']);
    Route::get('activarCuenta/{hash}', [DevController::class, 'activarCuenta']);
    Route::get('ordenConfirmada/{transaccion_id}', [DevController::class, 'ordenConfirmada']);
    Route::get('test/{hash}', [DevController::class, 'test']);
});
