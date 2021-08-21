<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class OrderHistoryResource extends JsonResource
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
            'status' =>  $this->status->name,
            'date' => Carbon::parse($this->created_at)->format('d M Y') ,
            'time' => Carbon::parse($this->created_at)->format('h:i a') ,
        ];
    }
}
