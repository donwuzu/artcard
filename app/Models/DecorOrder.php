<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DecorOrder extends Model
{
    use HasFactory;

    protected $table = 'clock_orders';

    protected $fillable = [
        'name',
        'phone',
        'location',
        'items',
        'total_price',
        'status',
        'currency',
        'is_paid',
    ];

    protected $casts = [
        'items' => 'array',
        'total_price' => 'decimal:2',
        'is_paid' => 'boolean',
    ];
}
