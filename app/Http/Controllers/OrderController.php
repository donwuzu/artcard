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
            'currency'           => 'required|string|in:KES,UGX,TZS,RWF',
            'portraitSelections' => 'required|json', // This is now required and must be valid JSON
        ]);

        // 2) Decode selections and keep only positive integer quantities
        try {
            $selections = json_decode($validated['portraitSelections'], true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return back()->withInput()->withErrors(['portraitSelections' => 'Invalid cart data. Please try again.']);
        }

        if (!is_array($selections)) {
            $selections = [];
        }

        $finalQuantities = [];
        $MAX_QTY_PER_ITEM = 1000; // adjust if you like

        foreach ($selections as $id => $qty) {
            $id  = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $qty = filter_var($qty, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => $MAX_QTY_PER_ITEM]]);
            if ($id && $qty) {
                $finalQuantities[$id] = $qty;
            }
        }


        // 3) Ensure order is not empty
            if (empty($finalQuantities)) {
                return back()->withInput()->withErrors([
                    'quantities' => 'Please select at least one portrait.'
                ]);
            }

            // 4) Validate submitted portrait IDs exist in the database
            $submittedPortraitIds = array_keys($finalQuantities);
            $portraits = Portrait::whereIn('id', $submittedPortraitIds)->get();

            // If DB returned fewer items than submitted, some IDs were invalid
            if ($portraits->count() !== count($finalQuantities)) {
                $validPortraitIds   = $portraits->pluck('id')->all();
                $orderableQuantities = array_intersect_key($finalQuantities, array_flip($validPortraitIds));

                if (empty($orderableQuantities)) {
                    return back()->withInput()->withErrors([
                        'quantities' => 'The portraits you selected are no longer available. Please update your selection.'
                    ]);
                }

                // Warn user that some invalid items were removed
                session()->flash(
                    'warning',
                    'Please note: One or more portraits in your cart were unavailable and have been removed from your order.'
                );
            } else {
                // All IDs valid
                $orderableQuantities = $finalQuantities;
            }

      // 5. Compute costs using ONLY THE VALID, ORDERABLE ITEMS.
            $totalUnits = array_sum($orderableQuantities);

            // Get currency (already validated earlier)
            $currency = $request->input('currency', 'KES');

            // Pricing table per currency
            $pricing = [
                'KES' => ['tier1' => 250,   'tier2' => 190,   'delivery' => 300],
                'UGX' => ['tier1' => 20000, 'tier2' => 15000, 'delivery' => 10000],
                'TZS' => ['tier1' => 5000,  'tier2' => 4000,  'delivery' => 3000],
                'RWF' => ['tier1' => 2500,  'tier2' => 2000,  'delivery' => 1500],
            ];

            // Select correct unit price and delivery fee
            $unitPrice   = $totalUnits >= 5 
                ? $pricing[$currency]['tier2'] 
                : $pricing[$currency]['tier1'];

            $deliveryFee = $pricing[$currency]['delivery'];
            $subtotal    = $totalUnits * $unitPrice;
            $totalPrice  = $subtotal + $deliveryFee;


        // 6. Save the order.
            $order = Order::create([
            'name'        => $validated['name'],
            'phone'       => $validated['phone'],
            'location'    => $validated['location'],
            'items'       => $orderableQuantities,
            'total_price' => $totalPrice,
            'currency'    => $currency, // now we store cleanly
         ]);

        // 7. Generate WhatsApp URL and redirect.
        $whatsappUrl = $this->sendWhatsappNotification($order, $portraits, $totalUnits, $subtotal, $deliveryFee);

        session()->flash('success', 'Order placed! Redirecting to WhatsApp.');
        return redirect()->away($whatsappUrl);
    }
    
    protected function sendWhatsappNotification($order, $portraits, $totalUnits, $subtotal, $deliveryFee)
    {
        $adminPhone = '254738269376';

         // Consistent formatting helper
    $format = function ($amount) use ($order) {
        return $order->currency . ' ' . number_format($amount);
    };
    
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
    $message .= "• Subtotal: " . $format($subtotal) . "\n";
    $message .= "• Delivery Fee: " . $format($deliveryFee) . "\n";
    $message .= "• *TOTAL DUE: " . $format($order->total_price) . "*\n";

        $message .= "\nOrder Time: " . now('Africa/Nairobi')->format('Y-m-d h:i A');

        return "https://wa.me/{$adminPhone}?text=" . rawurlencode($message);
    }


    public function showCart()
{
    return view('cart.index');
}

}
