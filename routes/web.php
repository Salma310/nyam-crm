<?php

use Illuminate\Support\Facades\Route;

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
    return view('dashboard');
})->name('dashboard');

Route::get('/transaksi', function () {
    return view('transaksi.index');
})->name('transaksi');

Route::get('/agen', function () {
    return view('agen.index');
})->name('agen');

Route::get('/history', function () {
    return view('history.index');
})->name('history');

Route::get('/inbox', function () {
    return view('inbox.index');
})->name('inbox');

Route::get('/profile', function () {
    return view('profile.index');
})->name('profile');