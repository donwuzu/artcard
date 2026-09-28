<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'location',
        'items',
        'total_price',
        'currency',
        'status',
        'is_paid',
    ];

    protected $casts = [
        'items' => 'array',
        'total_price' => 'decimal:2',
        'is_paid' => 'boolean',
    ];
}
