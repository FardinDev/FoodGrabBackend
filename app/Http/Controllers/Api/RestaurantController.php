<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RecomendedItemResource;
use App\Http\Resources\RestaurantResource;
use App\Models\Item;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {

        $restaurants = Restaurant::with(['available_categories', 'available_categories.available_items'])->where('is_open', 1)->where('status', 1);

        if ($request->has('status')) {
            $restaurants->where('status', $request->status);
        }

        if ($request->paginate != 'false') {

            $restaurants = $restaurants->inRandomOrder()->paginate(10);

            $data = $this->paginateFormat($restaurants, RestaurantResource::collection($restaurants->items()));

        } else {

            if ($request->has('id')) {
                $validator = Validator::make($request->all(), [
                    'id' => 'required|exists:restaurants,id',
                ]);
                if ($validator->fails()) {
                    return $this->apiFailedResponse('Falied', $validator);
                }

                $restaurant = $restaurants->findOrFail($request->id);

                $data = new RestaurantResource($restaurant);
            } else {

                $restaurants = $restaurants->inRandomOrder()->get();

                $data = RestaurantResource::collection($restaurants);
            }
        }

        return $this->apiSuccessResponse('Restaurant data', $data);

    }

    public function recomended(Request $request)
    {

        $items = Item::whereHas('restaurant', function ($query) {
            $query->where('is_open', 1)->where('status', 1);
        })->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->whereNotNull('image')->where('is_available', 1)->inRandomOrder()->take(5)->get();

        return $this->apiSuccessResponse('Recomended data', RecomendedItemResource::collection($items));

    }

    public function getValues(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required|exists:restaurants,id',
            'amount' => 'required',
            'location' => 'required|exists:locations,name',

        ]);

        if ($validator->fails()) {
            return $this->apiFailedResponse($validator->messages()->first(), $validator->messages()->toArray());
        }


        //// delivery calc here

        $deliveryCharge = 30;
        
        $data = [
            'discount' => false,
            'discount_label' => '',
            'discount_amount' => '',
            'delivery_charge' => $deliveryCharge,
            'total' => $request->amount + $deliveryCharge,
        ];

        $restaurant = Restaurant::where('discount_type', '!=', null)
        ->where('discount_amount', '!=', null)
        ->where('discount_cap', '!=', null)
        ->find($request->restaurant_id);


        if (!$restaurant) {
            $data['message'] = 'No discounts available :(';
            return $this->apiSuccessResponse('Cart data',  $data);
        }


        if ($restaurant->discount_cap > $request->amount) {
            $data['message'] = 'Order amount should be '. $restaurant->discount_cap .'Tk or more to get discount';
            return $this->apiSuccessResponse('Cart data',  $data);
        }



        if ($restaurant->discount_type == 'percent') {
            $label = '-'.(int) $restaurant->discount_amount.'%';
            $newAmount = $request->amount * ((100-(int) $restaurant->discount_amount) / 100);
        }else if ($restaurant->discount_type == 'amount'){
            $label = '-'.(int) $restaurant->discount_amount.' Tk';
            $newAmount = $request->amount - (int) $restaurant->discount_amount;
        }

        
        $data = [
            'discount' => true,
            'discount_label' => $label,
            'discount_amount' => (int) $restaurant->discount_amount,
            'delivery_charge' => $deliveryCharge,
            'total' => $newAmount + $deliveryCharge,
            'message' => 'You are getting '. ($request->amount - $newAmount) .'Tk discount'
        ];


        return $this->apiSuccessResponse('Cart data',  $data);

    }

}
