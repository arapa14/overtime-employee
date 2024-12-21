<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OvertimeController;
use Illuminate\Support\Facades\Route;

// Mengarahkan root ke controller
Route::get('/', [EmployeeController::class, 'index']);

Route::resource('employee', EmployeeController::class);

Route::resource('overtime', OvertimeController::class);