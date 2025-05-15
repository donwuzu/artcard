<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Portrait;
use App\Models\Order;

class OrderCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_submit_valid_order()
    {
        // Create dummy portraits
        $portraits = Portrait::factory()->count(5)->create([
            'price' => 200,
        ]);

        // Prepare quantities (5 portraits total)
        $quantities = $portraits->mapWithKeys(function ($portrait) {
            return [$portrait->id => 1];
        })->toArray();

        $response = $this->post('/checkout', [
            'name' => 'Test User',
            'phone' => '0712345678',
            'location' => 'Test Town',
            'quantities' => $quantities,
        ]);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('orders', [
            'name' => 'Test User',
            'phone' => '0712345678',
            'location' => 'Test Town',
            'total_price' => 1000,
        ]);

        $order = Order::first();
        $this->assertEquals($quantities, $order->items);
    }
}


;
