<?php

Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
    Route::post('register', 'AuthController@register')->name('auth.register');
    Route::post('login', 'AuthController@login')->name('auth.login');
    Route::post('logout', 'AuthController@logout')->name('auth.logout');
    Route::get('attempt', 'AuthController@attempt')->name('auth.attempt');
});


Route::get('/reports', 'ReportsController@index')->name('reports.index');
Route::post('/reports', 'ReportsController@store')->name('reports.store');
Route::patch('/reports/{id}', 'ReportsController@update')->name('reports.update');

Route::put('/reports/{id}/picture', 'ReportsPicturesController@update')->name('reports.picture.update');

Route::get('/users/{id}/reports', 'UsersReportsController@index')->name('users.reports.index');
Route::delete('/users/{id}/reports', 'UsersReportsController@destroy')->name('users.reports.destroy');

Route::post('/reports/{id}/confirm', 'ReportConfirmationController@store')->name('reports.confirmations.store');
Route::delete('/reports/{id}/confirm', 'ReportConfirmationController@destroy')->name('reports.confirmations.destroy');

Route::post('/reports/{id}/fix', 'ReportFixController@store')->name('reports.fix.store');
Route::delete('/reports/{id}/fix', 'ReportFixController@destroy')->name('reports.fix.destroy');

Route::get('/stats/reports/total', 'StatController@total')->name('stats.reports.total');

Route::get('/filter/reports', 'FilterController@waiting')->name('filter.reports.waiting');
Route::get('/filter/reports/confirmed', 'FilterController@confirmed')->name('filter.reports.confirmed');
Route::get('/filter/reports/fixed', 'FilterController@fixed')->name('filter.reports.fixed');