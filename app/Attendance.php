<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
    protected $hidden = ['created_at', 'updated_at'];

    public function members () {
      return $this->hasMany('App\AttendanceList', 'attendance_id');
    }

    /**
     * The attendance belongs many members.
     */
    public function attendees()
    {
        return $this->belongsToMany('App\Masterlist', 'attendance_masterlist', 'attendanceId', 'brethrenId');
    }
}
