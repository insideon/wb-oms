<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'wb_order_id',
        'customer_name',
        'customer_name_translated',
        'customer_address',
        'customer_address_translated',
        'customer_phone',
        'status',
        'total_amount',
        'currency',
        'ordered_at',
        'raw_data',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
            'raw_data' => 'array',
            'total_amount' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(WmsShipment::class);
    }
}
