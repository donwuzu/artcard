<?php

namespace App\Http\Controllers;

use App\Models\Decor;
use App\Models\DecorOrder;
use Illuminate\Http\Request;

class DecorOrderController extends Controller
{
    public function store(Request $request)
    {
        // 1️⃣ Validate request
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'required|string|max:20',
            'location'        => 'required|string|max:255',
            'currency'        => 'required|string|in:KES,UGX,TZS,RWF',
            'decorSelections' => 'required|json',
        ]);

        // 2️⃣ Decode selections safely
        try {
            $selections = json_decode(
                $validated['decorSelections'],
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            return back()->withInput()->withErrors([
                'decorSelections' => 'Invalid cart data. Please try again.'
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
                'quantities' => 'Please select at least one decor.'
            ]);
        }

        // 4️⃣ Validate Decor IDs exist
        $submittedIds = array_keys($finalQuantities);

        $decors = Decor::whereIn('id', $submittedIds)->get();

        if ($decors->count() !== count($finalQuantities)) {

            $validIds = $decors->pluck('id')->all();

            $orderableQuantities = array_intersect_key(
                $finalQuantities,
                array_flip($validIds)
            );

            if (empty($orderableQuantities)) {
                return back()->withInput()->withErrors([
                    'quantities' => 'The decor you selected are no longer available.'
                ]);
            }

            session()->flash(
                'warning',
                'Some unavailable decor were removed from your cart.'
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

    $portraitPricing = $currencyPricing['decors'] ?? null;

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
        $order = DecorOrder::create([
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
            $decors,
            $totalUnits,
            $subtotal,
            $deliveryFee
        );

        session()->flash('success', 'Order placed! Redirecting to WhatsApp.');

        return redirect()->away($whatsappUrl);
    }

    protected function sendWhatsappNotification(
        $order,
        $decors,
        $totalUnits,
        $subtotal,
        $deliveryFee
    ) {

        $adminPhone = '256781716748';

        $format = function ($amount) use ($order) {
            return $order->currency . ' ' . number_format($amount);
        };

        $message = "*NEW DECOR ORDER*\n\n";

        $message .= "*Customer Details*\n";
        $message .= "• Name: {$order->name}\n";
        $message .= "• Phone: {$order->phone}\n";
        $message .= "• Location: {$order->location}\n\n";

        $message .= "*Order Summary*\n";

        $decorsById = $decors->keyBy('id');

        foreach ($order->items as $decorId => $qty) {

            if ($qty > 0) {

                $decor = $decorsById->get($decorId);

                if ($decor) {
                    $message .= "• Decor \"{$decor->name}\" (ID: {$decor->id}) × {$qty}\n";
                } else {
                    $message .= "• Decor #{$decorId} × {$qty}\n";
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