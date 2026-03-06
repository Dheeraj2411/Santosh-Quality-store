<?php

namespace App\Observers;

use App\Jobs\SendWhatsAppNotification;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     * Dispatches the WhatsApp notification job to the queue.
     */
    public function created(Order $order): void
    {
        SendWhatsAppNotification::dispatch($order->id);
    }
}
