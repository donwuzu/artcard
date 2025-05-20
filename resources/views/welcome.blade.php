@extends('layouts.base')

@section('title', 'Welcome')

@section('content')

    <div class="text-center mt-12">
        <h2 class="text-2xl font-semibold mb-4">ARTCARD COMPANY PORTRAITS</h2>
        <p class="mb-6 text-gray-600">Explore our gallery and order custom portraits directly.</p>
    </div>




  @if($showDiscountBanner ?? false)
    <div id="discountBanner" class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <p class="font-bold">Special Offer!</p>
                <p>Select 10 or more portraits and get each @ 190!</p>
            </div>
            <button onclick="document.getElementById('discountBanner').style.display='none'" 
                    class="text-blue-700 hover:text-blue-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
@endif

   <form id="order-form" method="POST" action="{{ route('order.store') }}">

        @csrf



<!-- View Toggle and Controls -->
<div class="flex justify-between items-center px-4 mb-4">
    <h2 class="text-xl font-bold text-gray-800">Portrait Gallery</h2>

        <div class="flex space-x-2">


                        <button id="carouselViewBtn" class="p-2 bg-green-100 rounded-lg text-green-700">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8v3h2v-3l4 8z" clip-rule="evenodd" />
                             </svg>
                        </button>

                        <button id="gridViewBtn" class="p-2 bg-green-600 rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                      
         </div>
</div>




<div id="gridView" class="hidden px-4 py-6">
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
        @foreach ($portraits as $portrait)
        <div class="portrait-card group flex flex-col bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 ease-in-out hover:shadow-2xl transform hover:-translate-y-1 h-full"
             data-id="{{ $portrait->id }}"
             data-price="{{ $portrait->price }}"  {{-- Assuming this is the raw price for JS calculations --}}
             data-name="Portrait #{{ $portrait->id }}">

            <div class="relative flex-shrink-0">
                <img src="{{ Storage::url($portrait->image_path) }}"
                     alt="Portrait #{{ $portrait->id }}"
                     onclick="openModal(this.src)"
                     class="cursor-pointer w-full h-56 sm:h-64 object-cover transition-transform duration-300 group-hover:scale-105">
                {{-- Optional: Add an overlay on hover if desired --}}
                {{-- <div class="absolute inset-0 bg-black bg-opacity-25 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                    <span class="text-white text-lg font-semibold">View</span>
                </div> --}}
            </div>

            <div class="p-4 flex flex-col flex-grow">
                {{-- Portrait Name/ID (Optional but good for context) --}}
                <h3 class="text-md font-semibold text-gray-800 mb-2 truncate" title="Portrait #{{ $portrait->id }}">
                    Portrait #{{ $portrait->id }}
                </h3>

                {{-- Price Display --}}
                <p class="text-sm text-gray-700 mb-3">
                    Price: <span class="unit-price-display font-bold text-indigo-600">KSh 250</span> {{-- Or use {{ $portrait->formatted_price }} if available --}}
                </p>

                {{-- Spacer to push subsequent content to bottom --}}
                <div class="flex-grow"></div>

                {{-- Quantity Controls --}}
                <div class="flex items-center justify-between mb-3">
                    <button
                        onclick="updateQuantity(this, -1, true)"
                        aria-label="Decrease quantity for Portrait #{{ $portrait->id }}"
                        class="quantity-button w-8 h-8 flex items-center justify-center bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 active:bg-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <input
                        type="number"
                        name="quantities[{{ $portrait->id }}]"
                        min="0"
                        value="0"
                        aria-label="Quantity for Portrait #{{ $portrait->id }}"
                        class="quantity-input text-center w-16 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 py-1.5 text-sm mx-2">
                    <button
                        onclick="updateQuantity(this, 1, true)"
                        aria-label="Increase quantity for Portrait #{{ $portrait->id }}"
                        class="quantity-button w-8 h-8 flex items-center justify-center bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 active:bg-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                {{-- Subtotal Display --}}
                <p class="text-green-700 text-sm font-semibold text-right">
                    Subtotal: <span class="subtotal">KSh 0</span>
                </p>

                {{-- Optional: Add to Cart Button (if applicable) --}}
                {{-- <button class="mt-4 w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 text-sm font-medium">
                    Add to Cart
                </button> --}}
            </div>
        </div>
        @endforeach
    </div>
</div>





