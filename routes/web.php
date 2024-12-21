<?php

use App\Exports\OvertimeExport;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OvertimeController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

// Mengarahkan root ke controller
Route::get('/', [EmployeeController::class, 'index']);

Route::resource('employee', EmployeeController::class);

Route::resource('overtime', OvertimeController::class);

Route::get('export-overtime', [OvertimeController::class, 'export'])->name('export.overtime');

Route::get('export-overtime-pdf', [OvertimeController::class, 'toPdf'])->name('export.overtime.pdf');