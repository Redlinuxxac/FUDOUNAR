<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('web.index');
})->name('home');

Route::get('/Actividad', function () {
    return view('web.actividad');
})->name('Actividad');

Route::get('/contact', function () {
    return view('web.contact');
})->name('contact');