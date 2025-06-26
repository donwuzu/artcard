<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class PortraitClock extends Model
{
     use HasFactory;
     protected $fillable = [ 'image_path', 'price'];
}
