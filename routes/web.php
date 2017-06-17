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

Route::get('api/user/home', 'CourseController@showCourseUser');

#--------------------------------------------------------------------------------------------------------

Route::group(['middleware' => 'userAuth', 'prefix' => 'api'], function (){

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

    });

    Route::group(['middleware' => 'teacherAuth', 'prefix' => 'teacher'], function (){

        # 11
        Route::get('dashboard', 'TeacherController@dashboard');
        # 12
        Route::get('profile/{id}', 'TeacherController@profile');
        # 13
        Route::post('profile/edit', 'TeacherController@updateProfile');
        # 14
        Route::post('change_password', 'TeacherController@changePassword');


        # 15
        Route::get('course/{course_id}', 'CourseController@showTeacher');
        # 16
        Route::get('course/{id}/member', 'CourseController@member');


        # 17
        Route::get('lesson/{id}', 'LessonController@show');
        # 18
        Route::post('lesson/edit', 'LessonController@update');
        # 19
        Route::post('lesson/store', 'LessonController@store');
        # 20
        Route::delete('lesson/delete/{id}', 'LessonController@delete');
        # 21
        Route::post('lesson/change_order', 'LessonController@changeOrder');


        # 22
        Route::get('problem/{id}', 'ProblemController@show');
        # 23 Todo Fix this
        Route::post('problem/edit', 'ProblemController@update');
        # 24 Todo Fix this
        Route::post('problem/store', 'ProblemController@storeProblem');
        # 25
        Route::post('problem/store_score', 'ProblemController@storeScore');
        # 26
        Route::delete('problem/delete/{id}', 'ProblemController@delete');
        # 27
        Route::post('problem/change_order', 'ProblemController@changeOrder');
        # 28
        Route::get('problem/{id}/submission', 'ProblemController@submission');


        # 29
        Route::get('submission/{id}/code', 'SubmissionController@code');


        # 30
        Route::get('announcement/{id}', 'AnnouncementController@show');
        # 31
        Route::post('announcement/edit', 'AnnouncementController@update');
        # 32 Todo Priority
        Route::post('announcement/store', 'AnnouncementController@store');
        # 33
        Route::delete('announcement/delete/{id}', 'AnnouncementController@delete');


        # 34
        Route::get('student/{id}', 'StudentController@profile');
        # 35
        Route::post('student/store', 'StudentController@addMember');
        # 36 Todo finish this
        Route::post('students/store', 'StudentController@addMembers');
        # 37
        Route::get('student/disable/{student_id}/{course_id}', 'StudentController@disable');
        # 38 Todo Change this stupid name
        Route::get('student/all/{course_id}', 'StudentController@getAll');


        # 39
        Route::get('remove/ip/{id}', 'StudentController@removeIP');

    });

    Route::group(['middleware' => 'adminAuth', 'prefix' => 'admin'], function (){

        # 40
        Route::get('dashboard', 'AdminController@dashboard');


        # 41 Todo Course Image
        Route::post('course', 'CourseController@store');
        # 42
        Route::get('course/status/{course_id}', 'CourseController@changeStatus');
        # 43 Todo some problem here
        Route::post('course/add/teacher', 'CourseController@addTeacher');


        # 44
        Route::get('teacher', 'TeacherController@getAll');
        # 45
        Route::get('teacher/status/{teacher_id}', 'TeacherController@changeStatus');
        # 46
        Route::get('teacher/course/{course_id}', 'CourseController@teacherMember');

    });

});

//Route::get('test/student', 'TestController@testStudent');
//Route::get('test', 'TestController@test');
