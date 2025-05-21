<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgenController;
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