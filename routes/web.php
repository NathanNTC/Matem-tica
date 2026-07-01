<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetodoController;


Route::get('/', [MetodoController::class, 'index']);
Route::post('/calcular', [MetodoController::class, 'calcular']);
Route::get('/historico', [MetodoController::class, 'historico']);
Route::post('/recalcular', [MetodoController::class, 'recalcular']);
Route::get('/historico-dados/{id}', [MetodoController::class, 'dadosHistorico']);