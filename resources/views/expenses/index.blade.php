<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Expenses') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow">
                <h1 class="text-2xl font-bold mb-6">Expense Manager</h1>



                <form method="GET" action="{{ route('expenses.index') }}" class="mb-4 flex gap-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name/phone" class="px-4 py-2 rounded border w-60">
                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded">Search</button>

                
                 
                        <div class="flex gap-2">
                            <a href="{{ route('expenses.index', ['status' => 'paid', 'search' => request('search')]) }}"
                            class="px-4 py-2 rounded {{ request('status') === 'paid' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                Paid
                            </a>
                            <a href="{{ route('expenses.index', ['status' => 'unpaid', 'search' => request('search')]) }}"
                            class="px-4 py-2 rounded {{ request('status') === 'unpaid' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                Unpaid
                            </a>
                        </div>
                </form>


                <br>


                @php
                    $paidTotal = $orders->where('status', 'paid')->sum('total_price');
                    $unpaidTotal = $orders->where('status', 'unpaid')->sum('total_price');
                @endphp

                <div class="flex justify-between mb-4">
                 
                      <div class="text-red-600 font-semibold">
                        Total Unpaid: KSh {{ number_format($unpaidTotal) }}
                    </div>
                 
                 
                    <div class="text-green-700 font-semibold">
                        Total Paid: KSh {{ number_format($paidTotal) }}
                    </div>
                  
                </div>

                 <br>

                <table class="w-full border border-gray-300 rounded-lg overflow-hidden shadow">
                    <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                        <tr>
                            <th class="p-3">#</th>
                            <th class="p-3">Name</th>
                            <th class="p-3">Phone</th>
                            <th class="p-3">Location</th>
                            <th class="p-3">Total</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-800">
                        @foreach ($orders as $index => $order)
                            <tr 
                                class="border-t hover:bg-gray-50 cursor-pointer" 
                                onclick="toggleOrderDetails({{ $order->id }})"
                            >
                                <td class="p-3 font-semibold">{{ $index + 1 }}</td>
                                <td class="p-3">{{ $order->name }}</td>
                                <td class="p-3">{{ $order->phone }}</td>
                                <td class="p-3">{{ $order->location }}</td>
                                <td class="p-3">KSh {{ number_format($order->total_price) }}</td>
                                <td class="p-3">{{ $order->status ?? 'unpaid' }}</td>
                            <td class="p-3">
                                <form action="{{ route('expenses.toggleStatus', $order->id) }}" method="POST">
                                    @csrf
                                    <button class="{{ $order->status === 'paid' ? 'bg-red-600' : 'bg-green-600' }} text-white px-3 py-1 rounded">
                                        {{ $order->status === 'paid' ? 'Mark Unpaid' : 'Mark Paid' }}
                                    </button>
                                </form>
                            </td>

                            </tr>
                         <tr>
    <td colspan="7" class="p-0">
<div id="items-{{ $order->id }}" class="transition-all duration-500 ease-in-out max-h-0 overflow-hidden bg-gray-50">            <div class="p-4">
                <h4 class="font-semibold mb-2">Ordered Portraits:</h4>
                <div class="flex flex-nowrap overflow-x-auto gap-4 pb-2"> <!-- Changed to flex row with horizontal scrolling -->
                    @foreach ($order->items as $portraitId => $qty)
                        @php $portrait = \App\Models\Portrait::find($portraitId); @endphp
                        @if($portrait)
                            <div class="flex-shrink-0 w-24"> <!-- Changed to prevent wrapping and fixed width -->
                                <img 
                                    src="{{ Storage::url($portrait->image_path) }}" 
                                    class="rounded-xl w-16 h-16 object-cover mb-2"
                                    alt="Portrait {{ $portrait->id }}"
                                >
                                <p class="text-sm font-medium">#{{ $portrait->id }} x{{ $qty }}</p>
                                <p class="text-xs text-gray-500">Unit Price: KSh {{ number_format($portrait->price) }}</p>
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


<script>
    // Close all sections when page loads
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[id^="items-"]').forEach(el => {
            el.classList.add('max-h-0');
            el.classList.remove('max-h-[1000px]');
        });
    });

    function toggleOrderDetails(orderId) {
        const section = document.getElementById(`items-${orderId}`);
        if (section.classList.contains('max-h-0')) {
            // Close any other open sections first
            document.querySelectorAll('[id^="items-"]').forEach(el => {
                if (el.id !== `items-${orderId}`) {
                    el.classList.add('max-h-0');
                    el.classList.remove('max-h-[1000px]');
                }
            });
            
            // Open the clicked section
            section.classList.remove('max-h-0');
            section.classList.add('max-h-[1000px]');
        } else {
            section.classList.add('max-h-0');
            section.classList.remove('max-h-[1000px]');
        }
    }
</script>
</x-app-layout>