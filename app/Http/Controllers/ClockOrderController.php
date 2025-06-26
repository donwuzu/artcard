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
            'clockSelections'    => 'required|json',
        ]);

        // 2. Decode and filter positive quantities.
        $selections = json_decode($validated['clockSelections'], true);

        $finalQuantities = [];
        foreach ($selections as $id => $qty) {
            if ((int)$qty > 0) {
                $finalQuantities[(int)$id] = (int)$qty;
            }
        }

        // 3. Empty order check.
        if (empty($finalQuantities)) {
            return back()->withInput()->withErrors(['quantities' => 'Please select at least one portrait clock.']);
        }

        // 4. Validate clock IDs exist.
        $submittedClockIds = array_keys($finalQuantities);
        $clocks = PortraitClock::whereIn('id', $submittedClockIds)->get();

        if ($clocks->count() !== count($finalQuantities)) {
            $validClockIds = $clocks->pluck('id')->all();
            $orderableQuantities = array_intersect_key($finalQuantities, array_flip($validClockIds));

            if (empty($orderableQuantities)) {
                return back()->withInput()->withErrors(['quantities' => 'Selected clocks are unavailable.']);
            }

            session()->flash('warning', 'Some selected clocks were removed due to unavailability.');
        } else {
            $orderableQuantities = $finalQuantities;
        }

        // 5. Compute cost.
        $totalUnits = array_sum($orderableQuantities);
        $unitPrice  = $totalUnits >= 5 ? 1900 : 2500;
        $subtotal   = $totalUnits * $unitPrice;
        $deliveryFee = 300;
        $totalPrice  = $subtotal + $deliveryFee;

        // 6. Store order.
        $order = ClockOrder::create([
            'name'        => $validated['name'],
            'phone'       => $validated['phone'],
            'location'    => $validated['location'],
            'items'       => $orderableQuantities,
            'total_price' => $totalPrice,
        ]);

        // 7. WhatsApp redirect.
        $whatsappUrl = $this->sendWhatsappNotification($order, $clocks, $totalUnits, $subtotal, $deliveryFee);

        session()->flash('success', 'Order placed! Redirecting to WhatsApp.');
        return redirect()->away($whatsappUrl);
    }

    protected function sendWhatsappNotification($order, $clocks, $totalUnits, $subtotal, $deliveryFee)
    {
        $adminPhone = '254738269376';

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
        $message .= "• Subtotal: KSh {$subtotal}\n";
        $message .= "• Delivery Fee: KSh {$deliveryFee}\n";
        $message .= "• *TOTAL DUE: KSh {$order->total_price}*\n";

        $message .= "\nOrder Time: " . now('Africa/Nairobi')->format('Y-m-d h:i A');

        return "https://wa.me/{$adminPhone}?text=" . rawurlencode($message);
    }
}
