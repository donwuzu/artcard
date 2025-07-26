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
        // 1. Validate the request. We only care about the user details
        // and the final JSON string from our JavaScript.
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'phone'              => 'required|string|max:20',
            'location'           => 'required|string|max:255',
            'portraitSelections' => 'required|json', // This is now required and must be valid JSON
        ]);

        // 2. Decode the selections and filter for positive quantities.
        // This is now our SINGLE SOURCE OF TRUTH. No merging needed.
        $selections = json_decode($validated['portraitSelections'], true);
        
        $finalQuantities = [];
        foreach ($selections as $id => $qty) {
            if ((int)$qty > 0) {
                $finalQuantities[(int)$id] = (int)$qty;
            }
        }

        // 3. Check for an empty order.
        if (empty($finalQuantities)) {
            // This might happen if the JSON was just '{}' or contained only zero quantities.
            return back()->withInput()->withErrors(['quantities' => 'Please select at least one portrait.']);
        }

        // 4. Validate that all submitted portrait IDs exist in the database.
        // This logic remains crucial for data integrity.
        $submittedPortraitIds = array_keys($finalQuantities);
        $portraits = Portrait::whereIn('id', $submittedPortraitIds)->get();

        // If the number of portraits found in the DB doesn't match what was submitted,
        // it means some IDs were invalid (e.g., from old localStorage data).
        if ($portraits->count() !== count($finalQuantities)) {
            // Gracefully filter the submitted items to only include what's valid.
            $validPortraitIds = $portraits->pluck('id')->all();
            $orderableQuantities = array_intersect_key($finalQuantities, array_flip($validPortraitIds));

            // If, after filtering, the cart is empty, return an error.
            if (empty($orderableQuantities)) {
                return back()->withInput()->withErrors(['quantities' => 'The portraits you selected are no longer available. Please update your selection.']);
            }
            
            // If some items were dropped, inform the user.
            session()->flash('warning', 'Please note: One or more portraits in your cart were unavailable and have been removed from your order.');
        } else {
            // If all IDs were valid, the orderable list is the same as the final list.
            $orderableQuantities = $finalQuantities;
        }

        // 5. Compute costs using ONLY THE VALID, ORDERABLE ITEMS.
        $totalUnits = array_sum($orderableQuantities);
        $unitPrice  = $totalUnits >= 5 ? 190 : 250;
        $subtotal   = $totalUnits * $unitPrice;
        $deliveryFee = 300;
        $totalPrice  = $subtotal + $deliveryFee;

        // 6. Save the order.
        $order = Order::create([
            'name'        => $validated['name'],
            'phone'       => $validated['phone'],
            'location'    => $validated['location'],
            'items'       => $orderableQuantities, // Use the final, clean list
            'total_price' => $totalPrice,
        ]);

        // 7. Generate WhatsApp URL and redirect.
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


    public function showCart()
{
    return view('cart.index');
}

}
