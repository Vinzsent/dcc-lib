<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ScannerController;

Route::match(['GET', 'HEAD'], '/', function () {
    return redirect()->route('login');
});

Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner');
Route::post('/scan', [ScannerController::class, 'scan'])->name('scan');

// Route::get('/', function () {
//     return redirect()->route('login');
// });

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Auth Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Admin Dashboard Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index']); // /admin -> dashboard
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/student-data', [AdminController::class, 'studentData'])->name('student-data');
    Route::post('/student-data', [AdminController::class, 'storeStudent'])->name('student-data.store');
    Route::put('/student-data/{student}', [AdminController::class, 'updateStudent'])->name('student-data.update');
    Route::delete('/student-data/{student}', [AdminController::class, 'destroyStudent'])->name('student-data.destroy');
    Route::get('/student-logs', [AdminController::class, 'studentLogs'])->name('student-logs');
    Route::get('/student-logs/export', [AdminController::class, 'exportLogs'])->name('student-logs.export');

    // Employee Routes
    Route::get('/employee-data', [AdminController::class, 'employeeData'])->name('employee-data');
    Route::post('/employee-data', [AdminController::class, 'storeEmployee'])->name('employee-data.store');
    Route::put('/employee-data/{employee}', [AdminController::class, 'updateEmployee'])->name('employee-data.update');
    Route::delete('/employee-data/{employee}', [AdminController::class, 'destroyEmployee'])->name('employee-data.destroy');
    Route::get('/employee-logs', [AdminController::class, 'employeeLogs'])->name('employee-logs');

    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
});

// Redirect generic dashboard to appropriate page based on role
Route::get('/dashboard', function () {
    if (Auth::user()->role === 'User') {
        return redirect()->route('scanner');
    }
    return redirect()->route('admin.dashboard');
})->middleware('auth');
