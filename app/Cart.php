<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';

    public function items() {
      return $this->hasMany('App\CartItems', 'cartId');
    }
}
