<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('user/login', 'UserController@authenticate');
Route::post('user/register', 'UserController@register');

// Generate UUID
Route::get('generate/guestID', 'ToolsController@generateUUID');

Route::group(['middleware' => 'auth:api'], function () {
  // USER LOGOUT
  Route::post('user/logout', 'UserController@userLogout');

  /*
   * USERS
   */
  // GET AUTHENTICATED User
  Route::get('getUserLogin', 'UserController@getUserLogin');
  // GET ALL USERS
  Route::get('user/all', 'UserController@getAllUsers');
  // VIEW USER
  Route::get('user/{id}', 'UserController@getUser');
  // UPDATE USER
  Route::post('user/update', 'UserController@updateUser');

  /*
   * MEMBERS
   */
  // GET ALL MEMBERS
  Route::get('member/all', 'MasterlistController@getAllMembers');
  // VIEW MEMBER
  Route::get('member/{id}', 'MasterlistController@getMemberData');
  // UPDATE MEMBER
  Route::post('member/add', 'MasterlistController@addMember');
  Route::post('member/update', 'MasterlistController@updateMember');

  /*
   * ATTENDANCE
   */
  // GET ALL ATTENDANCE
  Route::get('attendance/all', 'AttendanceController@getAttendanceList');
  // VIEW ATTEDANCE
  Route::get('attendance/{id}', 'AttendanceController@getAttendeesList');
  Route::get('attendance/{id}/download-excel', 'AttendanceController@downloadAttendance');

  Route::post('attendance/add', 'AttendanceController@createAttendance');
  Route::post('attendance/update', 'AttendanceController@updateMember');
});
