<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\WhatsAppMessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly int $orderId,
    ) {}

    public function handle(WhatsAppMessageService $whatsAppService): void
    {
        $order = Order::with(['items', 'user'])->find($this->orderId);

        if (!$order) {
            Log::warning("[WhatsApp] Order #{$this->orderId} not found, skipping notification.");
            return;
        }

        $message = $whatsAppService->generateMessage($order);
        $url     = $whatsAppService->generateWhatsAppUrl($order);

        // Log to a dedicated channel so the shop owner can access the link
        Log::channel('whatsapp')->info("Order #{$order->order_number}", [
            'order_id'     => $order->id,
            'order_number' => $order->order_number,
            'total'        => $order->total,
            'whatsapp_url' => $url,
            'message'      => $message,
        ]);

        Log::info("[WhatsApp] Notification generated for Order #{$order->order_number}");
    }
}
