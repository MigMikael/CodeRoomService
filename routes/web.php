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

# 1
Route::post('login', 'UserAuthController@login');
# 2
Route::get('logout', 'UserAuthController@logout');


# 3
Route::post('register', 'UserAuthController@registerUser');
# 4
//Route::get('register', 'UserAuthController@register');

#--------------------------------------------------------------------------------------------------------

# 5
Route::get('image/show/{id}', 'ImageController@show');
# 6
Route::get('file/show/{id}', 'FileController@show');
# 8
Route::get('problem/{id}/question', 'FileController@question');

Route::group(['middleware' => 'userAuth', 'prefix' => 'api'], function (){

    # 7
    Route::get('user/home', 'CourseController@index');

    Route::group(['middleware' => 'studentAuth', 'prefix' => 'student'], function (){

        # 9
        Route::get('dashboard', 'StudentController@dashboard');
        # 10
        Route::get('profile/{id}', 'StudentController@profile');
        # 11 Todo Student can change Avatar Image
        Route::post('profile/edit', 'StudentController@updateProfile');
        # 12
        Route::post('change_password', 'StudentController@changePassword');


        # 13
        Route::get('course/{id}/member', 'CourseController@member');
        # 14
        Route::post('course/join', 'CourseController@join');
        # 15 Todo Test This API
        Route::get('course/{student_id}/{course_id}', 'CourseController@showStudent');


        # 16
        Route::get('lesson/{id}', 'LessonController@show');
        # 17
        Route::get('announcement/{id}', 'AnnouncementController@show');


        # 18
        Route::get('submission/{problem_id}/{student_id}', 'SubmissionController@result');


        Route::group(['middleware' => 'checkSubmit'], function (){
            # 19
            Route::post('submission', 'SubmissionController@store2');
        });

    });

    Route::group(['middleware' => 'teacherAuth', 'prefix' => 'teacher'], function (){

        # 20
        Route::get('dashboard', 'TeacherController@dashboard');
        # 21
        Route::get('profile/{id}', 'TeacherController@profile');
        # 22
        Route::post('profile/edit', 'TeacherController@updateProfile');
        # 23
        Route::post('change_password', 'TeacherController@changePassword');


        # 24
        Route::get('course/{course_id}', 'CourseController@showTeacher');
        # 25
        Route::get('course/mode/{course_id}', 'CourseController@changeMode');
        # 26
        Route::get('course/{id}/member', 'CourseController@member');


        # 27
        Route::get('lesson/{id}', 'LessonController@show');
        # 28 Todo Lesson Status
        Route::post('lesson/edit', 'LessonController@update');
        # 29 Todo Lesson Status
        Route::post('lesson/store', 'LessonController@store');
        # 30
        Route::delete('lesson/delete/{id}', 'LessonController@delete');
        # 31
        Route::post('lesson/change_order', 'LessonController@changeOrder');
        # [New API]
        Route::get('lesson/change_submit/{id}', 'LessonController@changeSubmit');
        # [New API]
        Route::get('lesson/change_status/{id}', 'LessonController@changeStatus');
        #
        Route::get('lesson/scoreboard/{id}', 'LessonController@scoreboard');
        # 32
        Route::get('lesson/export/score/{id}', 'LessonController@exportScore');


        # 33
        Route::get('problem/{id}', 'ProblemController@show');
        # 34 Todo Edit Problem File
        Route::post('problem/edit', 'ProblemController@update');
        # 35
        Route::post('problem/store', 'ProblemController@store');
        # 36
        Route::post('problem/store_score', 'ProblemController@storeScore');
        # 37
        Route::delete('problem/delete/{id}', 'ProblemController@delete');
        # 38
        Route::post('problem/change_order', 'ProblemController@changeOrder');
        # 39
        Route::get('problem/{id}/submission', 'ProblemController@submission');


        # [New API]
        Route::post('problem/resource/create', 'ResourceController@store');
        # [New API]
        Route::post('problem/resource/{id}/edit', 'ResourceController@update');
        # [New API]
        Route::get('problem/resource/{id}/delete', 'ResourceController@destroy');
        # [New API]
        Route::get('problem/resource/{id}/visible', 'ResourceController@changeStatus');


        # 40
        Route::get('submission/{id}/code', 'SubmissionController@code');


        # 41
        Route::get('announcement/{id}', 'AnnouncementController@show');
        # 42 Todo Can support priority
        Route::post('announcement/store', 'AnnouncementController@store');
        # 43 Todo Can support priority
        Route::post('announcement/edit', 'AnnouncementController@update');
        # 44
        Route::delete('announcement/delete/{id}', 'AnnouncementController@delete');


        # 45
        Route::get('student/{id}', 'StudentController@profile');
        # 46
        Route::post('student/store', 'StudentController@addMember');
        # 47
        Route::post('students/store', 'StudentController@addMembers');
        # 48
        Route::get('student/disable/{student_id}/{course_id}', 'StudentController@disable');
        # 49 Todo Change this stupid name
        Route::get('student/all/{course_id}', 'StudentController@getAll');


        # 50
        Route::get('remove/ip/{id}', 'StudentController@removeIP');
        # 51
        Route::get('remove/ip_all/{course_id}', 'StudentController@removeAllIP');

    });

    Route::group(['middleware' => 'adminAuth', 'prefix' => 'admin'], function (){

        # 52
        Route::get('dashboard', 'AdminController@dashboard');


        # 53
        Route::post('course', 'CourseController@store');
        # 54
        Route::get('course/status/{course_id}', 'CourseController@changeStatus');
        # 55
        Route::post('course/add/teacher', 'CourseController@addTeacher');


        # 56
        Route::get('teacher', 'TeacherController@getAll');
        # 57 New Api
        Route::post('teacher', 'TeacherController@store');
        # 58
        Route::get('teacher/status/{teacher_id}', 'TeacherController@changeStatus');
        # 59
        Route::get('teacher/course/{course_id}', 'CourseController@teacherMember');

    });

});

//Route::get('test/student', 'TestController@testStudent');
//Route::post('test', 'TestController@test');
//Route::get('test2', 'TestController@test2');
Route::get('test3', 'TestController@test3');
Route::get('test4/{id}', 'LessonController@scoreboard');
Route::get('test5/{id}', 'LessonController@exportScore2');

//Deprecated api
//Route::post('api/submission/code', 'SubmissionController@store');
//Route::get('problem/getQuestion/{id}', 'FileController@question');
