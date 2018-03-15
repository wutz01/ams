<?php

namespace App\Http\Controllers;
use App\Masterlist;
use App\UserImage;
use Validator;

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
        'memberType'       => 'required'
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

  public function getAllMembers (Request $request) {
    if ($request->has('status')) {
      $json['members'] = Masterlist::where('status', strtoupper($request->input('status')))->get();
      return response()->json($json, 200);
    }
    $json['members'] = Masterlist::all();
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
      'memberType'       => 'required'
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
}
