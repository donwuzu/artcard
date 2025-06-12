@extends('layouts.base')

@section('title', 'Welcome')

@section('content')

    <div class="text-center mt-12">
        <h2 class="text-2xl font-semibold mb-4">ARTCARD COMPANY PORTRAITS</h2>
        <p class="mb-6 text-gray-600">Explore our gallery and order custom portraits directly via Whatsapp.</p>
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




<div id="gridView" class=" px-4 py-6">
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
        onclick="updateQuantity(this, -1)"
        aria-label="Decrease quantity for Portrait #{{ $portrait->id }}"
        class="quantity-button w-8 h-8 flex items-center justify-center bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 active:bg-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-6" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd" />
        </svg>
    </button>
    <input
        type="number"
        name="quantities[{{ $portrait->id }}]"
        min="0"
        value="0"
        aria-label="Quantity for Portrait #{{ $portrait->id }}"
        class="quantity-input text-center w-16 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 py-1.5 text-sm mx-2"
    >
    <button
        onclick="updateQuantity(this, 1)"
        aria-label="Increase quantity for Portrait #{{ $portrait->id }}"
        class="quantity-button w-8 h-8 flex items-center justify-center bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 active:bg-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-6" viewBox="0 0 20 20" fill="currentColor">
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
<div id="carouselView" class="hidden relative overflow-hidden px-4 py-6">
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-6" viewBox="0 0 20 20" fill="currentColor">
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-6" viewBox="0 0 20 20" fill="currentColor">
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
<div class="mt-8 mb-8 px-4 flex justify-center">
    {{ $portraits->links() }}
</div>





<!-- Modal -->
<div id="fullscreenModal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden">
    <div class="relative max-w-4xl w-full p-4">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-white text-3xl font-bold hover:text-gray-300">&times;</button>
        <img id="fullscreenImage" src="" class="w-full max-h-[90vh] object-contain rounded-xl">
    </div>
</div>





<div class="mt-6 px-4 mb-6 max-w-md w-full mx-auto space-y-4">

  <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <div class="bg-green-50/80 px-4 py-3 font-medium text-green-700 text-center text-sm border-b border-green-100/50">
        Your Selected Portraits
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-[13px] text-gray-600">
            <thead class="bg-gray-50/60 text-gray-500">
                <tr class="[&>th]:font-medium [&>th]:py-2.5 [&>th]:align-middle [&>th]:px-2">
                    <th class="text-left w-[45%] pl-4">Portrait</th>
                    <th class="text-center w-[10%]">Qty</th>
                    <th class="text-right w-[22%]">Price</th>
                    <th class="text-right w-[23%] pr-4">Subtotal</th>
                </tr>
            </thead>
            <tbody id="checkout-summary-body" class="divide-y divide-gray-100/80">
                <tr class="[&>td]:py-2.5 [&>td]:align-baseline [&>td]:px-2">
                    <td class="pl-4">Sample Portrait Name</td>
                    <td class="text-center">1</td>
                    <td class="text-right">KSh 1,200</td>
                    <td class="pr-4 text-right font-medium">KSh 1,200</td>
                </tr>
                <tr class="[&>td]:py-2.5 [&>td]:align-baseline [&>td]:px-2">
                    <td class="pl-4">Another Portrait With A Longer Name To Test Responsiveness</td>
                    <td class="text-center">2</td>
                    <td class="text-right">KSh 900</td>
                    <td class="pr-4 text-right font-medium">KSh 1,800</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="border-t border-gray-200 px-4 py-3 space-y-2">
      <p class="flex justify-between items-center text-sm">
          <span class="text-gray-600">Total Portraits:</span>
          <span id="summary-portraits-total" class="font-medium text-gray-800">KSh 3,000</span>
      </p>
      <p class="flex justify-between items-center text-sm">
          <span class="text-gray-600">Delivery Fee:</span>
          <span id="delivery-fee" class="font-medium text-green-600">KSh 300</span>
      </p>
    </div>
</div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm px-4 py-3">
       <p class="flex justify-between items-center text-base font-semibold">
            <span class="text-gray-700">Grand Total:</span>
            <span id="total" class="text-green-600 text-lg">KSh 3,300</span>
        </p>
    </div>

    <div class="pt-1 text-center">
        <button type="button"
                onclick="document.getElementById('user-details-modal').classList.remove('hidden')"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium w-full max-w-[280px] mx-auto shadow-sm hover:shadow-md transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
            Order Via WhatsApp
        </button>
    </div>
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
window.addEventListener('DOMContentLoaded', () => {
    setupInitialViewToggle();

    const selections = JSON.parse(localStorage.getItem('portraitSelections') || '{}');

    document.querySelectorAll('.quantity-input').forEach(input => {
        const id = input.name.match(/\[(\d+)\]/)[1];
        if (selections[id]) {
            input.value = selections[id];
            updateSubtotal(input.closest('.portrait-card'));
        }
    });

    document.addEventListener('input', function(event) {
        if (event.target.classList.contains('quantity-input')) {
              const input = event.target;
        const card = input.closest('.portrait-card');
        const id = card?.dataset.id;
        const value = Math.max(0, parseInt(input.value) || 0);

        input.value = value;

        let selections = JSON.parse(localStorage.getItem('portraitSelections') || '{}');

        if (value > 0) {
            selections[id] = value;
        } else {
            delete selections[id];
        }

        localStorage.setItem('portraitSelections', JSON.stringify(selections));
            calculateAndUpdateUI();
        }
    });

    calculateAndUpdateUI();

    document.querySelector('form#order-form')?.addEventListener('submit', function () {
        document.getElementById('notification-banner')?.classList.remove('hidden');
    });

    setupAjaxPagination();
});

