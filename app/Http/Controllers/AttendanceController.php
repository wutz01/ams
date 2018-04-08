<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Attendance;
use App\AttendanceList;
use Validator, Excel;

class AttendanceController extends Controller
{
    public function createAttendance (Request $request) {
      $rules = [
        'batch'           => 'required',
        'time'            => 'required',
        'date'            => 'required',
        'worker_assign'   => 'required',
        'officer_assign'  => 'required'
      ];
      $validator = Validator::make($request->all(), $rules);

      if ($validator->fails()) {
        $json['errors'] = $validator->messages();
        return response()->json($json, 400);
      }

      $att = new Attendance;
      $att->batch          = $request->input('batch');
      $att->time           = $request->input('time');
      $att->date           = $request->input('date');
      $att->worker_assign  = $request->input('worker_assign');
      $att->officer_assign = $request->input('officer_assign');
      $att->save();

      $json['attendance'] = $att;
      return response()->json($json, 200);
    }

    public function getAttendanceList () {
      $attendance = Attendance::all();
      $json['attendance'] = $attendance;
      return response()->json($json, 200);
    }

    public function getAttendeesList ($attendanceId) {
      $attendance = Attendance::find($attendanceId);
      $attendees = [];
      if ($attendance) {
        $attendees = $attendance->attendees;
        $count = $attendees->count();
        $json['count'] = $count;
        $json['attendees'] = $attendees;
      }
      $json['attendance'] = $attendance;

      return response()->json($json, 200);
    }

    public function migrateOldData() {
      $list = AttendanceList::all();
      foreach($list as $key => $value) {
        $a = Attendance::find($value->attendance_id);
        if (!$a->attendees->contains($value->brethren_id)) {
          $a->attendees()->attach($value->brethren_id);
        }
      }
    }

    public function downloadAttendance ($attendanceId) {
      $data = Attendance::find($attendanceId);
      if ($data) {
        $filename = $data->batch.'-'.$data->date.$data->time;
        $destinationPath = storage_path('excel/exports');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        Excel::create($filename, function($excel) use($data) {
          $excel->sheet('Attendees', function($sheet) use($data) {
              $sheet->fromModel($data->attendees()->select(['churchId', 'lastname', 'firstname', 'middlename'])->get());
          });
        })->store('xls', $destinationPath);
        return response()->download($destinationPath, $filename);
      }
      return response()->json(['error' => 'Failed to generate excel'], 400);
    }
}
