<?php

use App\Http\Controllers\PersonajeController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Inicio');

Route::get('/personajes', [PersonajeController::class, 'index'])->name('personajes.index');

Route::get('/personajes/crear', [PersonajeController::class, 'create'])->name('personajes.create');
Route::post('/personajes', [PersonajeController::class, 'store'])->name('personajes.store');

Route::get('/personajes/{personaje}/editar', [PersonajeController::class, 'edit'])->name('personajes.edit');
Route::put('/personajes/{personaje}', [PersonajeController::class, 'update'])->name('personajes.update');