function setupAjaxPagination() {
    document.querySelectorAll('.pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            fetchPortraits(this.href);
        });
    });
}

function fetchPortraits(url) {
    saveCurrentSelections();

    fetch(url)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newCarousel = doc.getElementById('carouselView');
            if (newCarousel) document.getElementById('carouselView').outerHTML = newCarousel.outerHTML;

            const newGridView = doc.getElementById('gridView');
            if (newGridView) document.getElementById('gridView').outerHTML = newGridView.outerHTML;

            const newPagination = doc.querySelector('.pagination');
            if (newPagination) document.querySelector('.pagination').outerHTML = newPagination.outerHTML;

            const selections = JSON.parse(localStorage.getItem('portraitSelections') || '{}');
            document.querySelectorAll('.quantity-input').forEach(input => {
                const id = input.name.match(/\[(\d+)\]/)[1];
                if (selections[id]) {
                    input.value = selections[id];
                    updateSubtotal(input.closest('.portrait-card'));
                }
            });

            setupInitialViewToggle();
            calculateAndUpdateUI();
            setupAjaxPagination();
        });
}

function saveCurrentSelections() {
    const selections = {};
    document.querySelectorAll('.quantity-input').forEach(input => {
        const id = input.name.match(/\[(\d+)\]/)[1];
        const value = parseInt(input.value) || 0;
        if (value > 0) selections[id] = value;
    });
    localStorage.setItem('portraitSelections', JSON.stringify(selections));
}

function updateSubtotal(card) {
    const quantityInput = card.querySelector('.quantity-input');
    const quantity = parseInt(quantityInput.value) || 0;
    const price = parseFloat(card.dataset.price) || 250;
    const subtotal = quantity * price;
    card.querySelector('.subtotal').textContent = `KSh ${subtotal.toLocaleString()}`;
}

function showUserDetailsModal() {
    const inputs = document.querySelectorAll("input[name^='quantities']");
    let totalSelected = 0;
    inputs.forEach(input => totalSelected += parseInt(input.value) || 0);
    const modal = document.getElementById('user-details-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.querySelector('input[name="name"]').focus();
    }
}

function scrollCarousel(direction) {
    const carousel = document.getElementById('portraitCarousel');
    const scrollAmount = carousel.clientWidth * 0.8 * direction;
    carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
}

