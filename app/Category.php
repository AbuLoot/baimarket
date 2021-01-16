<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use NodeTrait;

    protected $table = 'categories';

    public function products()
    {
        return $this->hasMany('App\Product');
    }

    // public function products_lang()
    // {
    //     return $this->belongsToMany('App\ProductLang', 'product_category');
    // }
}
