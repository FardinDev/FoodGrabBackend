<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Order extends Model
{
    use HasFactory;

    protected $guarded = [];


    protected $casts = [
        'cart_details' => 'array'
    ];



    public function getDiscountLabel()
    {
        $label = 0;

        if ($this->is_discounted) {
            $type = $this->discount_type == 'percent' ? '%' : ' TK';

            $label = ((int) $this->discount_amount ).$type;
        }

        return $label;
    }
    public function itemCount()
    {
        return $this->details->sum('quantity');
    }
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
        return $this->belongsTo(User::class, 'user_id', 'id')->select(['id', 'name', 'phone', 'notification_token']);
    }

    public function scopeOrderOlderThan($query, $interval)
    {
        return $query->where('placed_at', '<', Carbon::parse('-5 minutes'));

        
    }
}
