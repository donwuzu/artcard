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
    // 1. Validate the request (No changes here)
    $validated = $request->validate([
        'name'               => 'required|string|max:255',
        'phone'              => 'required|string|max:20',
        'location'           => 'required|string|max:255',
        'portraitSelections' => 'nullable|string',
        'quantities'         => 'nullable|array',
        'quantities.*'       => 'integer|min:0',
    ]);

    // 2. Safely decode and merge quantities (No changes here, this logic is solid)
    $localSelections = [];
    $rawLocalSelections = $request->input('portraitSelections');
    if (!empty($rawLocalSelections)) {
        $decoded = json_decode($rawLocalSelections, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $localSelections = $decoded;
        }
    }
    $formQuantities = $validated['quantities'] ?? [];
    $mergedQuantities = array_merge($formQuantities, $localSelections);
    $finalQuantities = [];
    foreach ($mergedQuantities as $id => $qty) {
        $portraitId = (int) $id;
        $quantity = (int) $qty;
        if ($portraitId > 0 && $quantity > 0) {
            $finalQuantities[$portraitId] = $quantity;
        }
    }

    // 3. Check for empty submission (No changes here)
    if (empty($finalQuantities)) {
        return back()->withInput()->withErrors(['quantities' => 'Please select at least one portrait.']);
    }

    // ========================================================================
    // 4. MODIFIED LOGIC: Validate Portrait IDs and filter out invalid ones
    // ========================================================================

    $submittedPortraitIds = array_keys($finalQuantities);
    $portraits = Portrait::whereIn('id', $submittedPortraitIds)->get();

    // Get a list of IDs that are actually valid and exist in the database
    $validPortraitIds = $portraits->pluck('id')->all();

    // Filter the user's selections to keep only the items with valid IDs
    // This removes any selections related to deleted portraits (e.g., 1-27)
    $orderableQuantities = array_intersect_key($finalQuantities, array_flip($validPortraitIds));

    // After filtering, check if any items are left. Maybe the user ONLY selected deleted items.
    if (empty($orderableQuantities)) {
        return back()->withInput()
            ->withErrors(['quantities' => 'The portraits you selected are no longer available. Please update your selection.']);
    }

    // If we had to remove some items, prepare a friendly warning message for the user.
    if (count($orderableQuantities) < count($finalQuantities)) {
        session()->flash('warning', 'Please note: One or more portraits in your cart were unavailable and have been removed from your order.');
    }


    // ========================================================================
    // 5. Compute costs USING ONLY THE VALID, ORDERABLE ITEMS
    // ========================================================================
    $totalUnits = array_sum($orderableQuantities);
    $unitPrice  = $totalUnits >= 5 ? 190 : 250;
    $subtotal   = $totalUnits * $unitPrice;
    $deliveryFee = 300;
    $totalPrice  = $subtotal + $deliveryFee;


    // ========================================================================
    // 6. Save order USING ONLY THE VALID, ORDERABLE ITEMS
    // ========================================================================
    $order = Order::create([
        'name'        => $validated['name'],
        'phone'       => $validated['phone'],
        'location'    => $validated['location'],
        'items'       => $orderableQuantities, // Use the filtered list
        'total_price' => $totalPrice,
    ]);

    // 7. Generate WhatsApp URL and redirect
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
