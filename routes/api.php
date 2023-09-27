<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\FavouritesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\Admin\AdminApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(PagesController::class)->group(function() {
    Route::get('sections','getSections');
    Route::post('get_themes_and_section_by_section_url',
                'getThemesAndSectionBySectionUrl');
    Route::post('get_section_name_and_theme_name_by_url',
                'getSectionNameAndThemeNameByUrl');
    Route::post('get_paragraps_by_section_and_theme_url',
                'getParagraphsBySectionAndThemeUrl')->middleware('auth:sanctum');
});

Route::prefix('favourites')->middleware('auth:sanctum')->controller(FavouritesController::class)->group(function() {
    Route::post('get_favourites', 'getFavourites');
    Route::post('add_to_favourites', 'addToFavourites');
    Route::post('remove_from_favourites', 'removeFromFavourites');
});

Route::controller(UserController::class)->group(function() {
    Route::post('auth','authUser');
    Route::post('login','login');
    Route::get('logout','logout')->middleware('auth:sanctum');
    Route::get('get_user_name','getUserName')->middleware('auth:sanctum');
    Route::get('is_authenticated','isAuthenticated')->middleware('auth:sanctum');
});

// Route::prefix('dev')->controller(DeveloperController::class)->group(function() {
//     Route::post('change_paragraps_from_old_table','changeParagraphsFromOldTable');
//     Route::post('test','test');
//     Route::post('set_theme_url','setThemeUrl');
//     Route::post('set_allowed_themes','setAllowedThemes');
//     Route::get('get_headers','getHeaders');
//     Route::post('test_get_token','getToken');
//     Route::get('test_get_me','getMe')->middleware('auth:sanctum');
//     Route::post('split_paragraphs','splitParagraphs');
//     Route::post('change_paragraps_from_old_table_v_01','changeParagraphsFromOldTableV01');
// });

// Route::post('/date_time_test', function (Request $request) {
//     // $d = time()+(3*60*60);

//     $d = time();
//     return date("d.m.Y H.i.s",$d) ;
// });

