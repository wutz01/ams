<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'pivot'
    ];

    /**
     * The category that belong to the products.
     */
    public function products()
    {
        return $this->belongsToMany('App\Product', 'categories_product', 'category_id', 'product_id');
    }
}
