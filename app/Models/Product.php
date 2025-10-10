<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'wb_product_id',
        'name',
        'sku',
        'description',
        'stock_quantity',
        'price',
        'category',
        'barcode',
        'images',
        'is_active',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'stock_quantity' => 'integer',
            'price' => 'decimal:2',
            'images' => 'array',
            'is_active' => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
