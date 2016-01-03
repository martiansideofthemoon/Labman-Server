<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(array('before'=>'API' ,'after'=>'afterAPI') ,function (){
	Route::post('add_user', 'HomeController@add_user');
	Route::post('add_experiment', 'HomeController@add_experiment');
	Route::get('search_experiments', 'HomeController@search_experiments');
	Route::get('get_experiment', 'HomeController@get_experiment');
	Route::post('add_result', 'HomeController@add_result');
});
