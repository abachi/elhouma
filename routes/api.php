<?php

Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
    Route::post('register', 'AuthController@register')->name('auth.register');
    Route::post('login', 'AuthController@login')->name('auth.login');
    Route::post('logout', 'AuthController@logout')->name('auth.logout');
    Route::get('attempt', 'AuthController@attempt')->name('auth.attempt');
});

Route::group(['prefix' => 'reports'], function () {
    Route::get('index', 'ReportsController@index')->name('reports.index');
    Route::post('store', 'ReportsController@store')->name('reports.store');
    // update
    Route::group(['prefix' => 'update'], function(){
        Route::put('position', 'ReportsController@updatePosition')->name('reports.update.position');
        Route::put('description', 'ReportsController@updateDescription')->name('reports.update.description');
    });
    Route::post('update/picture', 'ReportsController@updatePicture')->name('reports.update.picture');
    Route::post('confirm', 'ReportsController@confirm')->name('reports.confirm');
    Route::post('fixed', 'ReportsController@fixed')->name('reports.fixed');
});

Route::group(['prefix' => 'users'], function(){
    Route::get('my-reports', 'ReportsController@myReports')->name('users.my-reports');
});
