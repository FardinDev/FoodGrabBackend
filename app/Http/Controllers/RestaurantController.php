<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class RestaurantController extends Controller
{
    public function orderRecieved(Request $request, Order $order){
        if (! $request->hasValidSignature()) {
            abort(401);
        }
        if ($order->order_status_id != 1) {
            
            abort(403, 'Already Recieved');
        }

        updateOrderStatus($order, 2);

       
   

        return view('success.200', ['message' => 'Recieved Successfully', 'order' => $order]);
    }
}
