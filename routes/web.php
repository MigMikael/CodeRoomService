<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/document', function () {
    return view('doc');
});

#--------------------------------------------------------------------------------------------------------
#
#
#                               CodeRoomService API
#
#
#--------------------------------------------------------------------------------------------------------
#                               General API
#--------------------------------------------------------------------------------------------------------

#
Route::post('login', 'UserAuthController@login');
#
Route::get('logout', 'UserAuthController@logout');


#
Route::post('register', 'UserAuthController@registerUser');

#--------------------------------------------------------------------------------------------------------

#
Route::get('image/show/{id}', 'ImageController@show');
#
Route::get('file/show/{id}', 'FileController@showResource');
#
Route::get('problem/{id}/question', 'FileController@question');

Route::group(['middleware' => 'userAuth', 'prefix' => 'api'], function (){

    #
    Route::get('user/home', 'CourseController@index');

    Route::group(['middleware' => 'studentAuth', 'prefix' => 'student'], function (){

        #
        Route::get('dashboard', 'StudentController@dashboard');
        #
        Route::get('profile/{student_id}', 'StudentController@profile');
        #
        Route::post('profile/edit', 'StudentController@updateProfile');
        #
        Route::post('change_password', 'StudentController@changePassword');


        #
        Route::post('course/join', 'CourseController@join');

        Route::group(['middleware' => ['checkBanned', 'checkAccess']], function (){
            #
            Route::get('course/{id}/member', 'CourseController@member');
            #
            Route::get('course/{student_id}/{id}', 'CourseController@showStudent');
            #
            Route::get('course/{id}/lesson/progress', 'CourseController@sumProgress');
            #
            Route::get('lesson/{lesson_id}/{student_id}', 'LessonController@showStudent');
            #
            Route::get('announcement/{announce_id}', 'AnnouncementController@show');
            #
            Route::get('submission/{problem_id}/{student_id}', 'SubmissionController@result');
        });

        Route::group(['middleware' => 'checkSubmit'], function (){
            #
            Route::post('submission', 'SubmissionController@store2');
        });

    });

    Route::group(['middleware' => 'teacherAuth', 'prefix' => 'teacher'], function (){

        #
        Route::get('dashboard', 'TeacherController@dashboard');
        #
        Route::get('profile/{id}', 'TeacherController@profile');
        #
        Route::post('profile/edit', 'TeacherController@updateProfile');
        #
        Route::post('change_password', 'TeacherController@changePassword');


        #
        Route::get('course/{id}', 'CourseController@showTeacher');
        #
        Route::get('course/{id}/mode', 'CourseController@changeMode');
        #
        Route::get('course/{id}/status', 'CourseController@changeStatus');
        #
        Route::get('course/{id}/member', 'CourseController@member');
        #
        Route::get('course/{id}/detail/progress', 'CourseController@progressDetail');
        #
        Route::get('course/{lesson_id}/detail/summary', 'CourseController@summaryDetail');
        #
        Route::get('course/{lesson_id}/{problem_id}/detail', 'CourseController@problemDetail');
        #
        Route::get('course/{id}/lesson/progress', 'CourseController@sumProgress');
        #
        Route::get('course/token/reset/{id}', 'CourseController@resetToken');


        #
        Route::get('lesson/{id}', 'LessonController@showTeacher');
        #
        Route::post('lesson/edit', 'LessonController@update');
        #
        Route::post('lesson/store', 'LessonController@store');
        #
        Route::delete('lesson/delete/{id}', 'LessonController@delete');
        #
        Route::post('lesson/change_order', 'LessonController@changeOrder');
        #
        Route::get('lesson/change_submit/{id}', 'LessonController@changeSubmit');
        #
        Route::get('lesson/change_status/{id}', 'LessonController@changeStatus');
        #
        Route::get('lesson/scoreboard/{id}', 'LessonController@scoreboard');
        #
        Route::get('lesson/export/score/{id}', 'LessonController@exportScore');
        #
        Route::get('lesson/{id}/export/by_problem/{problem_id}', 'LessonController@exportByProblem');
        #
        Route::get('lesson/{id}/export/by_student/{student_id}', 'LessonController@exportByStudent');
        #
        Route::get('lesson/{id}/guide/change_status', 'LessonController@changeGuide');
        #
        Route::get('lesson/{id}/resource/visible/{status}', 'ResourceController@changeVisible');


        #
        Route::get('problem/{id}', 'ProblemController@show');
        #
        Route::post('problem/edit', 'ProblemController@update');
        #
        Route::post('problem/store', 'ProblemController@store');
        #
        Route::post('problem/store_score', 'ProblemController@storeScore');
        #
        Route::delete('problem/delete/{id}', 'ProblemController@delete');
        #
        Route::post('problem/change_order', 'ProblemController@changeOrder');
        #
        Route::get('problem/{id}/status', 'ProblemController@changeStatus');
        #
        Route::get('problem/{id}/submission', 'ProblemController@submission');


        #
        Route::post('problem/in_sol/store', 'ProblemController@storeInputAndOutput');
        #
        Route::post('problem/input/edit', 'ProblemController@updateInput');
        #
        Route::post('problem/output/edit', 'ProblemController@updateOutput');
        #
        Route::delete('problem/input/{id}/delete', 'ProblemController@destroyInput');
        #
        Route::delete('problem/output/{id}/delete', 'ProblemController@destroyOutput');
        #
        Route::delete('problem/delete_input/{problem_id}', 'ProblemController@destroyAllInput');
        #
        Route::delete('problem/delete_output/{problem_id}', 'ProblemController@destroyAllOutput');


        #
        Route::post('problem/resource/store', 'ResourceController@store');
        #
        Route::post('problem/resource/edit', 'ResourceController@update');
        #
        Route::delete('problem/resource/{id}/delete', 'ResourceController@destroy');
        #
        Route::get('problem/resource/{id}/visible', 'ResourceController@changeStatus');


        #
        Route::post('problem/driver/store', 'ProblemCOntroller@storeDriver');
        #
        Route::post('problem/driver/edit', 'ProblemController@updateDriver');
        #
        Route::delete('problem/driver/{id}/delete', 'ProblemController@deleteDriver');


        #
        Route::get('submission/{id}/code', 'SubmissionController@code');


        #
        Route::get('announcement/{id}', 'AnnouncementController@show');
        #
        Route::post('announcement/store', 'AnnouncementController@store');
        #
        Route::post('announcement/edit', 'AnnouncementController@update');
        #
        Route::delete('announcement/delete/{id}', 'AnnouncementController@delete');
        #
        Route::get('announcement/{id}/status', 'AnnouncementController@changeStatus');


        #
        Route::get('student/{id}', 'StudentController@profile');
        #
        Route::post('student/store', 'StudentController@addMember');
        #
        Route::post('students/store', 'StudentController@addMembers');
        #
        Route::get('student/change_status/{student_id}/{course_id}', 'StudentController@disable');
        #
        Route::get('student/all/{course_id}', 'StudentController@getAll');
        #
        Route::get('student/submission/{id}/code', 'StudentController@submissionCode');


        #
        Route::post('submission', 'SubmissionController@store2');


        #
        Route::get('remove/ip/{id}', 'StudentController@removeIP');
        #
        Route::get('remove/ip_all/{course_id}', 'StudentController@removeAllIP');

    });

    Route::group(['middleware' => 'adminAuth', 'prefix' => 'admin'], function (){

        #
        Route::get('course', 'CourseController@getAll');
        #
        Route::get('course/{id}', 'CourseController@showAdmin');
        #
        Route::post('course/store', 'CourseController@store');
        #
        Route::post('course/edit', 'CourseController@update');
        #
        Route::get('course/status/{id}', 'CourseController@changeStatus');
        #
        Route::post('course/add/teacher', 'CourseController@addTeacher');
        #
        Route::post('course/clone', 'CourseController@cloneCourse');
        #
        Route::delete('course/{id}/delete', 'CourseController@destroy');


        #
        Route::get('teacher', 'TeacherController@getAll');
        #
        Route::get('teacher/{id}', 'TeacherController@showAdmin');
        #
        Route::post('teacher/store', 'TeacherController@store');
        #
        Route::post('teacher/edit', 'TeacherController@update');
        #
        Route::get('teacher/status/{teacher_id}', 'TeacherController@changeStatus');
        #
        Route::get('teacher/course/{course_id}', 'CourseController@teacherMember');

    });

});

//Route::get('test/summary/{id}', 'CourseController@summaryDetail');
//Route::get('test/progress/{id}', 'CourseController@progressDetail');
//Route::get('test/{id}/{problem_id}', 'LessonController@exportByProblem');
//Route::get('test4/{id}', 'LessonController@scoreboard');
//Route::get('export_score/{id}', 'LessonController@exportScore');
//Route::get('test/strpos', 'TestController@teststrpos');

