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
    Route::apiResource('/company', 'Api\CompanyController');
    Route::apiResource('/website', 'Api\WebsiteController');
    Route::apiResource('/customer', 'Api\CustomerController');
    Route::apiResource('/balance', 'Api\BalanceController');
    Route::apiResource('/user', 'Api\UserController');
    Route::apiResource('/canvas', 'Api\CanvasController');
    Route::apiResource('/canvastype', 'Api\CanvasTypeController');
    Route::apiResource('/canvassetting', 'Api\CanvasSettingController');
    Route::apiResource('/canvassettingjson', 'Api\CanvasSettingJsonController');
    Route::apiResource('/content', 'Api\ContentController');
    Route::apiResource('/product', 'Api\ProductController');
    Route::apiResource('/pins', 'Api\PinsController');
    Route::apiResource('/pincode', 'Api\PinCodeController');
    Route::apiResource('/keyword', 'Api\KeywordController');
    Route::apiResource('/authority', 'Api\AuthorityController');
    Route::apiResource('/webauthority', 'Api\WebAuthorityController');
    Route::apiResource('/bank', 'Api\BankController');
    Route::post('/upload', 'Api\UploadController@upload');
    Route::post('/storage', 'Api\UploadController@storage');
    Route::post('/s3', 'Api\UploadController@s3');
    Route::post('/removeS3', 'Api\UploadController@removeS3');
});
