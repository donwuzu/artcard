<?php

namespace App\Http\Controllers;

use App\Models\PortraitClock;
use App\Models\ClockOrder;
use Illuminate\Http\Request;

class ClockOrderController extends Controller
{
    public function store(Request $request)
    {
        // 1️⃣ Validate request
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'required|string|max:20',
            'location'        => 'required|string|max:255',
            'currency'        => 'required|string|in:KES,UGX,TZS,RWF',
            'clockSelections' => 'required|json',
        ]);

        // 2️⃣ Decode selections safely
        try {
            $selections = json_decode(
                $validated['clockSelections'],
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            return back()->withInput()->withErrors([
                'clockSelections' => 'Invalid cart data. Please try again.'
            ]);
        }

        if (!is_array($selections)) {
            $selections = [];
        }

        $finalQuantities = [];
        $MAX_QTY_PER_ITEM = 1000;

        foreach ($selections as $id => $qty) {

            $id = filter_var($id, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1]
            ]);

            $qty = filter_var($qty, FILTER_VALIDATE_INT, [
                'options' => [
                    'min_range' => 1,
                    'max_range' => $MAX_QTY_PER_ITEM
                ]
            ]);

            if ($id && $qty) {
                $finalQuantities[$id] = $qty;
            }
        }

        // 3️⃣ Prevent empty orders
        if (empty($finalQuantities)) {
            return back()->withInput()->withErrors([
                'quantities' => 'Please select at least one clock.'
            ]);
        }

        // 4️⃣ Validate clock IDs exist
        $submittedIds = array_keys($finalQuantities);

        $clocks = PortraitClock::whereIn('id', $submittedIds)->get();

        if ($clocks->count() !== count($finalQuantities)) {

            $validIds = $clocks->pluck('id')->all();

            $orderableQuantities = array_intersect_key(
                $finalQuantities,
                array_flip($validIds)
            );

            if (empty($orderableQuantities)) {
                return back()->withInput()->withErrors([
                    'quantities' => 'The clocks you selected are no longer available.'
                ]);
            }

            session()->flash(
                'warning',
                'Some unavailable clocks were removed from your cart.'
            );

        } else {
            $orderableQuantities = $finalQuantities;
        }

         // 5️⃣ Pricing (FROM CONFIG — SINGLE SOURCE OF TRUTH)
    $currency       = $validated['currency'];
    $pricing        = config('pricing.currencies');
    $bulkThreshold  = config('pricing.bulk_threshold');

    $totalUnits = array_sum($orderableQuantities);



    $currencyPricing = $pricing[$currency] ?? null;

    if (!$currencyPricing) {
        return back()->withErrors(['currency' => 'Invalid currency selected.']);
    }

    $portraitPricing = $currencyPricing['clocks'] ?? null;

    if (!$portraitPricing || !isset($portraitPricing['tier1'], $portraitPricing['tier2'])) {
        return back()->withErrors(['pricing' => 'Portrait pricing not configured properly.']);
    }

    $unitPrice = $totalUnits >= $bulkThreshold
        ? $portraitPricing['tier2']
        : $portraitPricing['tier1'];

    $deliveryFee = (int) ($currencyPricing['delivery'] ?? 0);

    $subtotal = $totalUnits * $unitPrice;
    $totalPrice = $subtotal + $deliveryFee;


        // 6️⃣ Save order
        $order = ClockOrder::create([
            'name'        => $validated['name'],
            'phone'       => $validated['phone'],
            'location'    => $validated['location'],
            'items'       => $orderableQuantities,
            'total_price' => $totalPrice,
            'currency'    => $currency,
        ]);

        // 7️⃣ Generate WhatsApp message
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

    protected function sendWhatsappNotification(
        $order,
        $clocks,
        $totalUnits,
        $subtotal,
        $deliveryFee
    ) {

        $adminPhone = '256781716748';

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
                    $message .= "• Clock #{$clockId} × {$qty}\n";
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