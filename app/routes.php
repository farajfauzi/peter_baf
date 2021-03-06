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

Route::get('/', function()
{
	return View::make('hello');
});

Route::api('v1', function () {
	Route::group(['prefix' => 'api', 'protected' => true], function ()
	{
		Route::resource('notes', 'NotesController');
		Route::resource('reports', 'ReportsController');

		Route::post('login', ['uses' => 'PetugasController@login', 'protected' => false]);
	});

});