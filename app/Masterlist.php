<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Masterlist extends Model
{
    protected $table = 'masterlist';

    public function image () {
      return $this->hasOne('App\UserImage', 'userId');
    }
}
