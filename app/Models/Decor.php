<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class Decor extends Model
{
     protected $table = 'portrait_clocks';
     use HasFactory;
     protected $fillable = [ 'image_path', 'price'];
}
