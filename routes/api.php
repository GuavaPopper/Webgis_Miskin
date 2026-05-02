<?php

use App\Http\Controllers\IbadahController;
use App\Http\Controllers\MiskinController;
use Illuminate\Support\Facades\Route;

Route::get('/ibadah', [IbadahController::class, 'index']);
Route::post('/ibadah', [IbadahController::class, 'store']);
Route::put('/ibadah/{id}', [IbadahController::class, 'update']);
Route::delete('/ibadah/{id}', [IbadahController::class, 'destroy']);

Route::get('/miskin', [MiskinController::class, 'index']);
Route::post('/miskin', [MiskinController::class, 'store']);
Route::put('/miskin/{id}', [MiskinController::class, 'update']);
Route::delete('/miskin/{id}', [MiskinController::class, 'destroy']);
