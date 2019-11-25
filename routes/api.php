<?php
use Illuminate\Http\Request;

/*
    şifresiz erişilebilecek standart ekranların tamamı burada
Route::apiResources([
    'user' => 'Api\UserController'
]);
*/


Route::post('/auth/login', 'Api\AuthController@login');
Route::post('/auth/register', 'Api\AuthController@register');

Route::middleware('api-token')->group(function() {
/*
    apiToken middlewarei user tablosunda api_token sütununu sorguluyor.
    şifre kontrolü yapılacak ekranları buraya yerleştiriyoruz.
*/
    Route::apiResource('/business', 'Api\BusinessController');
    Route::apiResource('/balance', 'Api\BalanceController');
    Route::apiResource('/user', 'Api\UserController');
});
