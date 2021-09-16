<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::get('/export', function () {
    return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\BarangaysExport, 'barangays.csv');
});

Route::view('/','index');
