<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'user_id', 'address_id', 'status', 'payment_method',
        'payment_status', 'subtotal', 'discount', 'delivery_charge', 'total',
        'notes', 'shipping_address', 'delivered_at',
        'razorpay_order_id', 'razorpay_payment_id',
    ];

    protected $casts = [
        'subtotal'         => 'decimal:2',
        'discount'         => 'decimal:2',
        'delivery_charge'  => 'decimal:2',
        'total'            => 'decimal:2',
        'shipping_address' => 'array',
        'delivered_at'     => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Helpers
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'yellow',
            'processing' => 'blue',
            'shipped'    => 'orange',
            'delivered'  => 'green',
            'cancelled'  => 'red',
            default      => 'gray',
        };
    }

    public static function generateOrderNumber(): string
    {
        return 'SS-' . strtoupper(uniqid());
    }
}
