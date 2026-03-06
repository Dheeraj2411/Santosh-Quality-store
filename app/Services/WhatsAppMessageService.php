<?php

namespace App\Services;

use App\Models\Order;

class WhatsAppMessageService
{
    /**
     * Generate the WhatsApp notification message for a given order.
     */
    public function generateMessage(Order $order): string
    {
        $order->loadMissing(['items', 'user']);

        $address = $order->shipping_address;
        $addressLine = implode(', ', array_filter([
            $address['line1'] ?? '',
            $address['line2'] ?? '',
            $address['city'] ?? '',
            $address['state'] ?? '',
            $address['pincode'] ?? '',
        ]));

        $customerName  = $address['name'] ?? $order->user->name ?? 'N/A';
        $customerPhone = $address['phone'] ?? $order->user->phone ?? 'N/A';

        // Build item list
        $itemLines = '';
        foreach ($order->items as $i => $item) {
            $num = $i + 1;
            $lineTotal = number_format($item->price * $item->quantity, 2);
            $itemLines .= "{$num}. {$item->product_name} (Qty: {$item->quantity}) - ₹{$lineTotal}\n";
        }

        $message = "🛒 *New Order - Santosh Store*\n\n"
            . "Order ID: {$order->order_number}\n"
            . "Payment: " . strtoupper($order->payment_method) . " ({$order->payment_status})\n\n"
            . "*Customer Details:*\n"
            . "Name: {$customerName}\n"
            . "Phone: {$customerPhone}\n"
            . "Address: {$addressLine}\n\n"
            . "*Order Items:*\n"
            . $itemLines . "\n"
            . "Total Amount: ₹" . number_format($order->total, 2) . "\n";

        if ($order->notes) {
            $message .= "\nNotes: {$order->notes}\n";
        }

        $message .= "\nOrder Time: " . $order->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A');

        return $message;
    }

    /**
     * Generate the wa.me URL with the encoded message.
     */
    public function generateWhatsAppUrl(Order $order): string
    {
        $phone   = config('services.whatsapp.owner_number', '919910494819');
        $message = $this->generateMessage($order);

        return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
    }
}
