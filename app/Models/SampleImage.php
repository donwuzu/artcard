<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampleImage extends Model
{
    use HasFactory;

    protected $table = 'sample_images';

    protected $fillable = [
        'image_path',
        'price',
    ];
}
