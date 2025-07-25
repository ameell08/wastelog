<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LimbahMasukController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('landingpage');
});

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'postlogin'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/dashboard', function () {
    return 'Berhasil login! Selamat datang di dashboard.';
})->middleware('auth');

//LimbahMasuk
Route::middleware('auth')->group(function () {
    Route::get('/inputlimbahmasuk', [LimbahMasukController::class, 'index'])->name('limbahmasuk.index');
    Route::post('/inputlimbahmasuk', [LimbahMasukController::class, 'store'])->name('limbahmasuk.store');
});
