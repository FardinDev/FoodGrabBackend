<?php

namespace App\Observers;

use App\Models\Order;
use  App\Traits\NotifyUser;
use  App\Traits\NotifyRestaurant;

class OrderObserver
{

    use NotifyUser, NotifyRestaurant;
    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        // \Log::info("Order Created ". $order->id);

        // $this->notifyUser($order);
        // $this->notifyRestaurant($order);
        // $this->notifyUser($order);
    }

    /**
     * Handle the Order "updated" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        // \Log::info("Order updated ". $order->id. " => ". $order->order_status_id);

        $this->notifyRestaurant($order);
        $this->notifyUser($order);
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
