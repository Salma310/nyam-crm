<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgenController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseController;

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

// 🔓 Route untuk Landing Page (Terbuka)
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('landing'); // <- buat file resources/views/landing.blade.php
})->name('landing');

// 🔐 Auth Routes (login/logout/ubah-password)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/ubahpassword', [AuthController::class, 'ubahPassword'])->name('ubah-password');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 🔒 Protected Routes - Hanya jika sudah login
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/filter', [DashboardController::class, 'filter'])->name('dashboard.filter');
    Route::post('/dashboard/filter-top-barang', [DashboardController::class, 'filterTopBarang'])->name('dashboard.filterTopBarang');
    Route::post('/dashboard/filter-top-agen', [DashboardController::class, 'filterTopAgen'])->name('dashboard.filterTopAgen');

    // Agen
    Route::prefix('agen')->group(function () {
        Route::get('/', [AgenController::class, 'index']);
        Route::post('/list', [AgenController::class, 'list']);
        Route::get('/create', [AgenController::class, 'create']);
        Route::post('/add', [AgenController::class, 'store']);
        Route::get('/{id}/edit', [AgenController::class, 'edit']);
        Route::put('/{id}/update', [AgenController::class, 'update']);
        Route::put('/{id}/update_harga', [AgenController::class, 'update_harga']);
        Route::get('/{id}/show', [AgenController::class, 'show'])->name('agen.show');
        Route::get('/{id}/delete', [AgenController::class, 'confirm']);
        Route::delete('/{id}/delete', [AgenController::class, 'delete']);
        Route::delete('/{id}/force-delete', [AgenController::class, 'forceDelete']);
        Route::get('/{id}', [AgenController::class, 'show']);
        Route::delete('/{id}', [AgenController::class, 'destroy']);
        Route::get('/{id}/export_pdf', [AgenController::class, 'export_pdf']);
        Route::post('/{id}/send-reminder', [AgenController::class, 'sendReminder'])->name('agen.sendReminder');
    });

    Route::prefix('barang')->group(function () {
        Route::get('/', [BarangController::class, 'index']);
        Route::post('/list', [BarangController::class, 'list']);
        Route::get('/create', [BarangController::class, 'create']);
        Route::post('/add', [BarangController::class, 'store']);
        Route::get('/{id}/show', [BarangController::class, 'show']);
        Route::get('/{id}/edit', [BarangController::class, 'edit']);
        Route::put('/{id}/update', [BarangController::class, 'update']);
        Route::get('/{id}/delete', [BarangController::class, 'confirm']);
        Route::delete('/{id}/delete', [BarangController::class, 'delete']);
    });

    Route::prefix('transaksi')->group(function () {
        Route::get('/', [TransaksiController::class, 'index'])->name('transindex');
        Route::post('/list', [TransaksiController::class, 'list'])->name('translist');
        Route::get('/create', [TransaksiController::class, 'create']);
        Route::post('/add', [TransaksiController::class, 'store']);
        Route::get('/{id}/edit', [TransaksiController::class, 'edit']);
        Route::put('/{id}/update', [TransaksiController::class, 'update']);
        Route::get('/{id}/show', [TransaksiController::class, 'show']);
        Route::get('/{id}/delete', [TransaksiController::class, 'confirm']);
        Route::delete('/{id}/delete', [TransaksiController::class, 'delete']);
        Route::get('/{id}/print', [TransaksiController::class, 'printInvoice']);
        Route::get('/{id}/send', [TransaksiController::class, 'sendInvoiceToWhapi']);
        Route::get('/{id}/sendByEmail', [TransaksiController::class, 'sendInvoiceByEmail']);
    });

    Route::get('/harga-agen/{agen_id}/{barang_id}', function ($agen_id, $barang_id) {
        $harga = \App\Models\HargaAgen::where('agen_id', $agen_id)
            ->where('barang_id', $barang_id)
            ->first();

        return response()->json(['harga' => $harga?->harga ?? null]);
    });

    Route::group(['prefix' => 'purchase'], function () {
        Route::get('/', [PurchaseController::class, 'index']);
        Route::post('/list', [PurchaseController::class, 'list']);
        Route::get('/create', [PurchaseController::class, 'create']);
        Route::post('/add', [PurchaseController::class, 'store']);
        Route::get('/{id}/show', [PurchaseController::class, 'show']);
        Route::get('/{id}/edit', [PurchaseController::class, 'edit']);
        Route::put('/{id}/update', [BarangController::class, 'update']);
        Route::get('/{id}/delete', [PurchaseController::class, 'confirm']);
        Route::delete('/{id}/delete', [PurchaseController::class, 'delete']);
        Route::get('/{id}/print', [PurchaseController::class, 'printInvoice']);
    });
});
