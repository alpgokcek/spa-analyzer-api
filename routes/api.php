<?php
use Illuminate\Http\Request;
/* Route::apiResources([
    'user' => 'Api\UserController'
]); */

/*
    apiToken middlewarei user tablosunda api_token sütununu sorguluyor.
    dışardan üye olunabileceği için user'ın tamamını kontrole sokmuyoruz.
    şifre ihtiyacı duyan tüm ekranlarda api_token kontrolü sağlanacak!
        *** not *** if kullanmadan dene, sorguyu zaten middleware yapacak ?!
        *** not *** yukarıdaki user parametrelerini çalıştır.
*/

Route::post('/auth/login', 'Api\AuthController@login');

Route::middleware('api-token')->group(function() {
    Route::apiResource('/business', 'Api\BusinessController');
    Route::apiResource('/balance', 'Api\BalanceController');

    Route::get('/user', function(Request $request) {
        $user = $request->user();

        return response()->json([
            'name' => $user->name,
            'access_token' => $user->api_token,
            'time' => time()
        ]);
    });
});
