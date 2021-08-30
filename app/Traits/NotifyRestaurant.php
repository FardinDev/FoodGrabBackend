<?php
namespace App\Traits;
use App\Models\Order;


trait NotifyRestaurant
{
    public function notifyRestaurant(Order $order){

        $types = ['Placed', 'Canceled'];

        $user = $order->user;
        $restaurant = $order->restaurant;
        $status = $order->status;

    
        if (in_array($status->name, $types)) {
            $details = [
                'restaurant' => $restaurant->name,
                'owner_name' => $restaurant->users->first()->name,
                'owner_phone' => $restaurant->users->first()->phone,
                'user_name' => $user->name,
                'user_phone' => $user->phone,
                'total' => $order->discounted_total,
                'delivery' => $order->delivery_charge,
                'grand_total' => $order->grand_total,
                'area' => $order->location->name,
                'address' => $order->address,
                'items' => [],
            ];

            foreach ($order->details as $detail) {
                $details['items'][] = $detail->item->name.' x '. $detail->quantity;
            }

           \Log::warning([$details]);
        }

    }
}
