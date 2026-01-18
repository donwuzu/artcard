<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ClockOrder;
use App\Models\Portrait;
use App\Models\PortraitClock;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show()
{
    return view('cart.index');
}

  public function store(Request $request)
{
    $validated = $request->validate([
        'name'               => 'required|string|max:255',
        'phone'              => 'required|string|max:20',
        'location'           => 'required|string|max:255',
        'currency'           => 'required|string|in:KES,UGX,TZS,RWF',
        'portraitSelections' => 'nullable|json',
        'clockSelections'    => 'nullable|json',
    ]);

    $portraitSelections = json_decode($validated['portraitSelections'] ?? '{}', true);
    $clockSelections    = json_decode($validated['clockSelections'] ?? '{}', true);

    $orders = [];
    $grandSubtotal = 0;

    /** ---------------- Currency Config ---------------- */
    $pricing = [
        'KES' => [ 'symbol' => 'KSh', 'portraits' => [250, 190], 'clocks' => [700, 500], 'delivery' => 300 ],
        'UGX' => [ 'symbol' => 'UGX', 'portraits' => [20000, 15000], 'clocks' => [50000, 38000], 'delivery' => 10000 ],
        'TZS' => [ 'symbol' => 'TSh', 'portraits' => [5000, 4000], 'clocks' => [45000, 32000], 'delivery' => 3000 ],
        'RWF' => [ 'symbol' => 'FRw', 'portraits' => [2500, 2000], 'clocks' => [20000, 12000], 'delivery' => 1500 ],
    ];
    $cfg     = $pricing[$validated['currency']];
    $symbol  = $cfg['symbol'];

    /** ---------------- Portraits ---------------- */
    $portraitQuantities = collect($portraitSelections)->map(fn($q) => (int) $q)->filter(fn($q) => $q > 0);
    $portraitTotalUnits = $portraitQuantities->sum();

    if ($portraitTotalUnits > 0) {
        $validPortraits = Portrait::whereIn('id', $portraitQuantities->keys())->get()->keyBy('id');
        $unitPrice = $portraitTotalUnits >= 5 ? $cfg['portraits'][1] : $cfg['portraits'][0];
        $subtotal  = $unitPrice * $portraitTotalUnits;
        $grandSubtotal += $subtotal;

        $order = Order::create([
            'name'        => $validated['name'],
            'phone'       => $validated['phone'],
            'location'    => $validated['location'],
            'currency'    => $validated['currency'], // ✅ persist currency
            'items'       => $portraitQuantities,
            'total_price' => $subtotal,
        ]);

        $orders['portraits'] = compact('order', 'validPortraits', 'portraitQuantities', 'unitPrice', 'subtotal');
    }

    /** ---------------- Clocks ---------------- */
    $clockQuantities = collect($clockSelections)->map(fn($q) => (int) $q)->filter(fn($q) => $q > 0);
    $clockTotalUnits = $clockQuantities->sum();

    if ($clockTotalUnits > 0) {
        $validClocks = PortraitClock::whereIn('id', $clockQuantities->keys())->get()->keyBy('id');
        $unitPrice   = $clockTotalUnits >= 5 ? $cfg['clocks'][1] : $cfg['clocks'][0];
        $subtotal    = $unitPrice * $clockTotalUnits;
        $grandSubtotal += $subtotal;

        $clockOrder = ClockOrder::create([
            'name'        => $validated['name'],
            'phone'       => $validated['phone'],
            'location'    => $validated['location'],
            'currency'    => $validated['currency'], // ✅ persist currency
            'items'       => $clockQuantities,
            'total_price' => $subtotal,
        ]);

        $orders['clocks'] = compact('clockOrder', 'validClocks', 'clockQuantities', 'unitPrice', 'subtotal');
    }

    if (empty($orders)) {
        return back()->withInput()->withErrors(['quantities' => 'Please select at least one item to order.']);
    }

    /** ---------------- Delivery + Totals ---------------- */
    $deliveryFee = $cfg['delivery'];
    $finalTotal  = $grandSubtotal + $deliveryFee;

    /** ---------------- WhatsApp Message ---------------- */
    $message  = "*NEW CART ORDER (Mixed)*\n\n";
    $message .= "*Customer Details:*\n";
    $message .= "• Name: {$validated['name']}\n";
    $message .= "• Phone: {$validated['phone']}\n";
    $message .= "• Location: {$validated['location']}\n\n";

    foreach ($orders as $type => $data) {
        $sectionTitle = $type === 'portraits' ? 'Portraits' : 'Clocks';
        $message .= "*{$sectionTitle}:*\n";
        foreach ($data[$type === 'portraits' ? 'portraitQuantities' : 'clockQuantities'] as $id => $qty) {
            $item  = $data[$type === 'portraits' ? 'validPortraits' : 'validClocks']->get($id);
            $label = $item?->name ?? "#$id";
            $message .= "• {$label} × {$qty}\n";
        }
        $message .= "Subtotal: {$symbol} " . number_format($data['subtotal']) . "\n\n";
    }

    $message .= "*Delivery Fee:* {$symbol} " . number_format($deliveryFee) . "\n";
    $message .= "*Total: {$symbol} " . number_format($finalTotal) . "*\n";
    $message .= "Time: " . now('Africa/Nairobi')->format('Y-m-d h:i A');

    $adminPhone  = '254758922923';
    $whatsappUrl = "https://wa.me/{$adminPhone}?text=" . rawurlencode($message);

    session()->flash('success', 'Order placed! Redirecting to WhatsApp.');
    return redirect()->away($whatsappUrl);
}


}