<!-- Updated Carousel View -->
<div id="carouselView" class=" relative overflow-hidden px-4 py-6">
    <button onclick="scrollCarousel(-1)"
            aria-label="Previous portrait"
            class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-green-500 text-white shadow rounded-full w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center hover:bg-green-600 transition focus:outline-none focus:ring-2 focus:ring-green-400">
        ‹
    </button>

    <div id="portraitCarousel" class="flex overflow-x-auto snap-x snap-mandatory gap-6 pb-6 scroll-smooth hide-scrollbar">
        @foreach ($portraits as $portrait)
        <div class="portrait-card group flex flex-col flex-shrink-0 snap-center bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 ease-in-out hover:shadow-2xl transform hover:-translate-y-1"
             style="width: auto; max-width: 100%;"
             data-id="{{ $portrait->id }}"
             data-price="{{ $portrait->price }}"
             data-name="Portrait #{{ $portrait->id }}">

            <div class="relative flex-shrink-0">
                <img src="{{ Storage::url($portrait->image_path) }}"
                     alt="Portrait #{{ $portrait->id }}"
                     onclick="openModal(this.src)"
                     class="cursor-pointer w-full object-contain rounded-xl mb-4 shadow transition-transform duration-300 group-hover:scale-105"
                     style="max-height: 420px; height: auto;">
            </div>

            <div class="p-4 flex flex-col flex-grow">
                <h3 class="text-md font-semibold text-gray-800 mb-2 truncate" title="Portrait #{{ $portrait->id }}">
                    Portrait #{{ $portrait->id }}
                </h3>

                <p class="text-sm text-gray-700 mb-3">
                    Price: <span class="unit-price-display font-bold text-indigo-600">KSh 250</span>
                </p>

                <div class="flex-grow"></div>

                <div class="flex items-center justify-between mb-3">
                    <button
                        onclick="updateQuantity(this, -1)"
                        aria-label="Decrease quantity for Portrait #{{ $portrait->id }}"
                        class="quantity-button w-8 h-8 flex items-center justify-center bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 active:bg-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <input
                        type="number"
                        name="quantities_carousel[{{ $portrait->id }}]"
                        min="0"
                        value="0"
                        aria-label="Quantity for Portrait #{{ $portrait->id }}"
                        class="quantity-input text-center w-16 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 py-1.5 text-sm mx-2">
                    <button
                        onclick="updateQuantity(this, 1)"
                        aria-label="Increase quantity for Portrait #{{ $portrait->id }}"
                        class="quantity-button w-8 h-8 flex items-center justify-center bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 active:bg-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <p class="text-green-700 text-sm font-semibold text-right">
                    Subtotal: <span class="subtotal">KSh 0</span>
                </p>
            </div>
        </div>
        @endforeach
    </div>

    <button onclick="scrollCarousel(1)"
            aria-label="Next portrait"
            class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-green-500 text-white shadow rounded-full w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center hover:bg-green-600 transition focus:outline-none focus:ring-2 focus:ring-green-400">
        ›
    </button>
</div>

<!-- Shared Styles -->
<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .snap-x { scroll-snap-type: x mandatory; }
    .snap-center { scroll-snap-align: center; }
</style>









<!-- Pagination Section -->
<div class="mt-8 px-4 flex justify-center">
    {{ $portraits->links() }}
</div>







<!-- Modal -->
<div id="fullscreenModal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden">
    <div class="relative max-w-4xl w-full p-4">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-white text-3xl font-bold hover:text-gray-300">&times;</button>
        <img id="fullscreenImage" src="" class="w-full max-h-[90vh] object-contain rounded-xl">
    </div>
</div>

      <!-- Pricing & Checkout -->
<div class="mt-8 text-right space-y-2 px-4 mb-8">
    <p class="text-lg text-gray-700">Delivery Fee: <span id="delivery-fee" class="text-green-700">KSh 300</span></p>
    <p class="text-xl font-bold">Total: <span id="total" class="text-green-700">KSh 0</span></p>

    <!-- Trigger Modal -->
    <button type="button" onclick="document.getElementById('user-details-modal').classList.remove('hidden')" class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 font-semibold">
        Checkout
    </button>
</div>

