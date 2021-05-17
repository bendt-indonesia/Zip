<?php

Route::group([
    'namespace' => 'Bendt\Zip\Controllers\API',
    'middleware' => 'auth:api',
    'prefix' => 'api',
], function() {
    Route::get('province', 'ZipController@province');
    Route::get('city', 'ZipController@city');
    Route::get('kecamatan', 'ZipController@kecamatan');
    Route::get('kelurahan', 'ZipController@kelurahan');
    Route::get('province_city', 'ZipController@province_city');
    Route::post('check_zip', 'ZipController@check_zip');
});