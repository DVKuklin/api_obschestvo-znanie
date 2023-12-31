<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAdminController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect('/admin/user');
});

Route::get('/admin/dashboard', function () {
    return redirect('/admin/user');
});

Route::get('/admin', function () {
    return redirect('/admin/user');
});
