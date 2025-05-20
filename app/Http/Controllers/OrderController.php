<?php

namespace App\Http\Controllers;

use App\Models\Portrait;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'location' => 'required|string|max:255',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:0',
            'quantities_carousel' => 'nullable|array',
            'quantities_carousel.*' => 'integer|min:0',
        ]);

        // 2. Merge and filter non-zero quantities with keys preserved
        $mergedQuantities = array_replace(
            $validated['quantities'] ?? [],
            $validated['quantities_carousel'] ?? []
        );

        $selectedQuantities = array_filter($mergedQuantities, fn($qty) => $qty > 0);

        if (empty($selectedQuantities)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['quantities' => 'Please select at least one portrait with a quantity greater than zero.']);
        }

        // 3. Get valid portraits
        $portraitIds = array_keys($selectedQuantities);
        $portraits = Portrait::whereIn('id', $portraitIds)->get();

        if ($portraits->count() !== count($selectedQuantities)) {
            Log::error('Invalid portrait selection.', [
                'expected' => $portraitIds,
                'found' => $portraits->pluck('id')->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['quantities' => 'One or more selected portraits are invalid.']);
        }

        // 4. Calculate pricing
        $totalUnits = array_sum($selectedQuantities);
        $unitPrice = $totalUnits >= 5 ? 190 : 250;
        $subtotal = $totalUnits * $unitPrice;
        $deliveryFee = 300;
        $totalPrice = $subtotal + $deliveryFee;

        // 5. Save order with only required fields
        $order = Order::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'location' => $validated['location'],
            'items' => $selectedQuantities,
            'total_price' => $totalPrice,
        ]);

        // 6. Generate WhatsApp link
        $whatsappUrl = $this->sendWhatsappNotification($order, $portraits, $totalUnits, $subtotal, $deliveryFee);

        // 7. Redirect to WhatsApp
        session()->flash('success', 'Order placed successfully! Redirecting to WhatsApp.');

        return redirect()->away($whatsappUrl);
    }

    protected function sendWhatsappNotification($order, $portraits, $totalUnits, $subtotal, $deliveryFee)
    {
        $adminPhone = '254738269376';

        $message = " *NEW PORTRAIT ORDER* \n\n";
        $message .= " *Customer Details*\n";
        $message .= "• Name: {$order->name}\n";
        $message .= "• Phone: {$order->phone}\n";
        $message .= "• Location: {$order->location}\n\n";

        $message .= " *Order Summary*\n";
        $portraitsById = $portraits->keyBy('id');

        foreach ($order->items as $portraitId => $qty) {
            if ($qty > 0) {
                $portrait = $portraitsById->get($portraitId);
                if ($portrait) {
                    $message .= "• Portrait \"{$portrait->name}\" (ID: {$portrait->id}) × {$qty}\n";
                } else {
                    $message .= "• Portrait #{$portraitId} × {$qty} (Details unavailable)\n";
                }
            }
        }

        $message .= "\n";
        $message .= " *Order Totals*\n";
        $message .= "• Total Items: {$totalUnits}\n";
        $message .= "• Subtotal: KSh {$subtotal}\n";
        $message .= "• Delivery Fee: KSh {$deliveryFee}\n";
        $message .= "• *TOTAL DUE: KSh {$order->total_price}*\n";

        $message .= "\nOrder Time: " . now('Africa/Nairobi')->format('Y-m-d h:i A');

        return "https://wa.me/{$adminPhone}?text=" . rawurlencode($message);
    }
}
