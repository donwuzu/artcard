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
        'name'               => 'required|string|max:255',
        'phone'              => 'required|string|max:20',
        'location'           => 'required|string|max:255',
        'portraitSelections' => 'nullable|string', // Also validate the hidden input
        'quantities'         => 'nullable|array',
        'quantities.*'       => 'integer|min:0',
    ]);

    // 2. Safely decode quantities from localStorage JSON
    $localSelections = [];
    $rawLocalSelections = $request->input('portraitSelections');

    if (!empty($rawLocalSelections)) {
        $decoded = json_decode($rawLocalSelections, true);

        // If JSON is invalid, log the error instead of failing silently
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('JSON decoding failed for portraitSelections during order.', [
                'json_error' => json_last_error_msg(),
                'raw_data'   => $rawLocalSelections,
                'customer_name' => $validated['name'],
            ]);
        } else {
            $localSelections = is_array($decoded) ? $decoded : [];
        }
    }

    // 3. Merge quantities, prioritizing localStorage
    $formQuantities = $validated['quantities'] ?? [];
    $mergedQuantities = array_merge($formQuantities, $localSelections);

    // 4. Sanitize and filter the final list of quantities in one go
    $finalQuantities = [];
    foreach ($mergedQuantities as $id => $qty) {
        $portraitId = (int) $id;
        $quantity = (int) $qty;
        if ($portraitId > 0 && $quantity > 0) {
            $finalQuantities[$portraitId] = $quantity;
        }
    }

    // 5. Check for empty order
    if (empty($finalQuantities)) {
        return back()->withInput()
            ->withErrors(['quantities' => 'Please select at least one portrait.']);
    }

    // 6. Fetch portraits and ensure valid IDs
    $portraitIds = array_keys($finalQuantities);
    $portraits = Portrait::whereIn('id', $portraitIds)->get();

    if ($portraits->count() !== count($finalQuantities)) {
        Log::error('Mismatch in portrait IDs', [
           'input' => $finalQuantities,
           'found' => $portraits->pluck('id')->toArray(),
        ]);
        return back()->withInput()
            ->withErrors(['quantities' => 'Your selections appear invalid. Try again.']);
    }

    // 7. Compute costs
    $totalUnits = array_sum($finalQuantities);
    $unitPrice  = $totalUnits >= 5 ? 190 : 250;
    $subtotal   = $totalUnits * $unitPrice;
    $deliveryFee = 300;
    $totalPrice  = $subtotal + $deliveryFee;

    // 8. Save order
    $order = Order::create([
        'name'        => $validated['name'],
        'phone'       => $validated['phone'],
        'location'    => $validated['location'],
        'items'       => $finalQuantities,
        'total_price' => $totalPrice,
    ]);

    // 9. Generate WhatsApp URL and redirect
    $whatsappUrl = $this->sendWhatsappNotification($order, $portraits, $totalUnits, $subtotal, $deliveryFee);

    session()->flash('success', 'Order placed! Redirecting to WhatsApp.');
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
