<?php

Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
    Route::post('register', 'AuthController@register')->name('auth.register');
    Route::post('login', 'AuthController@login')->name('auth.login');
    Route::post('logout', 'AuthController@logout')->name('auth.logout');
    Route::get('attempt', 'AuthController@attempt')->name('auth.attempt');
});


Route::post('/reports/confirm', 'ReportsController@confirm')->name('reports.confirm');
Route::post('/reports/fixed', 'ReportsController@fixed')->name('reports.fixed');

Route::get('/reports', 'ReportsController@index')->name('reports.index');
Route::post('/reports', 'ReportsController@store')->name('reports.store');
Route::patch('/reports/{id}', 'ReportsController@update')->name('reports.update');

Route::put('/reports/{id}/picture', 'ReportsPicturesController@update')->name('reports.picture.update');

Route::get('/users/{id}/reports', 'UsersReportsController@index')->name('users.reports.index');
Route::delete('/users/{id}/reports', 'UsersReportsController@destroy')->name('users.reports.destroy');