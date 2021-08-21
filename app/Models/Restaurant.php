<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Spatial;
use TCG\Voyager\Traits\Resizable;

class Restaurant extends Model
{
    use HasFactory, Spatial, Resizable;

    public function users()
    {
        return $this->belongsToMany(User::class, 'restaurants_users');
    }

   
    public function categories()
    {
        return $this->hasMany(MenuCategory::class, 'restaurant_id', 'id');
    }

    public function available_categories() {
        return $this->categories()->where('is_available','=', 1)->where('status','=', 1);
    }
    
}
