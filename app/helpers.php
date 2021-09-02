<?php

use App\Models\User;
use App\Models\Location;
use App\Models\OrderStatus;
use App\Models\OrderHistory;
use App\Models\Restaurant;
use App\Models\Order;
use Carbon\Carbon;


if (! function_exists('adminFields')) {
    function adminFields() {
        $fieldList = ['Restaurant', 'Icon', 'Address'];

        return $fieldList;
    }
}

if (! function_exists('adminFields')) {
    function adminEditFields() {
        $fieldList = ['Restaurant', 'Status', 'Icon', 'Customer', 'Address'];

        return $fieldList;
    }
}

if (! function_exists('getBadgeClass')) {
    function getBadgeClass($status) {
        switch ($status) {
            case 'Placed':
                return 'badge-primary';
                break;
            case 'Received':
                return 'badge-warning';
                break;
            case 'Pending':
                return 'badge-secondary';
                break;
            case 'Delivered':
                return 'badge-success';
                break;
            case 'Canceled':
                return 'badge-danger';
                break;
                
                default:
                # code...
                return 'badge-secondary';
                break;
        }
    }
}

if (! function_exists('formatImage')) {
    function formatImage($image) {
        return TCG\Voyager\Facades\Voyager::image($image);
    }
}

if (! function_exists('updateOrderStatus')) {
    function updateOrderStatus($order, $status_id, $canceled_by = null, $cancelation_reason = null, $canceled_by_id = null) {
        $order->order_status_id = $status_id;
        $order->save();

        $historyData = [
                'order_id' => $order->id,
                'order_status_id' => $status_id,
                'updated_by' => auth()->user() ? auth()->user()->id : $order->restaurant->users->first()->id,
                'canceled_by' => $canceled_by,
                'cancelation_reason' => $cancelation_reason,
                'canceled_by_id' => $canceled_by_id,
        ];

        OrderHistory::create($historyData);

        // oldNotify($order);
    }
}


if (! function_exists('oldNotify')) {
    function oldNotify($order) {

        $user = User::find($order->user_id);
        $restaurant = Restaurant::find($order->restaurant_id);
        $order = Order::find($order->id);
        $status = OrderStatus::find($order->order_status_id);


        $title = 'Order '.$status->name;
        $body = '';
        switch ($status->name) {
            case 'Placed':
                $body = 'Your Order placed at '.$restaurant->name.'. Total amount to be paid is '.$order->grand_total.' Tk. Order Number '.$order->order_number;
                break;
            
            default:
                # code...
                break;
        }


        $key = $user->notification_token;

        $payload = array(
            'to' => $key,
            'sound' => 'default',
            'title' =>  $title,
            'body' =>  $body
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://exp.host/--/api/v2/push/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Accept-Encoding: gzip, deflate",
                "Content-Type: application/json",
                "cache-control: no-cache",
                "host: exp.host",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        // if ($err) {
        //     echo "cURL Error #:" . $err;
        // } else {
        //     echo $response;
        // }

    }
}