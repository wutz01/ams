<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserImage;
use Validator, Auth, DB;
use Hash;

class UserController extends Controller
{

    private function passwordCorrect($suppliedPassword, $user)
    {
        return Hash::check($suppliedPassword, $user->password, []);
    }

    public function authenticate (Request $request) {
      $email = $request->input('email');
    	$password = $request->input('password');

    	if(isset($email) && isset($password)){

    		if(Auth::attempt(['email'=> $email, 'password' => $password])){
          $user = Auth::user();
          $json['token'] = $user->createToken('Ordering')->accessToken;
          $json['user'] = $user;
          return response()->json($json, 200);
    		} else {
    			$json['error']	   = "Email / Password is incorrect.";
          return response()->json($json, 400);
    		}
    	} else {
    		$json['error']		= "Please enter email and password.";
        return response()->json($json, 400);
    	}
    }

    public function register (Request $request) {
      $message = [
    		'email.required' => 'Email Address is required.',
    		'required' => 'The :attribute field is required.'
    	];

      $client = $this->getClient($request->input('userType'));

      $rules = [
          'firstname'        => 'required',
          'lastname'         => 'required',
          'address'          => 'required',
          'city'             => 'required',
          'mobileNo'         => 'required',
          'phoneNo'          => 'required',
          'email'		         => 'email|required|unique:users,email',
          'password'	       => 'required|min:6',
          'confirm_password' => 'required|same:password',
          'userImage'        => 'image',
          'country'          => 'required',
          'userType'         => 'required'
      ];

      if ($client == 'CLIENT') {
        $rules = array_merge($rules, [
          'companyName'      => 'required',
          'companyEmail'     => 'required|email',
          'lineBusiness'     => 'required',
          'companyAddress'   => 'required',
          'companyCity'      => 'required',
          'companyZipCode'   => 'required',
          'companyLandLine'  => 'required',
          'companyCountry'   => 'required',
          'designation'      => 'required'
        ]);
      }
  	  $validator = Validator::make($request->all(), $rules, $message);

      if ($validator->fails()) {
          $json['errors'] = $validator->messages();
          return response()->json($json, 400);
      }


    	$user = new User;
    	$user->firstname     = $request->input('firstname');
    	$user->middlename    = $request->input('middlename') != null ? $request->input('middlename') : '';
    	$user->lastname      = $request->input('lastname');
    	$user->mobileNo      = $request->input('mobileNo');
    	$user->phoneNo       = $request->input('phoneNo');
    	$user->email         = $request->input('email');
    	$user->password      = bcrypt($request->input('password'));
      $user->address       = $request->input('address');
      $user->city          = $request->input('city');
      $user->zipCode       = $request->input('zipCode');
      $user->country       = $request->input('country');

      if ($client == 'CLIENT') {
        $user->companyName   = $request->has('companyName') ? $request->input('companyName') : '';
        $user->companyEmail  = $request->has('companyEmail') ? $request->input('companyEmail') : '';
        $user->lineBusiness  = $request->has('lineBusiness') ? $request->input('lineBusiness') : '';
        $user->companyAddress = $request->has('companyAddress') ? $request->input('companyAddress') : '';
        $user->companyCity    = $request->has('companyCity') ? $request->input('companyCity') : '';
        $user->companyZipCode = $request->has('companyZipCode') ? $request->input('companyZipCode') : '';
        $user->companyLandLine= $request->has('companyLandLine') ? $request->input('companyLandLine') : '';
        $user->companyCountry = $request->has('companyCountry') ? $request->input('companyCountry') : '';
        $user->designation   = $request->has('designation') ? $request->input('designation') : '';
      }

      $user->userType      = $client;
    	$user->save();

      $data = $request->all();
      if($image = array_pull($data, 'userImage')){
       $destinationPath = 'uploads/user/'.$user->id.'/';
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
           $fileImage->userId = $user->id;
           $fileImage->imagePath = url('/') . "/" . $destinationPath;
           $fileImage->filename = $filename;
           $fileImage->origFilename = $orig_name;
           $fileImage->extension = $ext;
           $fileImage->save();
         }
       }
      }

    	$json['token'] = $user->createToken('Ordering')->accessToken;
      $json['user'] = $user;

      if ($user->image) {
        $json['imageUrl'] = $user->image->imagePath . $user->id . '/' . $user->image->filename . '.' . $user->image->extension;
      } else {
        $json['imageUrl'] = null;
      }
      return response()->json($json, 200);
    }

    public function getClient($client) {
      $client = strtoupper($client);
      switch ($client) {
        case 'CLIENT': $client = 'CLIENT'; break;
        case 'SALES_AGENT': $client = 'SALES_AGENT'; break;
        case 'ADMIN': $client = 'ADMIN'; break;
        default: $client = 'CLIENT'; break;
      }

      return $client;
    }

    public function userLogout(Request $request) {
      $request->user()->token()->revoke();
      return response(null, 204);
    }

    public function getUserLogin () {
      $json['user'] = $user = Auth::user();
      if ($user->image) {
        $json['imageUrl'] = $user->image->imagePath . $user->id . '/' . $user->image->filename . '.' . $user->image->extension;
      } else {
        $json['imageUrl'] = null;
      }
      return response()->json($json, 200);
    }

    public function getAllUsers (Request $request) {
      if ($request->has('status')) {
        $json['users'] = User::where('status', strtoupper($request->input('status')))->get();
        return response()->json($json, 200);
      }
      $json['users'] = User::all();
      return response()->json($json, 200);
    }

    public function getUser ($id) {
      $json['user'] = $user = User::find($id);
      if ($user->image) {
        $json['imageUrl'] = $user->image->imagePath . $user->id . '/' . $user->image->filename . '.' . $user->image->extension;
      } else {
        $json['imageUrl'] = null;
      }
      return response()->json($json, 200);
    }

    public function updateUser (Request $request) {
      $message = [
    		'email.required' => 'Email Address is required.',
    		'required' => 'The :attribute field is required.'
    	];
      $userId = $request->input('userId');

      $client = $this->getClient($request->input('userType'));

      $rules = [
          'firstname'        => 'required',
          'lastname'         => 'required',
          'address'          => 'required',
          'city'             => 'required',
          'mobileNo'         => 'required',
          'phoneNo'          => 'required',
          'email'		         => 'email|required|unique:users,email,'.$userId,
          'userImage'        => 'image',
          'country'          => 'required',
          'userType'         => 'required'
      ];

      if ($client == 'CLIENT') {
        $rules = array_merge($rules, [
          'companyName'      => 'required',
          'companyEmail'     => 'required|email',
          'lineBusiness'     => 'required',
          'companyAddress'   => 'required',
          'companyCity'      => 'required',
          'companyZipCode'   => 'required',
          'companyLandLine'  => 'required',
          'companyCountry'   => 'required',
          'designation'      => 'required'
        ]);
      }

      if ($request->has('newPassword')) {
        $rules = array_merge($rules, [
          'newPassword' => 'required|min:6',
          'confirm_password' => 'same:newPassword'
        ]);
      }

  	  $validator = Validator::make($request->all(), $rules, $message);

      if (!isset($userId)) {
        $json['error'] = 'User not found';
        return response()->json($json, 400);
      }

      if ($validator->fails()) {
          $json['errors'] = $validator->messages();
          return response()->json($json, 400);
      }

    	$user = User::find($userId);

      if (!$this->passwordCorrect($request->input('oldPassword'), $user)) return response()->json(['error' => 'Old Password does not match.'], 400);
      if ($request->has('newPassword')) {
        $user->password = bcrypt($request->input('newPassword'));
      }
      $user->firstname     = $request->has('firstname') ? $request->input('firstname') : $user->firstname;
    	$user->middlename    = $request->has('middlename') ? $request->input('middlename') : $user->middlename;
    	$user->lastname      = $request->has('lastname') ? $request->input('lastname') : $user->lastname;
    	$user->mobileNo      = $request->has('mobileNo') ? $request->input('mobileNo') : $user->mobileNo;
    	$user->phoneNo       = $request->has('phoneNo') ? $request->input('phoneNo') : $user->phoneNo;
    	$user->email         = $request->has('email') ? $request->input('email') : $user->email;
      $user->address       = $request->has('address') ? $request->input('address') : $user->address;
      $user->city          = $request->has('city') ? $request->input('city') : $user->city;
      $user->zipCode       = $request->has('zipCode') ? $request->input('zipCode') : $user->zipCode;
      $user->country       = $request->has('country') ? $request->input('country') : $user->country;

      if ($client == 'CLIENT') {
        $user->companyName   = $request->has('companyName') ? $request->input('companyName') : $user->companyName;
        $user->companyEmail  = $request->has('companyEmail') ? $request->input('companyEmail') : $user->companyEmail;
        $user->lineBusiness  = $request->has('lineBusiness') ? $request->input('lineBusiness') : $user->lineBusiness;
        $user->companyAddress = $request->has('companyAddress') ? $request->input('companyAddress') : $user->companyAddress;
        $user->companyCity    = $request->has('companyCity') ? $request->input('companyCity') : $user->companyCity;
        $user->companyZipCode = $request->has('companyZipCode') ? $request->input('companyZipCode') : $user->companyZipCode;
        $user->companyLandLine= $request->has('companyLandLine') ? $request->input('companyLandLine') : $user->companyLandLine;
        $user->companyCountry = $request->has('companyCountry') ? $request->input('companyCountry') : $user->companyCountry;
        $user->designation   = $request->has('designation') ? $request->input('designation') : $user->designation;
      }

      $user->userType      = $client;
    	$user->save();

      $fileImage = $user->image;
      $data = $request->all();
      if($image = array_pull($data, 'userImage')){
       $destinationPath = 'uploads/user/'.$user->id.'/';
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

      $json['user'] = $user;

      if ($fileImage) {
        $json['imageUrl'] = $fileImage->imagePath . $user->id . '/' . $fileImage->filename . '.' . $fileImage->extension;
      } else {
        $json['imageUrl'] = null;
      }

      return response()->json($json, 200);
    }
}
