<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/pedidos-viagem', function () {
    return view('app.travel_request');
})->name('pedidos-viagem')->middleware('auth');