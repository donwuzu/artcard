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
                        <button id="gridViewBtn" class="p-2 bg-green-600 rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button id="carouselViewBtn" class="p-2 bg-green-100 rounded-lg text-green-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8v3h2v-3l4 8z" clip-rule="evenodd" />
                            </svg>
                        </button>
         </div>
</div>

<!-- Grid View -->
<div id="gridView" class=" px-4">
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @foreach ($portraits as $portrait)
        <div class="portrait-card bg-white rounded-lg shadow-md overflow-hidden transition-transform transform hover:scale-[1.02]"
             data-id="{{ $portrait->id }}"
             data-price="{{ $portrait->price }}"
             data-name="Portrait #{{ $portrait->id }}">
            
            <img src="{{ Storage::url($portrait->image_path) }}"
                 onclick="openModal(this.src)"
                 class="cursor-pointer w-full h-40 sm:h-48 object-cover">
            
            <div class="p-3">
                <h3 class="font-medium text-sm truncate">Portrait #{{ $portrait->id }}</h3>
                <p class="text-xs text-gray-600 mb-2">
                    Price: <span class="unit-price-display font-medium">KSh 250</span>
                </p>
                
                <div class="flex items-center justify-between mb-2">
                    <button onclick="updateQuantity(this, -1, true)" class="px-2 py-1 bg-gray-100 rounded text-sm">-</button>
                    <input type="number" name="quantities[{{ $portrait->id }}]" min="0" value="0" 
                           class="quantity-input text-center w-12 border rounded px-1 py-1 text-sm">
                    <button onclick="updateQuantity(this, 1, true)" class="px-2 py-1 bg-gray-100 rounded text-sm">+</button>
                </div>
                
                <p class="text-green-700 text-xs font-medium">
                    Subtotal: <span class="subtotal">KSh 0</span>
                </p>
            </div>
        </div>
        @endforeach
    </div>

   


</div>

<!-- Carousel View (Hidden by default) -->
<div id="carouselView" class="hidden relative overflow-hidden px-4">
    <button onclick="scrollCarousel(-1)" 
            class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-green-500 text-white shadow rounded-full w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center hover:bg-green-600 transition">
        ‹
    </button>

    <div id="portraitCarousel" class="flex overflow-x-auto snap-x snap-mandatory space-x-4 sm:space-x-6 pb-6 scroll-smooth hide-scrollbar">
        @foreach ($portraits as $portrait)
        <div class="portrait-card min-w-[70vw] sm:min-w-[45vw] md:min-w-[30vw] lg:min-w-[25vw] xl:min-w-[20vw] flex-shrink-0 snap-center bg-white p-4 rounded-xl shadow-lg transition-transform transform hover:scale-[1.02]"
             data-id="{{ $portrait->id }}"
             data-price="{{ $portrait->price }}"
             data-name="Portrait #{{ $portrait->id }}">
            
            <img src="{{ Storage::url($portrait->image_path) }}"
                 onclick="openModal(this.src)"
                 class="cursor-pointer w-full h-48 sm:h-56 md:h-64 object-cover rounded-lg mb-3">
            
            <h2 class="text-lg font-semibold mb-1 truncate">Portrait #{{ $portrait->id }}</h2>
            <p class="text-gray-600 text-sm mb-2">
                Unit Price: <span class="unit-price-display font-medium">KSh 250</span>
            </p>
            
            <div class="flex items-center justify-center space-x-2 mb-3">
                <button onclick="updateQuantity(this, -1)" class="px-2 sm:px-3 py-1 bg-gray-100 rounded text-sm sm:text-base">-</button>
                <input type="number" name="quantities[{{ $portrait->id }}]" min="0" value="0" 
                       class="quantity-input text-center w-12 sm:w-16 border rounded px-1 sm:px-2 py-1 text-sm">
                <button onclick="updateQuantity(this, 1)" class="px-2 sm:px-3 py-1 bg-gray-100 rounded text-sm sm:text-base">+</button>
            </div>
            
            <p class="text-green-700 font-medium text-sm">
                Subtotal: <span class="subtotal">KSh 0</span>
            </p>
        </div>
        @endforeach
    </div>

    <button onclick="scrollCarousel(1)" 
            class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-green-500 text-white shadow rounded-full w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center hover:bg-green-600 transition">
        ›
    </button>
</div>





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

        <!-- Pricing -->
        <div class="mt-8 text-right space-y-2 px-4 mb-8">
            <p class="text-lg text-gray-700">Delivery Fee: <span id="delivery-fee" class="text-green-700">KSh 0</span></p>
            <p class="text-xl font-bold">Total: <span id="total" class="text-green-700">KSh 0</span></p>
         <button type="button" onclick="showUserDetailsModal()" class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 font-semibold">Checkout</button>

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
                            <button type="submit" form="order-form" onclick="showSuccessBanner()" class="px-4 py-2 bg-green-600 text-white rounded">Submit</button>
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
    // Toggle between grid and carousel views
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
