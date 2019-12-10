<?php

Route::get('/', function () {
  return view('welcome');
});
    Route::resource('customer', 'CustomerController');

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
