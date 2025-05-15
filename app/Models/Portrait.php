<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class Portrait extends Model
{
     use HasFactory;
     protected $fillable = [ 'image_path', 'price'];
}
