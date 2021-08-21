<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecomendedItemResource extends JsonResource
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
            'description' => $this->description,
            'image' => formatImage($this->thumbnail('medium')),
            'price' => (int) $this->price,
            'restaurant' => [
                'id' => $this->restaurant->id,
                'name' => $this->restaurant->name
            ],
        ];
    }
}
