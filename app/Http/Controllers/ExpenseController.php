<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest()
            ->get();

        return view('expenses.index', compact('orders'));
    }

    public function toggleStatus(Order $order)
    {
        $order->update([
            'status' => $order->status === 'paid' ? 'unpaid' : 'paid',
        ]);

        return back()->with('success', 'Order status updated.');
    }

    public function report(Request $request)
    {
        $orders = Order::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest()
            ->get();

        return view('expenses.report', compact('orders'));
    }

    public function destroy(Order $order)
    {
        $order->delete();

       return redirect()
    ->route('expenses.index')
    ->with('success', 'Order deleted successfully.');

    }
}
