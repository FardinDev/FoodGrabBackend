<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\RestaurantCategoryResource as Category;


class RestaurantResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'photo' => formatImage($this->banner_image),
            'logo' => formatImage($this->logo),
            'menu_categories' => [],
            'categories' => Category::collection($this->available_categories),
        ];
    }
}