function openModal(src) {
    document.getElementById('fullscreenImage').src = src;
    document.getElementById('fullscreenModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('fullscreenModal').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});



function calculateAndUpdateUI() {
    const deliveryFee = 300;
    const tier1Price = 250;
    const tier2Price = 190;
    const tierThreshold = 5;

    const deliveryFeeSpan = document.getElementById('delivery-fee');
    const totalSpan = document.getElementById('total');

    const selections = JSON.parse(localStorage.getItem('portraitSelections') || '{}');
    let totalUnits = Object.values(selections).reduce((sum, q) => sum + parseInt(q), 0);
    const unitPrice = totalUnits >= tierThreshold ? tier2Price : tier1Price;

    let overallSubtotal = 0;

    for (const id in selections) {
        const quantity = parseInt(selections[id]) || 0;
        overallSubtotal += quantity * unitPrice;
    }

    document.querySelectorAll('.portrait-card').forEach(card => {
        const quantityInput = card.querySelector('.quantity-input');
        const id = card.dataset.id;
        const quantity = selections[id] ? parseInt(selections[id]) : 0;

        if (quantityInput) quantityInput.value = quantity;
        card.querySelector('.unit-price-display').textContent = `KSh ${unitPrice.toLocaleString()}`;

        const cardSubtotal = quantity * unitPrice;
        card.querySelector('.subtotal').textContent = `KSh ${cardSubtotal.toLocaleString()}`;
    });

    const currentDeliveryFee = totalUnits > 0 ? deliveryFee : 0;
    if (deliveryFeeSpan) deliveryFeeSpan.textContent = `KSh ${currentDeliveryFee.toLocaleString()}`;
    if (totalSpan) totalSpan.textContent = `KSh ${(overallSubtotal + currentDeliveryFee).toLocaleString()}`;

    renderSelectionTable();
}


function showSuccessBanner() {
    const banner = document.getElementById('notification-banner');
    if (!banner) return;

    banner.classList.remove('hidden', 'opacity-0');
    banner.classList.add('opacity-100', 'transition-opacity', 'duration-500');
    setTimeout(() => {
        banner.classList.remove('opacity-100');
        banner.classList.add('opacity-0');
        setTimeout(() => banner.classList.add('hidden'), 500);
    }, 5000);
}

function setupInitialViewToggle() {
    const gridView = document.getElementById('gridView');
    const carouselView = document.getElementById('carouselView');
    const carouselBtn = document.getElementById('carouselViewBtn');
    const gridBtn = document.getElementById('gridViewBtn');

    if (!gridView || !carouselView || !carouselBtn || !gridBtn) return;

    // --- MODIFIED SECTION: Set Grid View as the default ---

    // 1. Show the grid view and hide the carousel view
    gridView.classList.remove('hidden');
    carouselView.classList.add('hidden');

    // 2. Set the grid button to the "active" state
    gridBtn.classList.add('bg-green-600', 'text-white');
    gridBtn.classList.remove('bg-green-100', 'text-green-700');

    // 3. Set the carousel button to the "inactive" state
    carouselBtn.classList.add('bg-green-100', 'text-green-700');
    carouselBtn.classList.remove('bg-green-600', 'text-white');

    gridBtn.addEventListener('click', () => {
        gridView.classList.remove('hidden');
        carouselView.classList.add('hidden');
        gridBtn.classList.add('bg-green-600', 'text-white');
        gridBtn.classList.remove('bg-green-100', 'text-green-700');
        carouselBtn.classList.add('bg-green-100', 'text-green-700');
        carouselBtn.classList.remove('bg-green-600', 'text-white');
    });

    carouselBtn.addEventListener('click', () => {
        carouselView.classList.remove('hidden');
        gridView.classList.add('hidden');
        carouselBtn.classList.add('bg-green-600', 'text-white');
        carouselBtn.classList.remove('bg-green-100', 'text-green-700');
        gridBtn.classList.add('bg-green-100', 'text-green-700');
        gridBtn.classList.remove('bg-green-600', 'text-white');
    });
}



function renderSelectionTable() {
    const selections = JSON.parse(localStorage.getItem('portraitSelections') || '{}');
    const tbody = document.getElementById('checkout-summary-body');
    const portraitTotal = document.getElementById('summary-portraits-total');
    if (!tbody || !portraitTotal) return;
    tbody.innerHTML = '';

    let totalUnits = 0;
    let totalCost = 0;
    const totalQty = Object.values(selections).reduce((sum, q) => sum + parseInt(q), 0);
    const unitPrice = totalQty >= 5 ? 190 : 250;

    for (const id in selections) {
        const quantity = parseInt(selections[id]);
        totalUnits += quantity;
        const name = `Portrait #${id}`;
        const subtotal = quantity * unitPrice;
        totalCost += subtotal;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-3 py-1.5">${name}</td>
            <td class="px-2 py-1.5 text-right">${quantity}</td>
            <td class="px-2 py-1.5 text-right">KSh ${unitPrice}</td>
            <td class="px-2 py-1.5 text-right">KSh ${subtotal.toLocaleString()}</td>
             <td class="px-2 py-1.5 text-right">
                <button onclick="removePortrait('${id}')" class="text-red-600 hover:underline text-xs">Remove</button>
            </td>
        `;
        tbody.appendChild(row);
    }

    portraitTotal.textContent = `KSh ${totalCost.toLocaleString()}`;
}


function removePortrait(id) {
    const selections = JSON.parse(localStorage.getItem('portraitSelections') || '{}');
    delete selections[id];
    localStorage.setItem('portraitSelections', JSON.stringify(selections));
    calculateAndUpdateUI();
}



function updateQuantity(button, change) {
    // Find the parent '.portrait-card' for the button that was clicked.
    const card = button.closest('.portrait-card');
    if (!card) return;

    // Find the quantity input field within that specific card.
    const input = card.querySelector('.quantity-input');
    if (!input) return;

    // Calculate the new value, ensuring it doesn't go below zero.
    const currentValue = parseInt(input.value) || 0;
    const newValue = Math.max(0, currentValue + change);
    input.value = newValue;

    // Get the portrait's ID and selections from localStorage.
    const id = card.dataset.id;
    let selections = JSON.parse(localStorage.getItem('portraitSelections') || '{}');

    // Update the selections object and save it back to localStorage.
    if (newValue > 0) {
        selections[id] = newValue;
    } else {
        delete selections[id]; // Remove the item if its quantity becomes zero.
    }
    localStorage.setItem('portraitSelections', JSON.stringify(selections));

    // Call the main UI function to redraw everything consistently based on the new state.
    calculateAndUpdateUI();
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







        

@endsection