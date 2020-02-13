<?php
use Illuminate\Http\Request;

/*
    şifresiz erişilebilecek standart ekranların tamamı burada
Route::apiResources([
    'user' => 'Api\UserController'
]);
*/


Route::post('/uplink', 'Api\UplinkController@store');
Route::post('/auth/login', 'Api\AuthController@login');
Route::post('/auth/register', 'Api\AuthController@register');

Route::middleware('api-token')->group(function() {
/*
    apiToken middlewarei user tablosunda api_token sütununu sorguluyor.
    şifre kontrolü yapılacak ekranları buraya yerleştiriyoruz.
*/


    Route::apiResource('/university', 'Api\UniversityController');
    Route::apiResource('/faculty', 'Api\FacultyController');
    Route::apiResource('/department', 'Api\DepartmentController');
    Route::apiResource('/course', 'Api\CourseController');
    Route::apiResource('/section', 'Api\SectionController');
    Route::apiResource('/user', 'Api\UserController');
    Route::apiResource('/users-admin', 'Api\UsersAdminController');
    Route::apiResource('/users-student', 'Api\UsersStudentController');
    Route::apiResource('/users-instructor', 'Api\UsersInstructorController');
    Route::apiResource('/authority', 'Api\AuthorityController');
    Route::apiResource('/dashboard', 'Api\DashboardController');
    Route::apiResource('/log', 'Api\LogController');
});
