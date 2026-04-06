<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FlatOrderPlaced implements ShouldBroadcast
{
    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return new Channel('flat-orders');
    }

    public function broadcastAs()
    {
        return 'vendor.flate.order.placed';
    }
}