<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\OrderItemResource;
use App\Http\Resources\OrderHistoryResource;
use Carbon\Carbon;

class OrderResource extends JsonResource
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
            'order_number' => $this->order_number,
            'date' => Carbon::parse($this->placed_at)->format('d M Y') ,
            'time' => Carbon::parse($this->placed_at)->format('h:i a') ,
            'total' => $this->total,
            'discounts' => $this->discounts,
            'discounted_total' => $this->discounted_total,
            'delivery_charge' => $this->delivery_charge,
            'grand_total' => $this->grand_total,
            'location' => [
                'id' => $this->location->id,
                'name' => $this->location->name,
                'address' => $this->address
            ],
            'user' => $this->user,
            'status' => $this->status,
            'restaurant' => $this->restaurant,
            'items' => OrderItemResource::collection($this->details),
            'history' => OrderHistoryResource::collection($this->histories),
            
        ];
    }
}
