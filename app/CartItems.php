<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartItems extends Model
{
    protected $table = 'cart_items';

    public function cart () {
      return $this->belongsTo('App\Cart');
    }

    public function product () {
      return $this->belongsTo('App\Product');
    }
}
