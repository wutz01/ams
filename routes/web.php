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

use App\User;

Route::get('/', function () {
    return view('welcome');
});

Route::get('test', function () {
  return User::find(1);
});

Route::get('seed-members', 'MasterlistController@seedMembers');
Route::get('seed-attendance', 'MasterlistController@seedAttendance');
Route::get('seed-attendance-list', 'MasterlistController@seedAttendanceList');
// Route::get('migrate', 'AttendanceController@migrateOldData');
