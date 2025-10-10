<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    /** @use HasFactory<\Database\Factories\ApiLogFactory> */
    use HasFactory;

    protected $fillable = [
        'service',
        'method',
        'endpoint',
        'request_data',
        'response_data',
        'status_code',
        'status',
        'error_message',
        'duration_ms',
    ];

    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'duration_ms' => 'integer',
        ];
    }
}
