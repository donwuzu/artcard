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
    // 1. Validate the request
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'location' => 'required|string|max:255',
        'quantities' => 'nullable|array',
        'quantities.*' => 'integer|min:0',
    ]);

    // 2. Get quantities from JSON stored in a hidden input synced with localStorage (portraitSelections)
    $localSelections = json_decode($request->input('portraitSelections'), true);
    if (!is_array($localSelections)) {
        $localSelections = [];
    }

    // 3. Merge quantities from form inputs with localStorage (prioritizing localStorage)
    $formQuantities = $validated['quantities'] ?? [];
    $finalQuantities = [];

    foreach ($formQuantities as $id => $qty) {
        if ($qty > 0) {
            $finalQuantities[$id] = (int) $qty;
        }
    }

    foreach ($localSelections as $id => $qty) {
        if ($qty > 0) {
            $finalQuantities[$id] = (int) $qty; // Override with localStorage value
        }
    }

    // 4. Check for empty order
    if (empty($finalQuantities)) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['quantities' => 'Please select at least one portrait with a quantity greater than zero.']);
    }

    // 5. Validate portrait IDs
    $portraitIds = array_keys($finalQuantities);
    $portraits = Portrait::whereIn('id', $portraitIds)->get();

    if ($portraits->count() !== count($finalQuantities)) {
        Log::error('Invalid portrait selection during order.', [
            'expected_ids' => $portraitIds,
            'found_ids' => $portraits->pluck('id')->all(),
            'request_data' => $request->all(),
        ]);

        return redirect()->back()
            ->withInput()
            ->withErrors(['quantities' => 'An error occurred with your selection. Please try again.']);
    }

    // 6. Calculate pricing
    $totalUnits = array_sum($finalQuantities);
    $unitPrice = $totalUnits >= 5 ? 190 : 250;
    $subtotal = $totalUnits * $unitPrice;
    $deliveryFee = 300;
    $totalPrice = $subtotal + $deliveryFee;

    // 7. Save order
    $order = Order::create([
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'location' => $validated['location'],
        'items' => $finalQuantities,
        'total_price' => $totalPrice,
    ]);

    // 8. Generate WhatsApp URL
    $whatsappUrl = $this->sendWhatsappNotification($order, $portraits, $totalUnits, $subtotal, $deliveryFee);

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
