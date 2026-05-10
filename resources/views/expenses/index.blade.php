@php
    $filteredOrders = $orders;

    if (request('status')) {
        $filteredOrders = $filteredOrders->where('status', request('status'));
    }

    if (request('from_date') && request('to_date')) {
        $from = Carbon\Carbon::parse(request('from_date'))->startOfDay();
        $to = Carbon\Carbon::parse(request('to_date'))->endOfDay();
        $filteredOrders = $filteredOrders->whereBetween('created_at', [$from, $to]);
    }

    $paidTotal = $filteredOrders->where('status', 'paid')->sum('total_price');
    $unpaidTotal = $filteredOrders->where('status', 'unpaid')->sum('total_price');
@endphp

<x-app-layout>
  <x-slot name="header">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Expenses
            </h2>
            <p class="text-sm text-gray-500">
                Track and manage all expense records
            </p>
        </div>

    </div>
</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow">
                <h1 class="text-2xl font-bold mb-6">Expense Manager</h1>

        <form method="GET"
      action="{{ route('admin.expenses.index') }}"
      class="mb-6 bg-white p-4 rounded-xl shadow-sm border flex flex-col gap-4">

    {{-- SEARCH --}}
    <div class="flex flex-col gap-2 w-full">
        <label for="search" class="text-sm font-medium text-gray-600">
            Search
        </label>

        <input
            id="search"
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Name or phone..."
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none"
        >

        <button type="submit"
            class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
            Search
        </button>
    </div>

    {{-- STATUS FILTER (SCROLLABLE ON MOBILE) --}}
    <div class="flex gap-2 overflow-x-auto pb-2">

        @php $status = request('status'); @endphp

        <a href="{{ route('admin.expenses.index') }}"
           class="flex-shrink-0 px-4 py-2 rounded-full text-sm whitespace-nowrap transition
           {{ !$status ? 'bg-green-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
            All
        </a>

        <a href="{{ route('admin.expenses.index', ['status' => 'paid', 'search' => request('search')]) }}"
           class="flex-shrink-0 px-4 py-2 rounded-full text-sm whitespace-nowrap transition
           {{ $status === 'paid' ? 'bg-green-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
            Paid
        </a>

        <a href="{{ route('admin.expenses.index', ['status' => 'unpaid', 'search' => request('search')]) }}"
           class="flex-shrink-0 px-4 py-2 rounded-full text-sm whitespace-nowrap transition
           {{ $status === 'unpaid' ? 'bg-green-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">
            Unpaid
        </a>

    </div>

</form>


     <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

    {{-- UNPAID --}}
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <p class="text-sm text-red-500">
            Total Unpaid
        </p>

        <h3 class="text-2xl font-bold text-red-600 mt-1">
            KSh {{ number_format($unpaidTotal) }}
        </h3>
    </div>

    {{-- PAID --}}
    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
        <p class="text-sm text-green-600">
            Total Paid
        </p>

        <h3 class="text-2xl font-bold text-green-700 mt-1">
            KSh {{ number_format($paidTotal) }}
        </h3>
    </div>

</div>

   <div class="relative overflow-x-auto shadow-md sm:rounded-lg bg-white">

<table class="w-full text-sm text-left text-gray-600">

    {{-- HEADER --}}
    <thead class="text-xs uppercase bg-gray-100 text-gray-700 sticky top-0 z-10">
        <tr>
            <th class="px-4 py-3 text-center">Details</th>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Phone</th>
            <th class="px-4 py-3">Location</th>
            <th class="px-4 py-3">Total</th>
            <th class="px-4 py-3">Created</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Actions</th>
            <th class="px-4 py-3">Delete</th>
        </tr>
    </thead>

    {{-- BODY --}}
    <tbody class="divide-y">

    @foreach ($filteredOrders as $index => $order)

        {{-- MAIN ROW --}}
        <tr class="hover:bg-gray-50 transition">

            {{-- DETAILS TOGGLE --}}
            <td class="px-4 py-3 text-center">
                <button
                    type="button"
                    onclick="toggleOrderDetails({{ $order->id }}); event.stopPropagation();"
                    class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200">

                    <svg class="w-4 h-4 transition-transform duration-200"
                         fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M19 9l-7 7-7-7"/>
                    </svg>

                </button>
            </td>

            <td class="px-4 py-3">{{ $index + 1 }}</td>

            <td class="px-4 py-3 font-medium text-gray-800">{{ $order->name }}</td>

            <td class="px-4 py-3">{{ $order->phone }}</td>

            <td class="px-4 py-3">{{ $order->location }}</td>

            <td class="px-4 py-3 whitespace-nowrap">
                {{ $order->currency }} {{ number_format($order->total_price) }}
            </td>

            <td class="px-4 py-3 whitespace-nowrap">
                {{ $order->created_at->format('d M Y, h:i A') }}
            </td>

            {{-- STATUS BADGE --}}
            <td class="px-4 py-3">
                <span class="px-2 py-1 text-xs rounded-full
                    {{ $order->status === 'paid'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-600' }}">
                    {{ $order->status ?? 'unpaid' }}
                </span>
            </td>

            {{-- TOGGLE PAID/UNPAID --}}
            <td class="px-4 py-3">
                <form action="{{ route('admin.expenses.toggle', $order) }}" method="POST">
                    @csrf

                    <button class="px-3 py-1 rounded text-xs text-white
                        {{ $order->status === 'paid' ? 'bg-red-600' : 'bg-green-600' }}">
                        {{ $order->status === 'paid' ? 'Unpay' : 'Pay' }}
                    </button>
                </form>
            </td>

            {{-- DELETE --}}
            <td class="px-4 py-3">
                <form action="{{ route('admin.expenses.destroy', $order) }}"
                      method="POST"
                      onsubmit="return confirm('Delete this expense?');">
                    @csrf
                    @method('DELETE')

                    <button class="px-3 py-1 rounded text-xs bg-red-600 text-white hover:bg-red-700">
                        Delete
                    </button>
                </form>
            </td>

        </tr>

        {{-- EXPAND ROW --}}
        <tr>
            <td colspan="10" class="p-0">

                <div id="items-{{ $order->id }}"
                     class="max-h-0 overflow-hidden transition-all duration-500 bg-gray-50">

                    <div class="p-4">
                        <h4 class="font-semibold mb-3 text-gray-700">
                            Ordered Portraits
                        </h4>

                        <div class="flex gap-4 overflow-x-auto pb-2">

                            @foreach ($order->items as $portraitId => $qty)

                                @php
                                    $portrait = \App\Models\Portrait::find($portraitId);
                                @endphp

                                @if($portrait)
                                    <div class="flex-shrink-0 w-24 text-center">

                                        <img
                                            src="{{ Storage::url($portrait->image_path) }}"
                                            class="w-16 h-16 rounded-lg object-cover mx-auto"
                                        >

                                        <p class="text-xs mt-1">
                                            #{{ $portrait->id }} × {{ $qty }}
                                        </p>

                                    </div>
                                @endif

                            @endforeach

                        </div>
                    </div>

                </div>

            </td>
        </tr>

    @endforeach

    </tbody>
</table>

</div>

  




  


            </div>



        </div>


  



    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[id^="items-"]').forEach(el => {
                el.classList.add('max-h-0');
                el.classList.remove('max-h-[1000px]');
            });
        });

        function toggleOrderDetails(orderId) {
            const section = document.getElementById(`items-${orderId}`);
            if (section.classList.contains('max-h-0')) {
                document.querySelectorAll('[id^="items-"]').forEach(el => {
                    if (el.id !== `items-${orderId}`) {
                        el.classList.add('max-h-0');
                        el.classList.remove('max-h-[1000px]');
                    }
                });
                section.classList.remove('max-h-0');
                section.classList.add('max-h-[1000px]');
            } else {
                section.classList.add('max-h-0');
                section.classList.remove('max-h-[1000px]');
            }
        }
    </script>
</x-app-layout>
