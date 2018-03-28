<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Masterlist extends Model
{
    protected $table = 'masterlist';
    protected $hidden = ['fingerPrint'];

    public function image () {
      return $this->hasOne('App\UserImage', 'userId');
    }
}
