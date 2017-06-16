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
#                               Student API
#--------------------------------------------------------------------------------------------------------

Route::group(['middleware' => ['userAuth', 'studentAuth']], function (){

    Route::get('api/student/dashboard', 'StudentController@dashboard');

});

Route::get('test/student', 'TestController@testStudent');

Route::get('test', 'TestController@test');
