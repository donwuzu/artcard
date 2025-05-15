<?php

namespace App\Http\Controllers;

use App\Models\Order;

use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
{
    $query = Order::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('name', 'like', "%$search%")
              ->orWhere('phone', 'like', "%$search%");
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $orders = $query->latest()->get();

    return view('expenses.index', compact('orders'));
}

public function toggleStatus(Order $order)
{
    $order->status = $order->status === 'paid' ? 'unpaid' : 'paid';
    $order->save();

    return redirect()->back()->with('success', 'Order status updated.');
}


}
