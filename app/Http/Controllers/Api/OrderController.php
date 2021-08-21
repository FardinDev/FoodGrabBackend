<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Location;
use App\Models\OrderDetails;
use App\Models\Restaurant;
use App\Models\Order;
use App\Models\User;
use DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Validator;
use App\Http\Resources\OrderResource;
use Carbon\Carbon;


class OrderController extends Controller
{
    public function index(Request $request){

        $orders = Order::with(['status','restaurant','details', 'histories'])->where('user_id', Auth::user()->id)->latest()->get();

        $data = OrderResource::collection($orders);

        return $this->apiSuccessResponse('Order Data', $data);

    }

    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'restaurant_id' => [
                'required',
                Rule::exists('restaurants', 'id')->where(function ($query) {
                    $query->where('is_open', 1)->where('status', 1);
                }),
            ],
            'location' => 'required|exists:locations,name',
            'address' => 'required|string|max:250',
            'cart' => 'required|array',
            'cart.*.id' => [
                'required',
                Rule::exists('items')->where(function ($query) use ($request) {
                    $query->where('is_available', 1)->where('status', 1)->where('restaurant_id', $request->restaurant_id);
                }),
            ],
            'cart.*.quantity' => 'required',

        ]);

        if ($validator->fails()) {
            return $this->apiFailedResponse($validator->messages()->first(), $validator->messages()->toArray());
        }

        try {
            DB::beginTransaction();

            // restaurant
            $restaurant = Restaurant::find($request->restaurant_id);

            // location
            $location = Location::where('name', $request->location)->first();

            // cart
            $cartData = $this->getCartData($request->cart);

            // total
            $total = collect($cartData)->sum('item_total');

            // delivery
            $deliveryCharge = 30;

            // discount
            $discountData = $this->getDiscountData($restaurant, $total);
            
            // user
            $user = Auth::user();

            $latestOrder = Order::where('restaurant_id', $restaurant->id)->orderBy('created_at','DESC')->first();
            $orderData = [
                'uuid' => Str::uuid()->toString(),
                'order_number' =>   '#'.$restaurant->name[0].str_pad($latestOrder->id ?? 1 + 1, 4, "0", STR_PAD_LEFT) .'-'.Str::random(3),
                'restaurant_id' => $restaurant->id,
                'user_id' =>   $user->id,
                'order_status_id' => 1,
                'location_id' => $location->id,
                'address' => $request->address,
                'instruction' => $request->instruction,
                'total' => $total,
                'is_discounted' => $discountData['discount'],
                'discount_amount' => isset($discountData['discount_value']) ? $discountData['discount_value'] : null,
                'discount_type' => isset($discountData['discount_type']) ? $discountData['discount_type'] : null,
                'discounts' => $discountData['discount_amount'],
                'discounted_total' =>  $total - $discountData['discount_amount'],
                'delivery_charge' => $deliveryCharge,
                'grand_total' =>  ($total - $discountData['discount_amount']) + $deliveryCharge,
                'placed_at' =>  Carbon::now(),
                'cart_details' => $request->cart
               
            ];
            
            $order = Order::create($orderData);

            foreach ($cartData as $details) {
                $orderDetails = [
                    'order_id' => $order->id,
                    'restaurant_id' => $restaurant->id,
                    'user_id' =>   $user->id,
                    'item_id' =>   $details['id'],
                    'quantity' =>   $details['quantity'],
                    'price' =>   $details['price'],
                    'total' =>   $details['price'] * $details['quantity'],
                    // 'discount' =>   0,
                    'instruction' =>  '',
                ];

                OrderDetails::create($orderDetails);

            }


            updateOrderStatus($order, 1);

            DB::commit();

            return $this->apiSuccessResponse('Order Successfull', new OrderResource($order));

        } catch (\Throwable $th) {
            DB::rollback();

            dd($th);
            return $this->apiFailedResponse('Something went wrong', $th);
        }

    }

    private function getDiscountData($restaurant, $total)
    {
        $data = [
            'discount' => false,
            'discount_amount' => 0,
        ];

        if ($restaurant->discount_type != null && $restaurant->discount_amount != null && $restaurant->discount_cap != null) {

            if ($total >= $restaurant->discount_cap) {

                if ($restaurant->discount_type == 'percent') {
                    $label = '-' . (int) $restaurant->discount_amount . '%';
                    $newAmount = $total - ($total * ((100 - (int) $restaurant->discount_amount) / 100));
                } else if ($restaurant->discount_type == 'amount') {
                    $label = '-' . (int) $restaurant->discount_amount . ' Tk';
                    $newAmount = $total - ($total - (int) $restaurant->discount_amount);
                }

                $data = [
                    'discount' => true,
                    'discount_label' => $label,
                    'discount_value' => (int) $restaurant->discount_amount,
                    'discount_type' => $restaurant->discount_type,
                    'discount_amount' => $newAmount,
                ];
            }
        }

        return $data;
    }
    
    private function getCartData($cartData)
    {
        $data = [];

        foreach ($cartData as $cart) {
            $item = Item::find($cart['id']);
            $temp = [];

            $temp['id'] = $item->id;
            $temp['name'] = $item->name;
            $temp['price'] = $item->price;
            $temp['quantity'] = $cart['quantity'];
            $temp['item_total'] = $cart['quantity'] * $item->price;

            $data[] = $temp;
        }

        return $data;
    }
}
