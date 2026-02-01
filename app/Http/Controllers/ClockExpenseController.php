<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ClockOrder;

class ClockExpenseController extends Controller
{
      public function index(Request $request)
    {
        $query = ClockOrder::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $clockOrders = $query->latest()->get();

        return view('admin.clockExpenses.index', compact('clockOrders'));
    }

    public function toggleStatus(ClockOrder $clockOrder)
    {
        $clockOrder->status = $clockOrder->status === 'paid' ? 'unpaid' : 'paid';
        $clockOrder->save();

        return redirect()->back()->with('success', 'Clock order status updated.');
    }

    public function report(Request $request)
    {
        $query = ClockOrder::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $clockOrders = $query->latest()->get();

        return view('admin.clockExpenses.report', compact('clockOrders'));
    }

    public function toggleStatusFromReport(ClockOrder $clockOrder)
    {
        $clockOrder->status = $clockOrder->status === 'paid' ? 'unpaid' : 'paid';
        $clockOrder->save();

        return redirect()->route('admin.clockExpenses.report')->with('success', 'Clock order status updated from report.');
    }

  public function destroy(ClockOrder $clockOrder)
{
    $clockOrder->delete();

    return redirect()
        ->route('admin.clockExpenses.index')
        ->with('success', 'Clock order deleted successfully.');
}

}
