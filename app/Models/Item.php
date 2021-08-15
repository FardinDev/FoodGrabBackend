<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Resizable;
class Item extends Model
{
    use Resizable, HasFactory;

    public function getPriceAttribute($price)
    {
        return number_format($price);
    }


    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id', 'id');
    }
}
