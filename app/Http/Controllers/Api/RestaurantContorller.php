<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Http\Resources\RestaurantResource;
use Illuminate\Support\Facades\Auth;
use Validator;


class RestaurantContorller extends Controller
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
}
