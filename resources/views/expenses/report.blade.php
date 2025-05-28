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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow">
                <h1 class="text-2xl font-bold mb-6">Reports</h1>

        <form method="GET" action="{{ route('expenses.report') }}" class="mb-6 flex flex-col sm:flex-row sm:flex-wrap gap-4 items-start sm:items-center">
    <div class="flex flex-col sm:flex-row sm:justify-between gap-6">
       

        <div class="flex flex-col gap-4 w-60 sm:w-1/2">
            <label for="from_date" class="text-sm font-medium">From Date</label>
            <input id="from_date" type="date" name="from_date" value="{{ request('from_date') }}" class="px-4 py-2 rounded border w-full">

            <label for="to_date" class="text-sm font-medium">To Date</label>
            <input id="to_date" type="date" name="to_date" value="{{ request('to_date') }}" class="px-4 py-2 rounded border w-full">

            <div class="flex gap-4">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded w-60">
                    Filter
                </button>
                <a href="{{ route('expenses.report') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded w-full text-center">
                    Reset
                </a>
            </div>
        </div>
    </div>

    <div class="flex gap-2 flex-wrap w-full sm:w-auto sm:flex-nowrap mt-4">
       <a href="{{ route('expenses.report') }}"
           class="px-4 py-2 rounded text-center {{ !request('status') ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            ALL
        </a>
      
        <a href="{{ route('expenses.report', ['status' => 'paid', 'search' => request('search')]) }}"
           class="px-4 py-2 rounded text-center {{ request('status') === 'paid' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            Paid
        </a>
        <a href="{{ route('expenses.report', ['status' => 'unpaid', 'search' => request('search')]) }}"
           class="px-4 py-2 rounded text-center {{ request('status') === 'unpaid' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            Unpaid
        </a>
       
    </div>
</form>


             <div class="flex flex-col sm:flex-row justify-end gap-4 mb-4 text-right">
   
    <div class="text-green-700 font-extrabold">
        Total Paid: KSh {{ number_format($paidTotal) }}
    </div>
   
   
    <div class="text-red-600 font-extrabold">
        Total Unpaid: KSh {{ number_format($unpaidTotal) }}
    </div>
   
</div>


  <div class="overflow-x-auto rounded-lg shadow">
    <div class="min-w-[600px] sm:min-w-0"> <!-- Ensures table has minimum width on mobile -->
        <table class="w-full table-auto divide-y divide-gray-300 text-xs sm:text-sm">
            <thead class="bg-gray-100 font-semibold text-gray-700">
                <tr>
                    <th class="px-2 py-3 sm:px-4 whitespace-nowrap">#</th>
                    <th class="px-1 sm:px-4 py-3 whitespace-nowrap sticky left-0 bg-gray-100 z-10 min-w-[120px]">Name</th>
                    <th class="px-2 py-3 sm:px-4 whitespace-nowrap">Phone</th>
                    <th class="px-2 py-3 sm:px-4 whitespace-nowrap">Location</th>
                    <th class="px-2 py-3 sm:px-4 whitespace-nowrap">Total</th>
                    <th class="px-2 py-3 sm:px-4 whitespace-nowrap">Created At</th>
                    <th class="px-2 py-3 sm:px-4 whitespace-nowrap">Status</th>
                    <th class="px-2 py-3 sm:px-4 whitespace-nowrap">Actions</th>
                    <th class="px-2 py-3 sm:px-4 whitespace-nowrap">Delete</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-gray-800">
                @foreach ($filteredOrders as $index => $order)
                    <tr class="hover:bg-gray-50 cursor-pointer"
                      onclick="toggleOrderDetails({{ $order->id }})">
                        <td class="px-2 py-3 sm:px-4 font-semibold">{{ $index + 1 }}</td>
                        <td class="px-1 sm:px-4 py-3 sticky left-0 bg-white z-0 min-w-[120px] truncate max-w-[150px]">{{ $order->name }}</td>
                        <td class="px-2 py-3 sm:px-4">{{ $order->phone }}</td>
                        <td class="px-2 py-3 sm:px-4 truncate max-w-[100px]">{{ $order->location }}</td>
                        <td class="px-2 py-3 sm:px-4 whitespace-nowrap">KSh {{ number_format($order->total_price) }}</td>
                        <td class="px-2 py-3 sm:px-4 whitespace-nowrap">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-2 py-3 sm:px-4 whitespace-nowrap">{{ $order->status ?? 'unpaid' }}</td>
                        <td class="px-2 py-3 sm:px-4 whitespace-nowrap">
                            <form action="{{ route('expenses.toggleStatus', $order->id) }}" method="POST">
                                @csrf
                                <button class="{{ $order->status === 'paid' ? 'bg-black' : 'bg-green-600' }} text-white px-2 sm:px-3 py-1 rounded text-xs sm:text-sm">
                                    {{ $order->status === 'paid' ? 'Mark Unpaid' : 'Mark Paid' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-2 py-3 sm:px-4 whitespace-nowrap">
                            <form action="{{ route('expenses.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-800 text-white px-2 sm:px-3 py-1 rounded text-xs sm:text-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="9" class="p-0">
                            <div id="items-{{ $order->id }}" class="transition-all duration-500 ease-in-out max-h-0 overflow-hidden bg-gray-50">
                                <div class="p-4">
                                    <h4 class="font-semibold mb-2">Ordered Portraits:</h4>
                                    <div class="flex flex-nowrap overflow-x-auto gap-4 pb-2 -mx-2 px-2">
                                        @foreach ($order->items as $portraitId => $qty)
                                            @php $portrait = \App\Models\Portrait::find($portraitId); @endphp
                                            @if($portrait)
                                                <div class="flex-shrink-0 w-20 sm:w-24">
                                                    <img 
                                                        src="{{ Storage::url($portrait->image_path) }}" 
                                                        class="rounded-xl w-14 h-14 sm:w-16 sm:h-16 object-cover mb-2"
                                                        alt="Portrait {{ $portrait->id }}"
                                                    >
                                                    <p class="text-xs sm:text-sm font-medium">#{{ $portrait->id }} x{{ $qty }}</p>
                                                    <p class="text-xs text-gray-500">KSh {{ number_format($portrait->price) }}</p>
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
