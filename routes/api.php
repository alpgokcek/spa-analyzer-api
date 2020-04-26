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

    Route::apiResource('/authority', 'Api\AuthorityController');
    Route::apiResource('/course', 'Api\CourseController');
    Route::apiResource('/course-outcome', 'Api\CourseOutcomeController');
    Route::apiResource('/assessment', 'Api\AssessmentController');
    Route::apiResource('/dashboard', 'Api\DashboardController');
    Route::apiResource('/department', 'Api\DepartmentController');
    Route::apiResource('/departments-has-instructors', 'Api\DepartmentsHasInstructorsController');
    Route::apiResource('/faculty', 'Api\FacultyController');
    Route::apiResource('/grading-tool', 'Api\GradingToolController');
    Route::apiResource('/gtcco', 'Api\GradingToolCoversCourseOutcomeController');
    Route::apiResource('/igs', 'Api\InstructorsGivesSectionsController');
    Route::apiResource('/log', 'Api\LogController');
    Route::apiResource('/program-outcome', 'Api\ProgramOutcomeController');
    Route::apiResource('/popco', 'Api\ProgramOutcomesProvidesCourseOutcomesController');
    Route::apiResource('/section', 'Api\SectionController');
    Route::apiResource('/sagt', 'Api\StudentAnswersGradingToolController');
    Route::apiResource('/sgmgco', 'Api\StudentGetsMeasuredGradeCourseOutcomeController');
    Route::apiResource('/sgmgpo', 'Api\StudentGetsMeasuredGradeProgramOutcomeController');
    Route::apiResource('/sts', 'Api\StudentsTakesSectionsController');
    Route::apiResource('/university', 'Api\UniversityController');
    Route::apiResource('/user', 'Api\UserController');
    Route::apiResource('/users-admin', 'Api\UsersAdminController');
    Route::apiResource('/users-student', 'Api\UsersStudentController');
    Route::apiResource('/users-instructor', 'Api\UsersInstructorController');

    Route::post('/excelUpload', 'Api\UploadController@uploadExcel');
    Route::post('/sts/uploadedFile', 'Api\StudentsTakesSectionsController@uploadedFile');
    Route::post('/users-student/uploadedFile', 'Api\UsersStudentController@uploadedFile');
    Route::post('/users-instructor/uploadedFile', 'Api\UsersInstructorController@uploadedFile');
    Route::post('/section/uploadedFile', 'Api\SectionController@uploadedFile');
    Route::post('/igs/uploadedFile', 'Api\InstructorsGivesSectionsController@uploadedFile');
    Route::post('/course/uploadedFile', 'Api\CourseController@uploadedFile');
    Route::post('/program-outcome/uploadedFile', 'Api\ProgramOutcomeController@uploadedFile');

});
