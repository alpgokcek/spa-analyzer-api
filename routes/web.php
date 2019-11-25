<?php

Route::get('/', function () {
  return view('welcome');
});
    Route::resource('business', 'BusinessController');

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
