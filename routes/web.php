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

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/ubah-password', [AuthController::class, 'ubahPassword'])->name('ubah-password');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['prefix' => 'agen'], function () {
        Route::get('/', [AgenController::class, 'index']);
        Route::post('/list', [AgenController::class, 'list']);
        Route::get('/create', [AgenController::class, 'create']);
        Route::post('/add', [AgenController::class, 'store']);
        Route::get('/{id}/edit', [AgenController::class, 'edit']);
        Route::put('/{id}/update', [AgenController::class, 'update']);
        Route::put('/{id}/update_harga', [AgenController::class, 'update_harga']);
        Route::get('/{id}/show', [AgenController::class, 'show']);
        Route::get('/{id}/delete', [AgenController::class, 'confirm']);
        Route::delete('/{id}/delete', [AgenController::class, 'delete']);
        Route::get('/{id}', [AgenController::class, 'show']);
        Route::delete('/{id}', [AgenController::class, 'destroy']);
        Route::get('/{id}/export_pdf', [AgenController::class, 'export_pdf']);
    });

    Route::group(['prefix' => 'barang'], function () {
        Route::get('/', [BarangController::class, 'index']);              // menampilkan halaman awal barang
        Route::post('/list', [BarangController::class, 'list']);          // menampilkan data barang dalam bentuk json untuk datatables
        Route::get('/create', [BarangController::class, 'create']);       // menampilkan halaman form tambah barang
        Route::post('/add', [BarangController::class, 'store']);
        Route::get('/{id}/show', [BarangController::class, 'show']);            // menampilkan detail barang
        Route::get('/{id}/edit', [BarangController::class, 'edit']);       // menampilkan halaman form edit barang
        Route::put('/{id}/update', [BarangController::class, 'update']);          // menyimpan perubahan data barang
        Route::get('/{id}/delete', [BarangController::class, 'confirm']);
        Route::delete('/{id}/delete', [BarangController::class, 'delete']);
    });

    Route::group(['prefix' => 'transaksi'], function () {
        Route::get('/', [TransaksiController::class, 'index']);
        Route::post('/list', [TransaksiController::class, 'list']);
        Route::get('/create', [TransaksiController::class, 'create']);
        Route::post('/add', [TransaksiController::class, 'store']);
        Route::get('/{id}/edit', [TransaksiController::class, 'edit']);
        Route::put('/{id}/update', [TransaksiController::class, 'update']);
        Route::get('/{id}/show', [TransaksiController::class, 'show']);
        Route::get('/{id}/delete', [TransaksiController::class, 'confirm']);
        Route::delete('/{id}/delete', [TransaksiController::class, 'delete']);
        Route::get('/{id}', [TransaksiController::class, 'show']);
        Route::delete('/{id}', [TransaksiController::class, 'destroy']);
        // Route::get('/{id}/export_pdf', [TransaksiController::class, 'export_pdf']);
        Route::get('/{id}/print', [TransaksiController::class, 'printInvoice']); // PDF invoice
        Route::get('/{id}/send', [TransaksiController::class, 'sendInvoiceToWablas']); // Send invoice
        Route::get('/{id}/sendByEmail', [TransaksiController::class, 'sendInvoiceByEmail']); // Send invoice
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

    // Route::get('/transaksi', function () {
    //     return view('transaksi.index');
    // })->name('transaksi');

    Route::get('/barang', function () {
        return view('stok_barang.index');
    })->name('barang');

    // Route::get('/agen', function () {
    //     return view('agen.index');
    // })->name('agen');

    Route::get('/history', function () {
        return view('history.index');
    })->name('history');

    Route::get('/inbox', function () {
        return view('inbox.index');
    })->name('inbox');

    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile');
});
