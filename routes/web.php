<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/dashboard-data', [DashboardController::class, 'getDashboardData']);
Route::get('/buildings-table', [DashboardController::class, 'getBuildingsTable']);
Route::get('/building-details/{id}', [DashboardController::class, 'getBuildingDetails']);
Route::get('/available-progress-dates', [DashboardController::class, 'getAvailableDates']);
Route::get('/progress-dates', [DashboardController::class, 'getProgressDates']);
