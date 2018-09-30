<?php
Route::group(['namespace' => 'JianAstrero\JuggerAPI\Http\Controllers', 'middleware' => ['web']], function(){
    Route::get('/jugger-api', 'JuggerController@index');
    Route::get('/jugger-api/home', 'JuggerController@home');
    Route::get('/jugger-api/logout', 'JuggerController@logout');
});

Route::group(['middleware' => ['api'], 'prefix' => 'api'], function(){
    Route::post('/jugger-api/login', 'JianAstrero\JuggerAPI\Http\Controllers\JuggerController@login');

    Route::group(['middleware' => 'auth:api'], function(){
        Route::post('/jugger-api/logout', 'JianAstrero\JuggerAPI\Http\Controllers\JuggerController@logoutApi');
    });

    Route::group(['namespace' => 'JianAstrero\JuggerAPI\Http\Controllers\API', 'middleware' => 'auth:api'], function(){
        Route::get('/{slug}', 'JuggerAPIController@getList');
        Route::get('/{slug}/{id}', 'JuggerAPIController@item');

        Route::post('/{slug}', 'JuggerAPIController@create');
        Route::post('/{slug}/{id}', 'JuggerAPIController@notAllowed');

        Route::put('/{slug}', 'JuggerAPIController@updateMultiple');
        Route::put('/{slug}/{id}', 'JuggerAPIController@update');

        Route::delete('/{slug}', 'JuggerAPIController@deleteMultiple');
        Route::delete('/{slug}/{id}', 'JuggerAPIController@delete');

        Route::get('{any}', 'JuggerAPIController@notFound')->where('any', '.*');
    });
});