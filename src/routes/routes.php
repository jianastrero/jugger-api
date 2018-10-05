<?php
Route::group(['namespace' => 'JianAstrero\JuggerAPI\Http\Controllers', 'middleware' => ['web']], function(){
    Route::get('/jugger-api', 'JuggerController@index');
    Route::get('/jugger-api/home', 'JuggerController@home');
    Route::get('/jugger-api/logout', 'JuggerController@logout');
});

Route::group(['namespace' => 'JianAstrero\JuggerAPI\Http\Controllers', 'middleware' => ['api'], 'prefix' => 'jugger-api'], function(){
    Route::post('/jugger-api/login', 'JuggerController@login');

    Route::group(['middleware' => 'auth:juggeradmin-api'], function(){
        Route::post('/jugger-api/logout', 'JuggerController@logoutApi');
    });
});

Route::group(['namespace' => 'JianAstrero\JuggerAPI\Http\Controllers\API', 'middleware' => ['api'], 'prefix' => 'api'], function(){
    Route::get('/{version}/{slug}', 'JuggerAPIController@getList');
    Route::get('/{version}/{slug}/{id}', 'JuggerAPIController@item');

    Route::post('/{version}/{slug}', 'JuggerAPIController@create');
    Route::post('/{version}/{slug}/{id}', 'JuggerAPIController@notAllowed');

    Route::put('/{version}/{slug}', 'JuggerAPIController@updateMultiple');
    Route::put('/{version}/{slug}/{id}', 'JuggerAPIController@update');

    Route::delete('/{version}/{slug}', 'JuggerAPIController@deleteMultiple');
    Route::delete('/{version}/{slug}/{id}', 'JuggerAPIController@delete');

    Route::get('{any}', 'JuggerAPIController@notFound')->where('any', '.*');
});