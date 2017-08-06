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
Route::get('file/show/{id}', 'FileController@show');
#
Route::get('problem/{id}/question', 'FileController@question');

Route::group(['middleware' => 'userAuth', 'prefix' => 'api'], function (){

    #
    Route::get('user/home', 'CourseController@index');

    Route::group(['middleware' => 'studentAuth', 'prefix' => 'student'], function (){

        #
        Route::get('dashboard', 'StudentController@dashboard');
        #
        Route::get('profile/{id}', 'StudentController@profile');
        # Todo Student can change Avatar Image
        Route::post('profile/edit', 'StudentController@updateProfile');
        #
        Route::post('change_password', 'StudentController@changePassword');


        #
        Route::get('course/{id}/member', 'CourseController@member');
        #
        Route::post('course/join', 'CourseController@join');
        # Todo Test This API
        Route::get('course/{student_id}/{course_id}', 'CourseController@showStudent');


        #
        Route::get('lesson/{id}', 'LessonController@show');
        #
        Route::get('announcement/{id}', 'AnnouncementController@show');


        #
        Route::get('submission/{problem_id}/{student_id}', 'SubmissionController@result');


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
        Route::get('course/{course_id}', 'CourseController@showTeacher');
        #
        Route::get('course/mode/{course_id}', 'CourseController@changeMode');
        #
        Route::get('course/{id}/member', 'CourseController@member');
        # New API
        Route::post('course/clone', 'CourseController@cloneCourse');


        #
        Route::get('lesson/{id}', 'LessonController@show');
        # Todo Lesson Status
        Route::post('lesson/edit', 'LessonController@update');
        # Todo Lesson Status
        Route::post('lesson/store', 'LessonController@store');
        #
        Route::delete('lesson/delete/{id}', 'LessonController@delete');
        #
        Route::post('lesson/change_order', 'LessonController@changeOrder');
        # New API
        Route::get('lesson/change_submit/{id}', 'LessonController@changeSubmit');
        # New API
        Route::get('lesson/change_status/{id}', 'LessonController@changeStatus');
        #
        Route::get('lesson/scoreboard/{id}', 'LessonController@scoreboard');
        #
        Route::get('lesson/export/score/{id}', 'LessonController@exportScore');


        #
        Route::get('problem/{id}', 'ProblemController@show');
        # Todo Edit Problem File
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
        Route::get('problem/{id}/submission', 'ProblemController@submission');


        # New API
        Route::post('problem/resource/create', 'ResourceController@store');
        # New API
        Route::post('problem/resource/{id}/edit', 'ResourceController@update');
        # New API
        Route::get('problem/resource/{id}/delete', 'ResourceController@destroy');
        # New API
        Route::get('problem/resource/{id}/visible', 'ResourceController@changeStatus');


        #
        Route::get('submission/{id}/code', 'SubmissionController@code');


        #
        Route::get('announcement/{id}', 'AnnouncementController@show');
        # Todo Can support priority
        Route::post('announcement/store', 'AnnouncementController@store');
        # Todo Can support priority
        Route::post('announcement/edit', 'AnnouncementController@update');
        #
        Route::delete('announcement/delete/{id}', 'AnnouncementController@delete');


        #
        Route::get('student/{id}', 'StudentController@profile');
        #
        Route::post('student/store', 'StudentController@addMember');
        #
        Route::post('students/store', 'StudentController@addMembers');
        #
        Route::get('student/disable/{student_id}/{course_id}', 'StudentController@disable');
        #
        Route::get('student/all/{course_id}', 'StudentController@getAll');
        # New API
        Route::get('student/submission/{id}/code', 'StudentController@submissionCode');


        #
        Route::get('remove/ip/{id}', 'StudentController@removeIP');
        #
        Route::get('remove/ip_all/{course_id}', 'StudentController@removeAllIP');

    });

    Route::group(['middleware' => 'adminAuth', 'prefix' => 'admin'], function (){

        #
        Route::get('dashboard', 'AdminController@dashboard');


        #
        Route::post('course', 'CourseController@store');
        #
        Route::get('course/status/{course_id}', 'CourseController@changeStatus');
        #
        Route::post('course/add/teacher', 'CourseController@addTeacher');


        #
        Route::get('teacher', 'TeacherController@getAll');
        # New Api
        Route::post('teacher', 'TeacherController@store');
        #
        Route::get('teacher/status/{teacher_id}', 'TeacherController@changeStatus');
        #
        Route::get('teacher/course/{course_id}', 'CourseController@teacherMember');

    });

});

//Route::get('test/student', 'TestController@testStudent');
//Route::post('test', 'TestController@test');
//Route::get('test2', 'TestController@test2');
Route::get('test3', 'TestController@test3');
Route::get('test4', 'TestController@test4');
Route::get('test4/{id}', 'LessonController@scoreboard');
Route::get('export_score/{id}', 'LessonController@exportScore');
Route::post('test5', 'TestController@test5');
Route::get('test6', 'StudentController@submissionCode');

//Deprecated api
//Route::post('api/submission/code', 'SubmissionController@store');
//Route::get('problem/getQuestion/{id}', 'FileController@question');
