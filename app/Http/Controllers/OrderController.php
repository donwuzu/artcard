<?php

namespace App\Http\Controllers;

use App\Models\Portrait;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'location' => 'required|string|max:255',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:0' // Ensures individual quantities are integers and not negative
        ]);

        // 2. Filter out zero quantities
        $selectedQuantities = array_filter($validated['quantities'], function($qty) {
            return $qty > 0;
        });

        if (empty($selectedQuantities)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['quantities' => 'Please select at least one portrait with a quantity greater than zero.']);
        }

        // 3. Get portraits with their actual prices
        // Ensure you only fetch portraits that were actually selected
        $portraitIds = array_keys($selectedQuantities);
        $portraits = Portrait::whereIn('id', $portraitIds)->get();
        
        // Verify that all selected portrait IDs exist in the database
        if ($portraits->count() !== count($selectedQuantities)) {
             // Log this error for debugging, as it indicates a potential issue or tampering
            Log::error('Invalid portrait selection detected.', [
                'selected_ids' => $portraitIds,
                'found_portraits_count' => $portraits->count()
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['quantities' => 'One or more selected portraits are invalid. Please try again.']);
        }

        // 4. Calculate pricing
        $totalUnits = array_sum($selectedQuantities);
        // Using the actual prices from the database for subtotal calculation is more secure
        // The unitPrice (190 or 250) seems to be a dynamic discount rule based on total units.
        $unitPriceBasedOnTotalUnits = $totalUnits >= 5 ? 190 : 250; // This is the effective price per unit *after* discount

        $subtotal = 0;
        $orderItemsData = [];

        // It's crucial to use the database price for each item and then apply the bulk discount logic if that's the intention
        // The current code implies a flat unit price for all items if a bulk threshold is met.
        // Let's clarify the pricing:
        // Option A: Each portrait has its own price, and $unitPrice (190/250) is a separate general discount.
        // Option B: All selected portraits adopt the $unitPrice (190/250) if totalUnits >= 5.
        // The current code implements Option B for the subtotal calculation.
        // $subtotal = $totalUnits * $unitPriceBasedOnTotalUnits;

        // If the intention was to sum individual portrait prices stored in the DB and then potentially apply a discount,
        // the logic would be different. The current code seems to imply a flat rate based on quantity.
        // For the 'items' stored, it's good to store the quantity for each portrait ID.
        // The price used in the WhatsApp message (`$portrait->price`) is the individual portrait price from DB.
        // Let's ensure `unit_price` in the Order table reflects the `unitPriceBasedOnTotalUnits`.

        $subtotal = $totalUnits * $unitPriceBasedOnTotalUnits;
        $deliveryFee = 300;
        $total = $subtotal + $deliveryFee;

        // Prepare items for storing in the Order table.
        // Store quantity associated with each portrait ID.
        $itemsToStore = $selectedQuantities;


        // 5. Create order
        $order = Order::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'location' => $validated['location'],
            'items' => $itemsToStore, // Stores [portrait_id => quantity]
            'unit_price' => $unitPriceBasedOnTotalUnits, // The effective unit price after volume discount
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total_price' => $total
        ]);

        // 6. Generate WhatsApp notification URL
        $whatsappUrl = $this->sendWhatsappNotification($order, $portraits);

        // 7. Redirect to WhatsApp
        // Set a flash message. This message will be available on the next request
        // if the user navigates back to your site or if the redirect fails.
        session()->flash('success', 'Order placed successfully! You are being redirected to WhatsApp to send the order details to the admin.');

        // Redirect to the external WhatsApp URL
        return redirect()->away($whatsappUrl);

        // The following line will no longer be reached if the WhatsApp URL is generated:
        // return redirect()->route('home')->with('success', 'Order placed successfully!');
    }

    protected function sendWhatsappNotification($order, $portraits)
    {
        $adminPhone = '254784857383'; // Format: country code + number without +
        
        // Build message
        $message = " *NEW PORTRAIT ORDER* \n\n";
        $message .= " *Customer Details*\n";
        $message .= "• Name: {$order->name}\n";
        $message .= "• Phone: {$order->phone}\n";
        $message .= "• Location: {$order->location}\n\n";
        
        $message .= " *Order Summary*\n";
        // Assuming $order->items is an associative array [portrait_id => quantity]
        // And $portraits is a collection of Portrait models keyed by their ID or iterated through.
        // To ensure correct association, let's build a quick lookup for portraits by ID.
        $portraitsById = $portraits->keyBy('id');

        foreach ($order->items as $portraitId => $qty) {
            if ($qty > 0) { // Ensure we only list items with quantity > 0
                $portrait = $portraitsById->get($portraitId);
                if ($portrait) {
                    // The $portrait->price here is the original price from the database.
                    // The $order->unit_price is the potentially discounted price per unit.
                    // Clarify which price to show per line item in WhatsApp.
                    // For clarity, showing original price and then overall discounted subtotal might be best.
                    $message .= "• Portrait \"{$portrait->name}\" (ID: {$portrait->id}) × {$qty} \n";
                } else {
                    // This case should ideally not happen if validation is correct
                    $message .= "• Portrait #{$portraitId} × {$qty} (Details unavailable)\n";
                }
            }
        }
        
        $message .= "\n";
        $message .= " *Order Totals*\n";
        $message .= "• Total Items: " . array_sum($order->items) . "\n";
     
       

        $message .= "• *TOTAL DUE: KSh {$order->total_price}*\n\n";
        $orderTimeEAT = $order->created_at->setTimezone('Africa/Nairobi')->format('Y-m-d h:i A');
        $message .= " Order Time (EAT): " . $orderTimeEAT;
        // Encode message for URL
        $encodedMessage = rawurlencode($message);
        
        // Create WhatsApp link
        $whatsappUrl = "https://wa.me/{$adminPhone}?text={$encodedMessage}";
        
        // Return URL
        return $whatsappUrl;
    }
}