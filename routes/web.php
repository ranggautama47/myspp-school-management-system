<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — MySPP
|--------------------------------------------------------------------------
*/

// Halaman utama — nanti akan jadi student portal (Phase 4)
Route::get('/', function () {
    return view('welcome');
});
