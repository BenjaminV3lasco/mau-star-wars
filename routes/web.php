<?php

use App\Http\Controllers\PersonajeController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Inicio');

Route::controller(PersonajeController::class)
    ->prefix('personajes')
    ->name('personajes.')
    ->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/crear', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::post('/importar', 'importar')->name('importar');
        // Las rutas con {personaje} van al final para que /crear no se lea como un ID.
        Route::get('/{personaje}', 'show')->name('show');
        Route::get('/{personaje}/editar', 'edit')->name('edit');
        Route::put('/{personaje}', 'update')->name('update');
        Route::delete('/{personaje}', 'destroy')->name('destroy');
    });
