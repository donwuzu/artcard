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
        'portraitSelections' => 'nullable|json',
        'clockSelections'    => 'nullable|json',
    ]);

    $portraitSelections = json_decode($validated['portraitSelections'] ?? '{}', true);
    $clockSelections = json_decode($validated['clockSelections'] ?? '{}', true);

    $orders = [];
    $grandSubtotal = 0;
    $deliveryFee = 0;

    /** ---------------- Portraits ---------------- */
    $portraitQuantities = collect($portraitSelections)->map(fn($q) => (int) $q)->filter(fn($q) => $q > 0);
    $portraitTotalUnits = $portraitQuantities->sum();

    if ($portraitTotalUnits > 0) {
        $validPortraits = Portrait::whereIn('id', $portraitQuantities->keys())->get()->keyBy('id');
        $unitPrice = $portraitTotalUnits >= 5 ? 190 : 250;
        $subtotal = $unitPrice * $portraitTotalUnits;
        $grandSubtotal += $subtotal;

        $order = Order::create([
            'name'        => $validated['name'],
            'phone'       => $validated['phone'],
            'location'    => $validated['location'],
            'items'       => $portraitQuantities,
            'total_price' => $subtotal, // delivery handled globally
        ]);

        $orders['portraits'] = [
            'model' => $order,
            'items' => $validPortraits,
            'quantities' => $portraitQuantities,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
        ];
    }

    /** ---------------- Clocks ---------------- */
    $clockQuantities = collect($clockSelections)->map(fn($q) => (int) $q)->filter(fn($q) => $q > 0);
    $clockTotalUnits = $clockQuantities->sum();

    if ($clockTotalUnits > 0) {
        $validClocks = PortraitClock::whereIn('id', $clockQuantities->keys())->get()->keyBy('id');
        $unitPrice = $clockTotalUnits >= 5 ? 500 : 700;
        $subtotal = $unitPrice * $clockTotalUnits;
        $grandSubtotal += $subtotal;

        $clockOrder = ClockOrder::create([
            'name'        => $validated['name'],
            'phone'       => $validated['phone'],
            'location'    => $validated['location'],
            'items'       => $clockQuantities,
            'total_price' => $subtotal,
        ]);

        $orders['clocks'] = [
            'model' => $clockOrder,
            'items' => $validClocks,
            'quantities' => $clockQuantities,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
        ];
    }

    if (empty($orders)) {
        return back()->withInput()->withErrors(['quantities' => 'Please select at least one item to order.']);
    }

    $deliveryFee = 300;
    $finalTotal = $grandSubtotal + $deliveryFee;

    /** ---------------- WhatsApp Message ---------------- */
    $message = "*NEW CART ORDER (Mixed)*\n\n";
    $message .= "*Customer Details:*\n";
    $message .= "• Name: {$validated['name']}\n";
    $message .= "• Phone: {$validated['phone']}\n";
    $message .= "• Location: {$validated['location']}\n\n";

    foreach ($orders as $type => $data) {
        $sectionTitle = $type === 'portraits' ? 'Portraits' : 'Clocks';
        $message .= "*{$sectionTitle}:*\n";
        foreach ($data['quantities'] as $id => $qty) {
            $item = $data['items']->get($id);
            $label = $item?->name ?? "#$id";
            $message .= "• {$label} × {$qty}\n";
        }
        $message .= "Subtotal: KSh " . number_format($data['subtotal']) . "\n\n";
    }

    $message .= "*Delivery Fee:* KSh " . number_format($deliveryFee) . "\n";
    $message .= "*Total: KSh " . number_format($finalTotal) . "*\n";
    $message .= "Time: " . now('Africa/Nairobi')->format('Y-m-d h:i A');

    $adminPhone = '254738269376';
    $whatsappUrl = "https://wa.me/{$adminPhone}?text=" . rawurlencode($message);

    session()->flash('success', 'Order placed! Redirecting to WhatsApp.');
    return redirect()->away($whatsappUrl);
}

}
