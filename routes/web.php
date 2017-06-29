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

#--------------------------------------------------------------------------------------------------------
#
#
#                               CodeRoomService API
#
#
#--------------------------------------------------------------------------------------------------------
#                               General API
#--------------------------------------------------------------------------------------------------------
Route::post('login', 'UserAuthController@login');
Route::get('logout', 'UserAuthController@logout');

Route::post('register', 'UserAuthController@registerUser');
Route::get('register', 'UserAuthController@register');

Route::get('image/show/{id}', 'ImageController@show');

#--------------------------------------------------------------------------------------------------------

Route::group(['middleware' => 'userAuth', 'prefix' => 'api'], function (){

    Route::get('user/home', 'CourseController@index');

    Route::get('problem/{id}/question', 'FileController@question');


    Route::group(['middleware' => 'studentAuth', 'prefix' => 'student'], function (){

        # 1
        Route::get('dashboard', 'StudentController@dashboard');
        # 2
        Route::get('profile/{id}', 'StudentController@profile');
        # 3
        Route::post('profile/edit', 'StudentController@updateProfile');
        # 4
        Route::post('change_password', 'StudentController@changePassword');


        # 5
        Route::get('course/{id}/member', 'CourseController@member');
        # 6
        Route::post('course/join', 'CourseController@join');
        # 7
        Route::get('course/{student_id}/{course_id}', 'CourseController@showStudent');


        # 8
        Route::get('lesson/{id}', 'LessonController@show');
        # 9
        Route::get('announcement/{id}', 'AnnouncementController@show');


        # 10
        Route::get('submission/{problem_id}/{student_id}', 'SubmissionController@result');
        # 11
        Route::post('submission', 'SubmissionController@store2');


    });

    Route::group(['middleware' => 'teacherAuth', 'prefix' => 'teacher'], function (){

        # 12
        Route::get('dashboard', 'TeacherController@dashboard');
        # 13
        Route::get('profile/{id}', 'TeacherController@profile');
        # 14
        Route::post('profile/edit', 'TeacherController@updateProfile');
        # 15
        Route::post('change_password', 'TeacherController@changePassword');


        # 16
        Route::get('course/{course_id}', 'CourseController@showTeacher');
        # 17
        Route::get('course/{id}/member', 'CourseController@member');


        # 18
        Route::get('lesson/{id}', 'LessonController@show');
        # 19
        Route::post('lesson/edit', 'LessonController@update');
        # 20
        Route::post('lesson/store', 'LessonController@store');
        # 21
        Route::delete('lesson/delete/{id}', 'LessonController@delete');
        # 22
        Route::post('lesson/change_order', 'LessonController@changeOrder');


        # 23
        Route::get('problem/{id}', 'ProblemController@show');
        # 24 Todo Edit Problem File
        Route::post('problem/edit', 'ProblemController@update');
        # 25 Todo finish this
        Route::post('problem/store', 'ProblemController@store');
        # 26
        Route::post('problem/store_score', 'ProblemController@storeScore');
        # 27
        Route::delete('problem/delete/{id}', 'ProblemController@delete');
        # 28
        Route::post('problem/change_order', 'ProblemController@changeOrder');
        # 29
        Route::get('problem/{id}/submission', 'ProblemController@submission');


        # 30
        Route::get('submission/{id}/code', 'SubmissionController@code');


        # 31
        Route::get('announcement/{id}', 'AnnouncementController@show');
        # 32
        Route::post('announcement/edit', 'AnnouncementController@update');
        # 33 Todo Priority
        Route::post('announcement/store', 'AnnouncementController@store');
        # 34
        Route::delete('announcement/delete/{id}', 'AnnouncementController@delete');


        # 35
        Route::get('student/{id}', 'StudentController@profile');
        # 36
        Route::post('student/store', 'StudentController@addMember');
        # 37
        Route::post('students/store', 'StudentController@addMembers');
        # 38
        Route::get('student/disable/{student_id}/{course_id}', 'StudentController@disable');
        # 39 Todo Change this stupid name
        Route::get('student/all/{course_id}', 'StudentController@getAll');


        # 40
        Route::get('remove/ip/{id}', 'StudentController@removeIP');

    });

    Route::group(['middleware' => 'adminAuth', 'prefix' => 'admin'], function (){

        # 41
        Route::get('dashboard', 'AdminController@dashboard');


        # 42
        Route::post('course', 'CourseController@store');
        # 43
        Route::get('course/status/{course_id}', 'CourseController@changeStatus');
        # 44 Todo some problem here
        Route::post('course/add/teacher', 'CourseController@addTeacher');


        # 45
        Route::get('teacher', 'TeacherController@getAll');
        # 46 [New Api]
        Route::post('teacher', 'TeacherController@store');
        # 47
        Route::get('teacher/status/{teacher_id}', 'TeacherController@changeStatus');
        # 48
        Route::get('teacher/course/{course_id}', 'CourseController@teacherMember');

    });

});

//Route::get('test/student', 'TestController@testStudent');
Route::post('test', 'TestController@test');
Route::get('test2', 'TestController@test2');

//Deprecated api
Route::post('api/submission/code', 'SubmissionController@store');
Route::get('problem/getQuestion/{id}', 'FileController@question');
