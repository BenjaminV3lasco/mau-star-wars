<?php

use App\Http\Controllers\PersonajeController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Inicio');

Route::get('/personajes', [PersonajeController::class, 'index'])->name('personajes.index');
