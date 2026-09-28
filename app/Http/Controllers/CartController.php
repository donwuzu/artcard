<?php

namespace App\Http\Controllers;

use App\Models\Decor;
use App\Models\DecorOrder;
use App\Models\Order;
use App\Models\Portrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private const MAX_QTY_PER_ITEM = 1000;

    public function show()
    {
        return view('cart.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'location' => ['required', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'in:KES,UGX,TZS,RWF'],
            'portraitSelections' => ['nullable', 'json'],
            'decorSelections' => ['nullable', 'json'],
            'clockSelections' => ['nullable', 'json'],
            'quantities' => ['nullable', 'array'],
        ]);

        $currency = $validated['currency'] ?? 'UGX';
        $pricing = config("pricing.currencies.{$currency}");

        if (! $pricing) {
            return back()->withInput()->withErrors([
                'currency' => 'Please choose a supported currency.',
            ]);
        }

        $portraitQuantities = $this->sanitizeSelections(
            $validated['portraitSelections'] ?? null,
            $validated['quantities'] ?? []
        );

        $decorQuantities = $this->sanitizeSelections(
            $validated['decorSelections'] ?? ($validated['clockSelections'] ?? null)
        );

        if (empty($portraitQuantities) && empty($decorQuantities)) {
            return back()->withInput()->withErrors([
                'quantities' => 'Please select at least one item before checkout.',
            ]);
        }

        [$portraits, $portraitQuantities] = $this->onlyExistingItems(Portrait::class, $portraitQuantities);
        [$decors, $decorQuantities] = $this->onlyExistingItems(Decor::class, $decorQuantities);

        if (empty($portraitQuantities) && empty($decorQuantities)) {
            return back()->withInput()->withErrors([
                'quantities' => 'The selected items are no longer available.',
            ]);
        }

        $bulkThreshold = (int) config('pricing.bulk_threshold', 5);
        $portraitSummary = $this->summarizeItems($portraitQuantities, $pricing['portraits'], $bulkThreshold);
        $decorSummary = $this->summarizeItems($decorQuantities, $pricing['decors'] ?? $pricing['clocks'], $bulkThreshold);

        $deliveryFee = ((int) $portraitSummary['units'] + (int) $decorSummary['units']) > 0
            ? (int) ($pricing['delivery'] ?? 0)
            : 0;

        $deliveryAssigned = false;
        $orders = [];

        if ($portraitSummary['units'] > 0) {
            $portraitTotal = $portraitSummary['subtotal'] + $deliveryFee;
            $deliveryAssigned = true;

            $orders['portraits'] = Order::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'location' => $validated['location'],
                'items' => $portraitQuantities,
                'total_price' => $portraitTotal,
                'currency' => $currency,
                'status' => 'unpaid',
                'is_paid' => false,
            ]);
        }

        if ($decorSummary['units'] > 0) {
            $decorTotal = $decorSummary['subtotal'] + ($deliveryAssigned ? 0 : $deliveryFee);

            $orders['decor'] = DecorOrder::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'location' => $validated['location'],
                'items' => $decorQuantities,
                'total_price' => $decorTotal,
                'currency' => $currency,
                'status' => 'unpaid',
                'is_paid' => false,
            ]);
        }

        $whatsappUrl = $this->buildWhatsappUrl(
            customer: $validated,
            currency: $currency,
            symbol: $pricing['symbol'] ?? $currency,
            portraits: $portraits,
            portraitQuantities: $portraitQuantities,
            portraitSummary: $portraitSummary,
            decors: $decors,
            decorQuantities: $decorQuantities,
            decorSummary: $decorSummary,
            deliveryFee: $deliveryFee
        );

        session()->flash('success', 'Order placed! Redirecting to WhatsApp.');
        session()->flash('created_orders', collect($orders)->map(fn ($order) => $order->getKey())->all());

        return redirect()->away($whatsappUrl);
    }

    private function sanitizeSelections(?string $jsonSelections, array $fallbackSelections = []): array
    {
        $selections = $fallbackSelections;

        if ($jsonSelections !== null) {
            try {
                $decoded = json_decode($jsonSelections, true, 512, JSON_THROW_ON_ERROR);
                $selections = is_array($decoded) ? $decoded : [];
            } catch (\JsonException) {
                $selections = [];
            }
        }

        $clean = [];

        foreach ($selections as $id => $qty) {
            $id = filter_var($id, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]);

            $qty = filter_var($qty, FILTER_VALIDATE_INT, [
                'options' => [
                    'min_range' => 1,
                    'max_range' => self::MAX_QTY_PER_ITEM,
                ],
            ]);

            if ($id && $qty) {
                $clean[$id] = $qty;
            }
        }

        return $clean;
    }

    private function onlyExistingItems(string $modelClass, array $quantities): array
    {
        if (empty($quantities)) {
            return [new Collection, []];
        }

        $items = $modelClass::whereIn('id', array_keys($quantities))->get();
        $validIds = $items->pluck('id')->all();

        return [
            $items,
            array_intersect_key($quantities, array_flip($validIds)),
        ];
    }

    private function summarizeItems(array $quantities, array $pricing, int $bulkThreshold): array
    {
        $units = array_sum($quantities);
        $unitPrice = $units >= $bulkThreshold
            ? (int) $pricing['tier2']
            : (int) $pricing['tier1'];

        return [
            'units' => $units,
            'unit_price' => $unitPrice,
            'subtotal' => $units * $unitPrice,
        ];
    }

    private function buildWhatsappUrl(
        array $customer,
        string $currency,
        string $symbol,
        Collection $portraits,
        array $portraitQuantities,
        array $portraitSummary,
        Collection $decors,
        array $decorQuantities,
        array $decorSummary,
        int $deliveryFee
    ): string {
        $message = "*NEW ARTCARD ORDER*\n\n";
        $message .= "*Customer Details*\n";
        $message .= "Name: {$customer['name']}\n";
        $message .= "Phone: {$customer['phone']}\n";
        $message .= "Location: {$customer['location']}\n\n";

        if (! empty($portraitQuantities)) {
            $message .= $this->formatOrderSection(
                title: 'Portraits',
                label: 'Portrait',
                items: $portraits,
                quantities: $portraitQuantities,
                summary: $portraitSummary,
                symbol: $symbol
            );
        }

        if (! empty($decorQuantities)) {
            $message .= $this->formatOrderSection(
                title: 'Decor',
                label: 'Decor',
                items: $decors,
                quantities: $decorQuantities,
                summary: $decorSummary,
                symbol: $symbol
            );
        }

        $subtotal = $portraitSummary['subtotal'] + $decorSummary['subtotal'];
        $total = $subtotal + $deliveryFee;

        $message .= "*Totals*\n";
        $message .= "Subtotal: {$symbol} ".number_format($subtotal)."\n";
        $message .= "Delivery: {$symbol} ".number_format($deliveryFee)."\n";
        $message .= "*TOTAL DUE: {$symbol} ".number_format($total)."*\n";
        $message .= "Currency: {$currency}\n";
        $message .= 'Order Time: '.now('Africa/Nairobi')->format('Y-m-d h:i A');

        $adminPhone = config('services.whatsapp.orders_phone', '256781716748');

        return "https://wa.me/{$adminPhone}?text=".rawurlencode($message);
    }

    private function formatOrderSection(
        string $title,
        string $label,
        Collection $items,
        array $quantities,
        array $summary,
        string $symbol
    ): string {
        $itemsById = $items->keyBy('id');
        $message = "*{$title}*\n";

        foreach ($quantities as $id => $qty) {
            $item = $itemsById->get($id);
            $name = $item?->title ?: ($item?->name ?: "{$label} #{$id}");
            $message .= "{$name} x {$qty}\n";
        }

        $message .= "Unit Price: {$symbol} ".number_format($summary['unit_price'])."\n";
        $message .= "Subtotal: {$symbol} ".number_format($summary['subtotal'])."\n\n";

        return $message;
    }
}