<!-- Detail Input Modal -->
<div id="user-details-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white p-6 rounded-xl w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Enter Your Details</h2>

        <div class="space-y-4">
            <input type="text" name="name" form="order-form" placeholder="Full Name" class="w-full border rounded px-4 py-2" required>
            <input type="text" name="phone" form="order-form" placeholder="Phone Number" class="w-full border rounded px-4 py-2" required>
            <input type="text" name="location" form="order-form" placeholder="Location" class="w-full border rounded px-4 py-2" required>
        </div>

        <div class="mt-4 flex justify-end space-x-2">
            <button type="button" onclick="document.getElementById('user-details-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>

            <!-- ✅ Final Submit -->
            <button type="submit" form="order-form" class="px-4 py-2 bg-green-600 text-white rounded">
                Submit Order
            </button>
        </div>
    </div>
</div>

<!-- Notification Banner -->
<div id="notification-banner" class="hidden fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-100 text-green-900 px-6 py-3 rounded shadow z-50 border border-green-300">
    ✅ Portraits Submitted Successfully
</div>


    </form> 
    
    <div style="background-color: #f1f1f1; padding: 20px; text-align: center; font-size: 14px; color: #555;">
            &copy; <script>document.write(new Date().getFullYear())</script> ATCARD Company. All rights reserved.
    </div>




<script>

    document.querySelector('form#order-form').addEventListener('submit', function () {
  // Optionally show banner
  document.getElementById('notification-banner')?.classList.remove('hidden');
});




    document.addEventListener("DOMContentLoaded", () => {
        window.showUserDetailsModal = function () {
            const inputs = document.querySelectorAll("input[name^='quantities']");
            let totalSelected = 0;

            inputs.forEach(input => {
                const val = parseInt(input.value);
                if (!isNaN(val)) totalSelected += val;
            });

         

            const modal = document.getElementById('user-details-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.querySelector('input[name="name"]').focus();
            }
        }
    });

    function scrollCarousel(direction) {
        const carousel = document.getElementById('portraitCarousel');
        const cardWidth = carousel.querySelector('div').offsetWidth + 24;
        carousel.scrollBy({ left: direction * cardWidth, behavior: 'smooth' });
    }

    function openModal(src) {
        document.getElementById('fullscreenImage').src = src;
        document.getElementById('fullscreenModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('fullscreenModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });



























document.addEventListener('DOMContentLoaded', function() {
    const deliveryFee = 300;
    const tier1Price = 250; // Price if total units < 5
    const tier2Price = 190; // Price if total units >= 5
    const tierThreshold = 5; // Min units for Tier 2 price

    const deliveryFeeSpan = document.getElementById('delivery-fee');
    const totalSpan = document.getElementById('total');
    const portraitContainer = document.getElementById('portraitContainer'); // Add this container in your HTML

    // Function to handle +/- button clicks for both views
    window.updateQuantity = function(button, change, isGrid = false) {
        let input;
        
        if (isGrid) {
            // For grid view, the structure is different
            const buttonContainer = button.parentElement;
            input = buttonContainer.querySelector('.quantity-input');
        } else {
            // For carousel view
            input = (change > 0) 
                ? button.previousElementSibling 
                : button.nextElementSibling;
        }

        if (input && input.classList.contains('quantity-input')) {
            let currentValue = parseInt(input.value) || 0;
            let newValue = Math.max(0, currentValue + change);
            input.value = newValue;
            
            // Trigger input event for calculation
            const event = new Event('input', { bubbles: true });
            input.dispatchEvent(event);
        }
    }

    // Main calculation function
    function calculateAndUpdateUI() {
        let overallSubtotal = 0;
        let totalUnits = 0;
        
        // Get all portrait cards from both views
        const allCards = document.querySelectorAll('.portrait-card');

        // First pass: Calculate total units
        allCards.forEach(card => {
            const quantityInput = card.querySelector('.quantity-input');
            totalUnits += parseInt(quantityInput.value) || 0;
        });

        // Determine applicable unit price
        const currentUnitPrice = (totalUnits >= tierThreshold && totalUnits > 0) ? tier2Price : tier1Price;
        const displayUnitPrice = totalUnits === 0 ? tier1Price : currentUnitPrice;

        // Second pass: Update each card
        allCards.forEach(card => {
            const quantityInput = card.querySelector('.quantity-input');
            const quantity = parseInt(quantityInput.value) || 0;
            const unitPriceSpan = card.querySelector('.unit-price-display');
            const subtotalSpan = card.querySelector('.subtotal');

            // Update displayed prices
            unitPriceSpan.textContent = `KSh ${displayUnitPrice.toLocaleString()}`;
            
            // Calculate subtotal using the actual price tier
            const cardSubtotal = quantity * currentUnitPrice;
            subtotalSpan.textContent = `KSh ${cardSubtotal.toLocaleString()}`;
            
            overallSubtotal += cardSubtotal;
        });

        // Update summary
        const currentDeliveryFee = (totalUnits > 0) ? deliveryFee : 0;
        deliveryFeeSpan.textContent = `KSh ${currentDeliveryFee.toLocaleString()}`;
        
        const overallTotal = overallSubtotal + currentDeliveryFee;
        totalSpan.textContent = `KSh ${overallTotal.toLocaleString()}`;
    }

    // Event delegation for both views
    document.addEventListener('input', function(event) {
        if (event.target.classList.contains('quantity-input')) {
            calculateAndUpdateUI();
        }
    });

    calculateAndUpdateUI();

    
});

























