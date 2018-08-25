<?php

namespace App\Http\Controllers;
use App\Masterlist;
use App\Attendance;
use App\AttendanceList;
use App\UserImage;
use Validator, Excel;

use Illuminate\Http\Request;

class MasterlistController extends Controller
{
  public function addMember (Request $request) {
    $message = [
      'email.required' => 'Email Address is required.',
      'required' => 'The :attribute field is required.'
    ];

    $client = $this->getMember($request->input('memberType'));
    $rules = [
        'churchId'         => 'required',
        'firstname'        => 'required',
        'lastname'         => 'required',
        'email'		         => 'email|required|unique:masterlist,email',
        'contactNumber'    => 'required',
        'userImage'        => 'image',
        'memberType'       => 'required',
        'status'           => 'required'
    ];

    $validator = Validator::make($request->all(), $rules, $message);

    if ($validator->fails()) {
        $json['errors'] = $validator->messages();
        return response()->json($json, 400);
    }

    $member = new Masterlist;
    $member->churchId      = $request->input('churchId');
    $member->firstname     = $request->input('firstname');
    $member->middlename    = $request->input('middlename') != null ? $request->input('middlename') : '';
    $member->lastname      = $request->input('lastname');
    $member->email         = $request->input('email');
    $member->lokalOrigin   = $request->input('lokalOrigin');
    $member->birthday      = $request->input('birthday');
    $member->sabbathDay    = $request->input('sabbathDay');
    $member->contactNumber = $request->input('contactNumber');
    $member->address       = $request->input('address');
    $member->status        = $request->input('status');
    $member->isOfficer     = $request->input('isOfficer') ? 1 : 0;
    $member->memberType    = $client;
    $member->save();

    $data = $request->all();
    if($image = array_pull($data, 'userImage')){
     $destinationPath = 'uploads/member/'.$member->id.'/';
     if (!file_exists(public_path($destinationPath))) {
         mkdir(public_path($destinationPath), 0777, true);
     }

     if($image->isValid()){
       $ext        = $image->getClientOriginalExtension();
       $filename   = $image->getFilename();
       $orig_name  = $image->getClientOriginalName();

       $for_upload = $filename . "." . $ext;
       $is_success = $image->move(public_path($destinationPath), $for_upload);

       if($is_success){
         $fileImage = new UserImage;
         $fileImage->userId = $member->id;
         $fileImage->imagePath = url('/') . "/" . $destinationPath;
         $fileImage->filename = $filename;
         $fileImage->origFilename = $orig_name;
         $fileImage->extension = $ext;
         $fileImage->save();
       }
     }
    }

    $json['member'] = $member;

    if ($member->image) {
      $json['imageUrl'] = $user->image->imagePath . $user->id . '/' . $user->image->filename . '.' . $user->image->extension;
    } else {
      $json['imageUrl'] = null;
    }
    return response()->json($json, 200);
  }

  public function getMember($client) {
    $client = strtoupper($client);
    switch ($client) {
      case 'WORKER': $client = 'WORKER'; break;
      case 'MEMBER': $client = 'MEMBER'; break;
      default: $client = 'MEMBER'; break;
    }

    return $client;
  }

  public function membersDataFormat ($members) {
    $member = [];
    foreach ($members as $key => $value) {
      $member[] = [
        'id'          => $value->id,
        'churchId'     => $value->churchId,
        'name'        => rtrim($value->lastname . ", " . $value->firstname . " " . $value->middlename, ' '),
        'firstname'   => $value->firstname,
        'lastname'    => $value->lastname,
        'middlename'  => $value->middlename,
        'email'       => $value->email,
        'lokalOrigin' => $value->lokalOrigin,
        'birthday'    => $value->birthday,
        'sabbathDay'  => $value->sabbathDay,
        'contactNumber' => $value->contactNumber,
        'address'     => $value->address,
        'status'      => $value->status,
        'isOfficer'   => $value->isOfficer,
        'memberType'  => $value->memberType
      ];
    }

    return $member;
  }

  public function getAllMembers (Request $request) {
    if ($request->has('memberType')) {
      $members = Masterlist::where('memberType', strtoupper($request->input('memberType')))->get();
      $json['members'] = $this->membersDataFormat($members);
      return response()->json($json, 200);
    }
    $members = Masterlist::all();
    $json['members'] = $this->membersDataFormat($members);
    return response()->json($json, 200);
  }

  public function getMemberData ($id) {
    $json['member'] = $member = Masterlist::find($id);
    if ($member->image) {
      $json['imageUrl'] = $member->image->imagePath . $member->id . '/' . $member->image->filename . '.' . $member->image->extension;
    } else {
      $json['imageUrl'] = null;
    }
    return response()->json($json, 200);
  }

