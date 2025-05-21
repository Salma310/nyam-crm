<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgenController;
use App\Http\Controllers\BarangController;

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
Route::group(['prefix' => 'agen'], function () {
            Route::get('/', [AgenController::class, 'index']);
            Route::post('/list', [AgenController::class, 'list']);
            Route::get('/create', [AgenController::class, 'create']);
            Route::post('/', [AgenController::class, 'store']);
            Route::get('/{id}/edit', [AgenController::class, 'edit']);
            Route::get('/{id}/show', [AgenController::class, 'show']);
            Route::put('/{id}/update', [AgenController::class, 'update']);
            Route::get('/{id}/delete', [AgenController::class, 'confirm']);
            Route::delete('/{id}/delete', [AgenController::class, 'delete']);
            Route::get('/{id}', [AgenController::class, 'show']);
            Route::get('/{id}/edit', [AgenController::class, 'edit']);
            Route::put('/{id}', [AgenController::class, 'update']);
            Route::delete('/{id}', [AgenController::class, 'destroy']);
            Route::get('/{id}/export_pdf', [AgenController::class, 'export_pdf']);
});                                     

Route::group(['prefix' => 'barang'], function () {
        Route::get('/', [BarangController::class, 'index']);              // menampilkan halaman awal barang
        Route::post('/list', [BarangController::class, 'list']);          // menampilkan data barang dalam bentuk json untuk datatables
        Route::get('/create', [BarangController::class, 'create']);       // menampilkan halaman form tambah barang
        Route::post('/', [BarangController::class, 'store']);
        Route::get('/create_ajax', [BarangController::class, 'create_ajax']);
        Route::post('/ajax', [BarangController::class, 'store_ajax']);                 // menyimpan data barang baru
        Route::get('/{id}', [BarangController::class, 'show']);            // menampilkan detail barang
        Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax']);
        Route::get('/{id}/edit', [BarangController::class, 'edit']);       // menampilkan halaman form edit barang
        Route::put('/{id}', [BarangController::class, 'update']);          // menyimpan perubahan data barang
        Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']);
        Route::get('/import', [BarangController::class, 'import']);
        Route::post('/import_ajax', [BarangController::class, 'import_ajax']);
        Route::get('/export_excel', [BarangController::class, 'export_excel']);
        Route::get('/export_pdf', [BarangController::class, 'export_pdf']);
    });

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/transaksi', function () {
    return view('transaksi.index');
})->name('transaksi');

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