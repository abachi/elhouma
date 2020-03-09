<?php

Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
    Route::post('register', 'AuthController@register')->name('auth.register');
    Route::post('login', 'AuthController@login')->name('auth.login');
    Route::post('logout', 'AuthController@logout')->name('auth.logout');
    Route::get('attempt', 'AuthController@attempt')->name('auth.attempt');
});

Route::group(['prefix' => 'reports'], function () {
    Route::get('index', 'ReportsController@index')->name('reports.index');
});
