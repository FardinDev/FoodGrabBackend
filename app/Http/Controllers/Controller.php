<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;



    public function apiSuccessResponse($message, $data = []){

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => $message, 
            'data' => $data, 
        ]);
    }

    public function apiFailedResponse($message, $data = []){

        return response()->json([
            'status' => 'failed',
            'code' => 401,
            'message' => $message, 
            'data' => $data, 
        ]);
    }


    public function paginateFormat($collection, $items)
    {
        return [
            "paginator" => [
                "current_page" =>  $collection->currentPage(),
                "total_pages" =>  $collection->lastPage(),
                "previous_page_url" =>  $collection->previousPageUrl(),
                "next_page_url" =>  $collection->nextPageUrl(),
                "record_per_page" => $collection->perPage(),
            ],
            "pagination_last_page" => $collection->lastPage(),
            "total_count" => count($collection->items()),
            'data' =>  $items
        ];
    }

    public function sendOTP($number){

   
        $otp = rand(pow(10, 3), pow(10, 4)-1);

        $url = "http://66.45.237.70/api.php";
        $number=$number;
        $text="Your #FoodGrab OTP code is ".$otp;
        $data= array(
        'username'=>env('OTP_USERNAME', '01795514777'),
        'password'=>env('OTP_PASSWORD', '8ZYGB6TV'),
        'number'=>"$number",
        'message'=>"$text"
        );

        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $smsresult = curl_exec($ch);
        $p = explode("|",$smsresult);
        $sendstatus = $p[0];

        \Log::debug("smsresult ".$smsresult);
        
        return $otp;
    }
}
