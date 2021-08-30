<?php
namespace App\Traits;

use App\Models\User;
use App\Models\Location;
use App\Models\OrderStatus;
use App\Models\OrderHistory;
use App\Models\Restaurant;
use App\Models\Order;
use Carbon\Carbon;

trait NotifyUser
{
    public function notifyUser(Order $order){

        $user = $order->user;
        $restaurant = $order->restaurant;
        $status = $order->status;


        $title = 'Order '.$status->name;
        $body = '';
        switch ($status->name) {
            case 'Placed':
                $body = 'Your Order placed at '.$restaurant->name.'. Total amount to be paid is '.$order->grand_total.' Tk. Order Number '.$order->order_number;
                break;
            case 'Canceled':
                $body = 'Sorry Your Order from '.$restaurant->name.' is Canceled. Order Number '.$order->order_number;
                break;
            case 'Delivered':
                $body = 'Your Order Delivered Successfully! Enjoy the food';
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

        \Log::notice($title. " ". $body);
    }
}
