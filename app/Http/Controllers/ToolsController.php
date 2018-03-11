<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Hashkey;
use Webpatser\Uuid\Uuid;

class ToolsController extends Controller
{
    public function generateUUID () {
      $json['uuid'] = $uuid = Uuid::generate()->string;
      $json['ip_address'] = $ip = \Request::ip();
      $tool = new Hashkey;
      $tool->uuid = $uuid;
      $tool->ip_address = $ip;
      $tool->save();
      return response()->json($json, 200);
    }
}
