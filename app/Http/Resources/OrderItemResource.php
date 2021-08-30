<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'id' => $this->item->id,
            'name' => $this->item->name,
            'quantity' => $this->quantity,
            'price' => (int) $this->price,
            'total' => (int) $this->total,
        ];
    }
}
