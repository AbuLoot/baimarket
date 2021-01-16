<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Laravel\Scout\Searchable;

class ProductLang extends Model
{
    use Searchable;

    protected $table = 'products_lang';

    public $asYouType = true;

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize array...

        return $array;
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }
}