  public function updateMember (Request $request) {
    $message = [
      'email.required' => 'Email Address is required.',
      'required' => 'The :attribute field is required.'
    ];
    $userId = $request->input('userId');

    $client = $this->getMember($request->input('memberType'));

    $rules = [
      'churchId'         => 'required',
      'firstname'        => 'required',
      'lastname'         => 'required',
      'email'		         => 'email|required|unique:masterlist,email,'.$userId,
      'contactNumber'    => 'required',
      'userImage'        => 'image',
      'memberType'       => 'required',
      'status'           => 'required'
    ];

    $validator = Validator::make($request->all(), $rules, $message);

    if (!isset($userId)) {
      $json['error'] = 'Member not found';
      return response()->json($json, 400);
    }

    if ($validator->fails()) {
        $json['errors'] = $validator->messages();
        return response()->json($json, 400);
    }

    $member = Masterlist::find($userId);
    $member->churchId      = $request->input('churchId');
    $member->firstname     = $request->input('firstname');
    $member->middlename    = $request->input('middlename');
    $member->lastname      = $request->input('lastname');
    $member->email         = $request->input('email');
    $member->lokalOrigin   = $request->input('lokalOrigin');
    $member->birthday      = $request->input('birthday');
    $member->sabbathDay    = $request->input('sabbathDay');
    $member->contactNumber = $request->input('contactNumber');
    $member->address       = $request->input('address');
    $member->status        = $request->input('status');
    $member->isOfficer     = $request->input('isOfficer') ? 1 : 0;
    $member->memberType    = $client;
    $member->save();

    $fileImage = $member->image;
    $data = $request->all();
    if($image = array_pull($data, 'userImage')){
     $destinationPath = 'uploads/member/'.$member->id.'/';
     if (!file_exists(public_path($destinationPath))) {
         mkdir(public_path($destinationPath), 0777, true);
     }

     if($image->isValid()){
       $ext        = $image->getClientOriginalExtension();
       $filename   = $image->getFilename();
       $orig_name  = $image->getClientOriginalName();

       $for_upload = $filename . "." . $ext;
       $is_success = $image->move(public_path($destinationPath), $for_upload);

       if($is_success){
         if (!$fileImage) {
           $fileImage = new UserImage;
         }
         $fileImage->imagePath = url('/') . "/" . $destinationPath;
         $fileImage->filename = $filename;
         $fileImage->origFilename = $orig_name;
         $fileImage->extension = $ext;
         $fileImage->save();
       }
     }
    }

    $json['member'] = $member;

    if ($fileImage) {
      $json['imageUrl'] = $fileImage->imagePath . $member->id . '/' . $fileImage->filename . '.' . $fileImage->extension;
    } else {
      $json['imageUrl'] = null;
    }

    return response()->json($json, 200);
  }

  public function seedMembers () {
    $file = public_path() . '/masterlist/ams.csv/ams_table_masterlist.csv';
    Excel::load($file, function($reader) {
        $results = $reader->get();
        try {
          foreach($results as $key => $value) {
            if ($value->id && $value->churchid != "MEMBER") {
              $member = new Masterlist;
              $member->churchId      = $value->churchid;
              $member->firstname     = $value->firstname;
              $member->middlename    = $value->middlename;
              $member->lastname      = $value->lastname;
              $member->email         = $value->email;
              $member->lokalOrigin   = $value->lokalorigin;
              $member->birthday      = $value->birthday;
              $member->sabbathDay    = $value->sabbathday;
              $member->contactNumber = $value->contactnumber;
              $member->fingerPrint   = $value->fingerprint;
              $member->address       = $value->address;
              $member->status        = $value->status;
              $member->isOfficer     = ($value->isofficer ? 1 : 0);
              $member->memberType    = $value->membertype;
              $member->save();
            }
          }
        } catch (\Exception $e) {
          dd($e);
        }

        echo 'Done';
    });
  }

  public function seedAttendance () {
    $file = public_path() . '/masterlist/ams.csv/ams_table_attendance.csv';
    Excel::load($file, function($reader) {
        $results = $reader->get();
        try {
          foreach($results as $key => $value) {
            if ($value->id) {
              $att = new Attendance;
              $att->id = $value->id;
              $att->batch = $value->batch;
              $att->time = $value->time;
              $att->date = $value->date;
              $att->worker_assign = $value->worker_assign;
              $att->officer_assign = $value->officer_assign;
              $att->save();
            }
          }
        } catch (\Exception $e) {
          dd($e);
        }

        echo 'Done' . count($results);
    });
  }

  public function seedAttendanceList () {
    $file = public_path() . '/masterlist/ams.csv/attendance_list.csv';
    Excel::load($file, function($reader) {
        $results = $reader->get();
        try {
          foreach($results as $key => $value) {
            if ($value->id) {
              $att = new AttendanceList;
              $att->id = $value->id;
              $att->brethren_id = $value->brethren_id;
              $att->attendance_id = $value->attendance_id;
              $att->save();
            }
          }
        } catch (\Exception $e) {
          dd($e);
        }

        echo 'Done' . count($results);
    });
  }

  // public function seedMembers2 () {
  //   $file = public_path() . '/masterlist/STB2.xlsx';
  //   Excel::load($file, function($reader) {
  //       $results = $reader->get();
  //       foreach($results as $key => $value) {
  //         if ($value->church_id) {
  //           $check = Masterlist::where('churchId', '=', strval($value->church_id))->first();
  //           if (!$check) {
  //             $member = new Masterlist;
  //             $member->churchId      = strval($value->church_id);
  //             $member->firstname     = $value->first_name;
  //             $member->middlename    = $value->middle_name;
  //             $member->lastname      = $value->last_name;
  //             $member->email         = '';
  //             $member->lokalOrigin   = $value->lokal;
  //             $member->birthday      = '';
  //             $member->sabbathDay    = $value->sabbath_day;
  //             $member->contactNumber = $value->contact_number;
  //             $member->address       = '';
  //             $member->status        = 'ACTIVE';
  //             $member->isOfficer     = 0;
  //             $member->memberType    = 'MEMBER';
  //             $member->save();
  //           }
  //         }
  //       }
  //   });
  // }
}
