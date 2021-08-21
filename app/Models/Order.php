<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];


    protected $casts = [
        'cart_details' => 'array'
    ];



    public function details()
    {
        return $this->hasMany(OrderDetails::class, 'order_id', 'id');
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class, 'order_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id', 'id')->select(['id', 'name']);
    }
    
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id')->select(['id', 'name', 'address']);
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id')->select(['id', 'name']);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->select(['id', 'name', 'phone']);
    }
}
