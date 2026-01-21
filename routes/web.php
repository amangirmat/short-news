<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace'  => 'Botble\ShortNews\Http\Controllers',
    'middleware' => ['web', 'core'],
], function () {

    // Check if the constant exists, otherwise use a fallback or skip the filter
    $filter = defined('BASE_FILTER_PUBLIC_RENDER_DATA') ? BASE_FILTER_PUBLIC_RENDER_DATA : 'public_render_data';

    Route::group(['apply_filter' => $filter], function () {
        Route::get('shorts', [
            'as'   => 'public.short-news',
            'uses' => 'ShortNewsController@index',
        ]);
    });

});

// --- Admin Ajax/Settings Routes (Optional) ---
Route::group([
    'namespace'  => 'Botble\ShortNews\Http\Controllers',
    'prefix'     => BaseHelper::getAdminPrefix() . '/short-news',
    'middleware' => ['web', 'core', 'auth'],
], function () {
    // You can add admin-specific settings routes here if you expand the plugin later
});