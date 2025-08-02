<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LimbahDiolahController;
use App\Http\Controllers\DataLimbahMasukController;
use App\Http\Controllers\KodeLimbahController;
use App\Http\Controllers\TrukController;
use App\Http\Controllers\MesinController;
use App\Http\Controllers\DashboardController;
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

//dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

//LimbahMasuk
Route::middleware('auth')->group(function () {
    Route::get('/inputlimbahmasuk', [LimbahMasukController::class, 'index'])->name('limbahmasuk.index');
    Route::post('/inputlimbahmasuk', [LimbahMasukController::class, 'store'])->name('limbahmasuk.store');
    Route::get('/limbahmasuk/import', [LimbahMasukController::class, 'import'])->name('limbahmasuk.import');
    Route::post('/limbahmasuk/import_ajax', [LimbahMasukController::class, 'import_ajax'])->name('limbahmasuk.import_ajax');
});

// DataLimbahMasuk
Route::get('/datalimbahmasuk', [DataLimbahMasukController::class, 'index'])->name('datalimbahmasuk.index');
Route::get('/detaillimbahmasuk/export_excel', [DataLimbahMasukController::class, 'export_excel'])->name('datalimbahmasuk.export_excel');
// Route::get('/detaillimbahmasuk/{id}', [DataLimbahMasukController::class, 'show'])->name('datalimbahmasuk.show');
Route::get('/detaillimbahmasuk-by-tanggal/{tanggal}', [DataLimbahMasukController::class, 'showByTanggal'])->name('datalimbahmasuk.showbytanggal');



//LimbahDiolah
Route::middleware('auth')->group(function () {
    Route::get('/inputlimbaholah', [LimbahDiolahController::class, 'index'])->name('limbahdiolah.index');
    Route::post('/inputlimbaholah', [LimbahDiolahController::class, 'store'])->name('limbahdiolah.store');
    Route::get('/datalimbaholah', [LimbahDiolahController::class, 'show'])->name('limbahdiolah.show');
    Route::post('/inputlimbaholah/import', [LimbahDiolahController::class, 'import'])->name('limbahdiolah.import');
    Route::get('/inputlimbaholah/template', [LimbahDiolahController::class, 'downloadTemplate'])->name('limbahdiolah.template');
    Route::get('/datalimbaholah/export', [LimbahDiolahController::class, 'export'])->name('limbahdiolah.export');
    Route::get('/detaillimbahdiolah/{mesin_id}', [LimbahDiolahController::class, 'getDetailByMesin'])->name('limbahdiolah.detail');

});

//kode limbah
Route::middleware('auth')->group(function () {
Route::get('/kodelimbah', [KodeLimbahController::class, 'index'])->name('kode-limbah.index');
Route::get('/kodelimbah/create', [KodeLimbahController::class, 'create'])->name('kode-limbah.create');
Route::post('/kodelimbah', [KodeLimbahController::class, 'store'])->name('kode-limbah.store');
Route::get('/kodelimbah/{id}/edit', [KodeLimbahController::class, 'edit'])->name('kode-limbah.edit');
Route::put('/kodelimbah/{id}', [KodeLimbahController::class, 'update'])->name('kode-limbah.update');
Route::delete('/kodelimbah/{id}', [KodeLimbahController::class, 'delete'])->name('kode-limbah.delete');
});
//Data Truk
Route::middleware('auth')->group(function () {
    Route::get('/datatruk', [TrukController::class, 'index'])->name('truk.index');
    Route::get('/datatruk/create', [TrukController::class, 'create'])->name('truk.create');
    Route::post('/datatruk', [TrukController::class, 'store'])->name('truk.store');
    Route::get('/datatruk/{id}/edit', [TrukController::class, 'edit'])->name('truk.edit');
    Route::put('/datatruk/{id}', [TrukController::class, 'update'])->name('truk.update');
    Route::delete('/datatruk/{id}', [TrukController::class, 'destroy'])->name('truk.destroy');
});
// Data Mesin
Route::middleware('auth')->group(function () {
    Route::get('/datamesin', [MesinController::class, 'index'])->name('mesin.index');
    Route::get('/datamesin/create', [MesinController::class, 'create'])->name('mesin.create');
    Route::post('/datamesin', [MesinController::class, 'store'])->name('mesin.store');
    Route::get('/datamesin/{id}/edit', [MesinController::class, 'edit'])->name('mesin.edit');
    Route::put('/datamesin/{id}', [MesinController::class, 'update'])->name('mesin.update');
    Route::delete('/datamesin/{id}', [MesinController::class, 'destroy'])->name('mesin.destroy');
});

