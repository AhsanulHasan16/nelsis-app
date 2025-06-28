<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::get('products', function () {
    return Inertia::render('Products');
})->name('products');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
