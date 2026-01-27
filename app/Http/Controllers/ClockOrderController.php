<?php

namespace App\Http\Controllers;

use App\Models\PortraitClock;

use App\Models\ClockOrder;

use Illuminate\Http\Request;

class ClockOrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate request data.
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'phone'              => 'required|string|max:20',
            'location'           => 'required|string|max:255',
            'currency'           => 'required|string|in:KES,UGX,TZS,RWF',
            'clockSelections'    => 'required|json',
        ]);

         // 2) Decode selections and keep only positive integer quantities
        try {
            $selections = json_decode($validated['clockSelections'], true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return back()->withInput()->withErrors([
                'clockSelections' => 'Invalid cart data. Please try again.'
            ]);
        }

        if (!is_array($selections)) {
            $selections = [];
        }

        $finalQuantities   = [];
        $MAX_QTY_PER_ITEM  = 1000; // adjustable

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
                'quantities' => 'Please select at least one clock.'
            ]);
        }

     // 4) Validate submitted clock IDs exist in DB
        $submittedClockIds = array_keys($finalQuantities);
        $clocks = PortraitClock::whereIn('id', $submittedClockIds)->get();

        if ($clocks->count() !== count($finalQuantities)) {
            $validClockIds     = $clocks->pluck('id')->all();
            $orderableQuantities = array_intersect_key($finalQuantities, array_flip($validClockIds));

            if (empty($orderableQuantities)) {
                return back()->withInput()->withErrors([
                    'quantities' => 'The clocks you selected are no longer available. Please update your selection.'
                ]);
            }

            session()->flash(
                'warning',
                'Please note: One or more clocks in your cart were unavailable and have been removed from your order.'
            );
        } else {
            $orderableQuantities = $finalQuantities;
        }

        // 5) Compute costs
        $totalUnits = array_sum($orderableQuantities);
        $currency   = $validated['currency'];

        // Pricing table per currency
        $pricing = [
            'KES' => ['tier1' => 700,   'tier2' => 500,   'delivery' => 300],
            'UGX' => ['tier1' => 50000, 'tier2' => 38000, 'delivery' => 10000],
            'TZS' => ['tier1' => 45000, 'tier2' => 32000, 'delivery' => 3000],
            'RWF' => ['tier1' => 20000, 'tier2' => 12000, 'delivery' => 1500],
        ];

        $unitPrice   = $totalUnits >= 5 
            ? $pricing[$currency]['tier2'] 
            : $pricing[$currency]['tier1'];

        $deliveryFee = $pricing[$currency]['delivery'];
        $subtotal    = $totalUnits * $unitPrice;
        $totalPrice  = $subtotal + $deliveryFee;

       // 6) Save the order
        $order = ClockOrder::create([
            'name'        => $validated['name'],
            'phone'       => $validated['phone'],
            'location'    => $validated['location'],
            'items'       => $orderableQuantities,
            'total_price' => $totalPrice,
            'currency'    => $currency,
        ]);

          // 7) Generate WhatsApp URL + redirect
        $whatsappUrl = $this->sendWhatsappNotification(
            $order,
            $clocks,
            $totalUnits,
            $subtotal,
            $deliveryFee
        );

          session()->flash('success', 'Order placed! Redirecting to WhatsApp.');
        return redirect()->away($whatsappUrl);
    }

    protected function sendWhatsappNotification($order, $clocks, $totalUnits, $subtotal, $deliveryFee)
    {
        $adminPhone = '256781716748';


            // Consistent formatting helper
    $format = function ($amount) use ($order) {
        return $order->currency . ' ' . number_format($amount);
    };


        $message = "*NEW CLOCK ORDER*\n\n";
        $message .= "*Customer Details*\n";
        $message .= "• Name: {$order->name}\n";
        $message .= "• Phone: {$order->phone}\n";
        $message .= "• Location: {$order->location}\n\n";

        $message .= "*Order Summary*\n";
        $clocksById = $clocks->keyBy('id');

        foreach ($order->items as $clockId => $qty) {
            if ($qty > 0) {
                $clock = $clocksById->get($clockId);
                if ($clock) {
                    $message .= "• Clock \"{$clock->name}\" (ID: {$clock->id}) × {$qty}\n";
                } else {
                    $message .= "• Clock #{$clockId} × {$qty} (Unavailable details)\n";
                }
            }
        }

        $message .= "\n*Order Totals*\n";
        $message .= "• Total Items: {$totalUnits}\n";
        $message .= "• Subtotal: " . $format($subtotal) . "\n";
        $message .= "• Delivery Fee: " . $format($deliveryFee) . "\n";
        $message .= "• *TOTAL DUE: " . $format($order->total_price) . "*\n";

        $message .= "\nOrder Time: " . now('Africa/Nairobi')->format('Y-m-d h:i A');

        return "https://wa.me/{$adminPhone}?text=" . rawurlencode($message);
    }
}
