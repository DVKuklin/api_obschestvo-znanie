<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('section', 'SectionCrudController');
    Route::crud('user', 'UserCrudController');
    Route::crud('theme', 'ThemeCrudController');
    Route::crud('paragraph', 'ParagraphCrudController');
    Route::crud('paragraphs_edit', 'ParagraphController');
    Route::crud('user_extended', 'UserExtendedController');
    Route::crud('additional-pages', 'AdditionalPagesCrudController');
    Route::crud('additional-pages-contents', 'AdditionalPagesContentsCrudController');
}); // this should be the absolute last line of this file