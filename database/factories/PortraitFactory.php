<?php

namespace Database\Factories;


use App\Models\Portrait;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Portrait>
 */
class PortraitFactory extends Factory
{
    protected $model = Portrait::class;

    public function definition(): array
    {
        return [
            'image_path' => 'portraits/sample.jpg',
            'price' => 200,
            
        ];
    }
}
