<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Item;
use App\Http\Resources\RestaurantResource;
use App\Http\Resources\RecomendedItemResource;
use Illuminate\Support\Facades\Auth;
use Validator;


class RestaurantController extends Controller
{
    public function index(Request $request){

        $restaurants = Restaurant::with(['available_categories', 'available_categories.available_items'])->where('is_open', 1)->where('status', 1);

        if ($request->has('status')) {
            $restaurants->where('status', $request->status);
        }
     

        if ($request->paginate != 'false') {

            $restaurants = $restaurants->inRandomOrder()->paginate(10);

            $data = $this->paginateFormat($restaurants, RestaurantResource::collection($restaurants->items()));

        }else{

            if ($request->has('id')) {
                    $validator = Validator::make($request->all(),  [
                        'id' => 'required|exists:restaurants,id',
                    ]);
                    if ($validator->fails()) return $this->apiFailedResponse('Falied', $validator );

                    $restaurant = $restaurants->findOrFail($request->id);

                    $data = new RestaurantResource($restaurant);
                }else{

                    $restaurants = $restaurants->inRandomOrder()->get();

                    $data = RestaurantResource::collection($restaurants);
                }
        }

        return $this->apiSuccessResponse('Restaurant data', $data);

    }

    public function recomended(Request $request){

        $items = Item::whereHas('restaurant', function ($query) {
             $query->where('is_open', 1)->where('status', 1);
        })->whereHas('category', function ($query) {
            $query->where('status', 1);
       })->whereNotNull('image')->where('is_available', 1)->inRandomOrder()->take(5)->get();

        

        return $this->apiSuccessResponse('Recomended data', RecomendedItemResource::collection($items));

    }



}
