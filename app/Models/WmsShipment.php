<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WmsShipment extends Model
{
    /** @use HasFactory<\Database\Factories\WmsShipmentFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'wms_shipment_id',
        'tracking_number',
        'status',
        'carrier',
        'requested_at',
        'shipped_at',
        'delivered_at',
        'notes',
        'wms_response',
    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'wms_response' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
