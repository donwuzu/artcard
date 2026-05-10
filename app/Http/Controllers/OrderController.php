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
    // 1️⃣ Validate request
    $validated = $request->validate([
        'name'               => 'required|string|max:255',
        'phone'              => 'required|string|max:20',
        'location'           => 'required|string|max:255',
        'currency'           => 'required|string|in:KES,UGX,TZS,RWF',
        'portraitSelections' => 'required|json',
    ]);

    // 2️⃣ Decode and sanitize cart
    try {
        $selections = json_decode(
            $validated['portraitSelections'],
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    } catch (\JsonException $e) {
        return back()->withInput()->withErrors([
            'portraitSelections' => 'Invalid cart data. Please try again.'
        ]);
    }

    if (!is_array($selections)) {
        $selections = [];
    }

    $finalQuantities = [];
    $MAX_QTY_PER_ITEM = 1000;

    foreach ($selections as $id => $qty) {
        $id  = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
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

    // 3️⃣ Ensure order is not empty
    if (empty($finalQuantities)) {
        return back()->withInput()->withErrors([
            'quantities' => 'Please select at least one portrait.'
        ]);
    }

    // 4️⃣ Validate portrait IDs exist
    $submittedIds = array_keys($finalQuantities);
    $portraits = Portrait::whereIn('id', $submittedIds)->get();

    if ($portraits->count() !== count($finalQuantities)) {

        $validIds = $portraits->pluck('id')->all();
        $orderableQuantities = array_intersect_key(
            $finalQuantities,
            array_flip($validIds)
        );

        if (empty($orderableQuantities)) {
            return back()->withInput()->withErrors([
                'quantities' => 'The portraits you selected are no longer available.'
            ]);
        }

        session()->flash(
            'warning',
            'Some unavailable portraits were removed from your cart.'
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

    $portraitPricing = $currencyPricing['portraits'] ?? null;

    if (!$portraitPricing || !isset($portraitPricing['tier1'], $portraitPricing['tier2'])) {
        return back()->withErrors(['pricing' => 'Portrait pricing not configured properly.']);
    }

    $unitPrice = $totalUnits >= $bulkThreshold
        ? $portraitPricing['tier2']
        : $portraitPricing['tier1'];

    $deliveryFee = (int) ($currencyPricing['delivery'] ?? 0);

    $subtotal = $totalUnits * $unitPrice;
    $totalPrice = $subtotal + $deliveryFee;



    // 6️⃣ Save Order
    $order = Order::create([
        'name'        => $validated['name'],
        'phone'       => $validated['phone'],
        'location'    => $validated['location'],
        'items'       => $orderableQuantities,
        'total_price' => $totalPrice,
        'currency'    => $currency,
    ]);

    // 7️⃣ Generate WhatsApp URL
    $whatsappUrl = $this->sendWhatsappNotification(
        $order,
        $portraits,
        $totalUnits,
        $subtotal,
        $deliveryFee
    );

    session()->flash('success', 'Order placed! Redirecting to WhatsApp.');

    return redirect()->away($whatsappUrl);
}

    
    protected function sendWhatsappNotification($order, $portraits, $totalUnits, $subtotal, $deliveryFee)
    {
        $adminPhone = '256781716748';

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
