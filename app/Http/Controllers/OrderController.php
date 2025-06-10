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
        // 1. Corrected Validation
        // 'quantities' and 'quantities_carousel' are now both 'nullable'.
        // This allows users to order exclusively from either the grid or carousel view without validation errors.
        // The real check for an empty order is handled after we merge the quantities.
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'location' => 'required|string|max:255',
            'quantities' => 'nullable|array',
            'quantities.*' => 'integer|min:0',
            'quantities_carousel' => 'nullable|array',
            'quantities_carousel.*' => 'integer|min:0',
        ]);

        // 2. Corrected Quantity Merging Logic
        // We now loop through both arrays and SUM the values for each portrait ID.
        // This correctly combines selections from both grid and carousel views.
        $finalQuantities = [];
        $quantitiesGrid = $validated['quantities'] ?? [];
        $quantitiesCarousel = $validated['quantities_carousel'] ?? [];

        // Sum quantities from the grid view
        foreach ($quantitiesGrid as $id => $qty) {
            if ($qty > 0) {
                $finalQuantities[$id] = ($finalQuantities[$id] ?? 0) + (int)$qty;
            }
        }

        // Sum quantities from the carousel view
        foreach ($quantitiesCarousel as $id => $qty) {
            if ($qty > 0) {
                $finalQuantities[$id] = ($finalQuantities[$id] ?? 0) + (int)$qty;
            }
        }

        // 3. Check for Empty Order (using the correctly merged quantities)
        if (empty($finalQuantities)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['quantities' => 'Please select at least one portrait with a quantity greater than zero.']);
        }

        // 4. Get valid portraits (This logic is sound and now uses the correct data)
        $portraitIds = array_keys($finalQuantities);
        $portraits = Portrait::whereIn('id', $portraitIds)->get();

        if ($portraits->count() !== count($finalQuantities)) {
            Log::error('Invalid portrait selection during order.', [
                'expected_ids' => $portraitIds,
                'found_ids' => $portraits->pluck('id')->all(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['quantities' => 'An error occurred with your selection. Please try again.']);
        }

        // 5. Calculate pricing (This logic is sound and now uses the correct data)
        $totalUnits = array_sum($finalQuantities);
        $unitPrice = $totalUnits >= 5 ? 190 : 250;
        $subtotal = $totalUnits * $unitPrice;
        $deliveryFee = 300;
        $totalPrice = $subtotal + $deliveryFee;

        // 6. Save order with correctly merged items
        $order = Order::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'location' => $validated['location'],
            'items' => $finalQuantities, // Use the correctly summed quantities
            'total_price' => $totalPrice,
        ]);

        // 7. Generate WhatsApp link (This logic is sound)
        $whatsappUrl = $this->sendWhatsappNotification($order, $portraits, $totalUnits, $subtotal, $deliveryFee);

        // 8. Redirect to WhatsApp
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
