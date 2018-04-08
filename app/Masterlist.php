<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Masterlist extends Model
{
    protected $table = 'masterlist';
    protected $hidden = ['fingerPrint', 'created_at', 'updated_at'];

    public function image () {
      return $this->hasOne('App\UserImage', 'userId');
    }

    public function services () {
      return $this->hasMany('App\AttendanceList', 'brethren_id');
    }

    /**
     * The member belongs to many attendance.
     */
    public function attendees()
    {
        return $this->belongsToMany('App\Attendance', 'attendance_masterlist', 'brethrenId', 'attendanceId');
    }
}
