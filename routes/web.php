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
    if (extension_loaded('gd') && function_exists('gd_info')) {
        echo "hm no, GD is not the problem...";
    }else{
        echo "ah! I think we found it ";
    }
    die();
    return view('welcome');
});
