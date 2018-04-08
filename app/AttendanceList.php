<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendanceList extends Model
{
    protected $table = 'attendance_list';
    protected $hidden = ['created_at', 'updated_at'];
}