function showSuccessBanner() {
    const banner = document.getElementById('notification-banner');
    banner.classList.remove('hidden', 'opacity-0');
    banner.classList.add('opacity-100', 'transition-opacity', 'duration-500');

    setTimeout(() => {
        banner.classList.remove('opacity-100');
        banner.classList.add('opacity-0');
        setTimeout(() => banner.classList.add('hidden'), 500);
    }, 5000); // visible for 5 seconds


    }

</script>


    
    
<style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .snap-x {
        scroll-snap-type: x mandatory;
    }
    .snap-center {
        scroll-snap-align: center;
    }








    /* In your CSS file */
.pagination {
    display: flex;
    list-style: none;
    padding: 0;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 2rem;
}

.pagination li a, 
.pagination li span {
    display: block;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    text-decoration: none;
    color: #4a5568;
    border: 1px solid #e2e8f0;
    background-color: white;
}

.pagination li.active span {
    background-color: #48bb78;
    color: white;
    border-color: #48bb78;
}

.pagination li a:hover {
    background-color: #f7fafc;
}

.pagination li.disabled span {
    color: #a0aec0;
    cursor: not-allowed;
}
</style>



<script>
   // Set initial state - Carousel visible & active, Grid hidden & inactive
document.getElementById('gridView').classList.add('hidden');
document.getElementById('carouselView').classList.remove('hidden');

// Set Carousel button as active (green-600, white text)
document.getElementById('carouselViewBtn').classList.add('bg-green-600', 'text-white');
document.getElementById('carouselViewBtn').classList.remove('bg-green-100', 'text-green-700');

// Set Grid button as inactive (green-100, green text)
document.getElementById('gridViewBtn').classList.add('bg-green-100', 'text-green-700');
document.getElementById('gridViewBtn').classList.remove('bg-green-600', 'text-white');

// Toggle between views (unchanged)
document.getElementById('gridViewBtn').addEventListener('click', function() {
    document.getElementById('gridView').classList.remove('hidden');
    document.getElementById('carouselView').classList.add('hidden');
    this.classList.add('bg-green-600', 'text-white');
    this.classList.remove('bg-green-100', 'text-green-700');
    document.getElementById('carouselViewBtn').classList.add('bg-green-100', 'text-green-700');
    document.getElementById('carouselViewBtn').classList.remove('bg-green-600', 'text-white');
});

document.getElementById('carouselViewBtn').addEventListener('click', function() {
    document.getElementById('carouselView').classList.remove('hidden');
    document.getElementById('gridView').classList.add('hidden');
    this.classList.add('bg-green-600', 'text-white');
    this.classList.remove('bg-green-100', 'text-green-700');
    document.getElementById('gridViewBtn').classList.add('bg-green-100', 'text-green-700');
    document.getElementById('gridViewBtn').classList.remove('bg-green-600', 'text-white');
});
    
    document.getElementById('carouselViewBtn').addEventListener('click', function() {
        document.getElementById('carouselView').classList.remove('hidden');
        document.getElementById('gridView').classList.add('hidden');
        this.classList.add('bg-green-600', 'text-white');
        this.classList.remove('bg-green-100', 'text-green-700');
        document.getElementById('gridViewBtn').classList.add('bg-green-100', 'text-green-700');
        document.getElementById('gridViewBtn').classList.remove('bg-green-600', 'text-white');
    });

    // Carousel navigation
    function scrollCarousel(direction) {
        const carousel = document.getElementById('portraitCarousel');
        const scrollAmount = carousel.clientWidth * 0.8 * direction;
        carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
    
    // Quantity update function (you'll need to adapt your existing function)
    function updateQuantity(button, change, isGrid = false) {
        // Your existing quantity update logic here
    }
</script>






        

@endsection