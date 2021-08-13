<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    use HasFactory;


    public function items()
    {
        return $this->hasMany(Item::class, 'menu_category_id', 'id');
    }


    public function available_items() {
        return $this->items()->where('is_available','=', 1)->where('status','=', 1);
    }
}
