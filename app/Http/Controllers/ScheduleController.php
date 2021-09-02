<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function checkUnrespondsOrders(){

        $unResponed = Order::with('user', 'restaurant', 'restaurant.users')->where('order_status_id', 1)->orderOlderThan(2)->get();

        if ($unResponed) {
            foreach ($unResponed as $order) {
                updateOrderStatus($order, 5, 'admin', 'Time up', 1);
            }
        }
        
        \Log::info(count( $unResponed ));

        return 'done';

        // dd($unResponed);
    }
}
