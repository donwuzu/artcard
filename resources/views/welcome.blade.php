@extends('layouts.base')

@section('title', 'Welcome')

@section('content')


<div class="relative mt-12">
    <div class="flex justify-between items-start p-4">
        <div class="flex-grow text-center md:text-left">
            <h2 class="text-2xl font-semibold">ARTCARD COMPANY PORTRAITS</h2>
            <p class="text-gray-600">Explore our gallery and order custom portraits directly via Whatsapp.</p>
        </div>

        <div class="ml-4 flex-shrink-0">
             <button id="cartButton" type="button"
                class="relative bg-green-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-700 transition-all duration-300 flex items-center space-x-2 z-10 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span id="cartBadge" class="absolute -top-1.5 -right-1.5 bg-red-600 text-white text-xs font-semibold px-1.5 py-0.5 rounded-full hidden">0</span>
            </button>
        </div>
    </div>
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
<div id="fullscreenModal" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center">
  <div id="fullscreenContainer" class="relative max-w-4xl w-full px-4">
<button onclick="closeModal()" 
        class="absolute top-4 right-4 sm:top-6 sm:right-6 p-2 rounded-full bg-black/30 hover:bg-black/50 transition-all duration-200 z-50 cursor-pointer">
  <!-- Visible X icon with reliable sizing -->
  <div class="relative h-8 w-8 sm:h-10 sm:w-10 flex items-center justify-center">
    <svg xmlns="http://www.w3.org/2000/svg" 
         class="absolute h-full w-full text-white hover:text-red-300 transition-colors duration-200" 
         viewBox="0 0 24 24" 
         fill="none" 
         stroke="currentColor" 
         stroke-width="2.5" 
         stroke-linecap="round" 
         stroke-linejoin="round">
      <line x1="18" y1="6" x2="6" y2="18"></line>
      <line x1="6" y1="6" x2="18" y2="18"></line>
    </svg>
  </div>
  <span class="sr-only">Close modal</span>
</button>
    <img id="fullscreenImage" src="" 
         class="w-full max-h-[90vh] object-contain rounded-xl shadow-lg">
  </div>
</div>






<!-- Sidebar Cart (Replace your existing static cart) -->
<div id="cart-overlay" class="cart-overlay fixed inset-0 bg-black hidden z-40">
  
  <div id="cartSidebar" 
       role="dialog" 
       aria-modal="true" 
       aria-labelledby="cart-heading"
       class="absolute right-0 top-0 h-full w-full max-w-md bg-white flex flex-col">

      <header class="flex-shrink-0 relative border-b border-slate-200 px-5 py-4">
          <h2 id="cart-heading" class="text-lg font-semibold text-slate-800 text-center">Your Selected Portraits</h2>
          <button id="closeCart" 
                  aria-label="Close cart"
                  class="absolute top-1/2 right-4 -translate-y-1/2 text-2xl font-semibold text-slate-500 hover:text-red-600 transition-colors">
              &times;
          </button>
      </header>

      <!-- Scrollable Content -->
      <div class="flex-grow overflow-y-auto px-5 py-4" style="max-height: calc(100vh - 240px);">
          <table class="w-full text-sm text-slate-600">
              <thead>
                  <tr class="border-b border-slate-200">
                      <th class="w-2/5 text-left font-medium pb-3 pl-2">Portrait</th>
                      <th class="w-1/6 text-center font-medium pb-3">Qty</th>
                      <th class="w-1/5 text-right font-medium pb-3">Price</th>
                      <th class="w-1/5 text-right font-medium pb-3">Subtotal</th>
                      <th class="w-auto text-right font-medium pb-3 pr-2"><span class="sr-only">Actions</span></th>
                  </tr>
              </thead>
              <tbody id="checkout-summary-body" class="divide-y divide-slate-100">
                   <tr>
                      <td colspan="5" class="text-center text-slate-500 py-10">Your cart is currently empty.</td>
                  </tr>
              </tbody>
          </table>
      </div>

<!-- Footer section - improved -->
<footer class="flex-shrink-0 bg-slate-50 px-5 pt-5 pb-6 border-t border-slate-200">
    <div class="space-y-3 text-sm">
        <div class="flex justify-between">
            <span class="text-slate-600">Subtotal:</span>
            <span id="summary-portraits-total" class="font-medium text-slate-800">KSh 0</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-600">Delivery Fee:</span>
            <span id="delivery-fee" class="font-medium text-slate-800">KSh 0</span>
        </div>
    </div>

    <div class="flex justify-between items-center font-semibold text-lg border-t border-slate-200 mt-4 pt-4">
        <span class="text-slate-800">Grand Total:</span>
        <span id="total" class="text-green-600">KSh 0</span>
    </div>

    <button type="button" id="orderButton"
            class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold shadow-sm hover:shadow-lg transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white">
        Order Via WhatsApp
    </button>
</footer>
  </div>
</div>












<div id="user-details-overlay" class="hidden flex items-center justify-center px-4 sm:px-0">
  <div id="user-details-modal" role="dialog" aria-modal="true" aria-labelledby="modal-heading"
       class="bg-white rounded-lg p-6 shadow-lg w-full max-w-md relative">

      <button type="button"
              aria-label="Close modal"
              class="close-modal-button absolute top-4 right-4 text-gray-600 hover:text-red-600 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
      </button>

      <h2 id="modal-heading" class="text-2xl font-bold text-gray-900 mb-4 text-center">Enter Your Details</h2>

      <form id="order-form" method="POST" action="{{ route('order.store') }}" class="space-y-5">
          @csrf
          <div class="space-y-4">
              <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                  <input type="text" id="name" name="name" 
                         class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                  @error('name')
                      <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                  @enderror
              </div>

              <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                  <input type="text" id="phone" name="phone" 
                         class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                  @error('phone')
                      <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                  @enderror
              </div>

              <div>
                  <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Delivery Location *</label>
                  <input type="text" id="location" name="location" 
                         class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                  @error('location')
                      <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                  @enderror
              </div>
          </div>

         <div class="pt-5 border-t border-dashed flex flex-col sm:flex-row sm:justify-end gap-3">
   
    <button type="submit"
            class="w-full sm:w-auto px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-500">
        Submit Order
    </button>

   <button type="button"
            class="close-modal-button w-full sm:w-auto px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition focus:outline-none focus:ring-2 focus:ring-gray-400">
        Cancel
    </button>
   
</div>
      </form>
  </div>
</div>









<!-- Notification Banner -->
<div id="notification-banner" class="hidden fixed top-4 left-1/2 transform -translate-x-1/2 bg-white px-6 py-4 rounded-lg shadow-lg z-50 border border-green-900/20">
    <div class="flex items-center space-x-3">
        <svg class="h-20 w-20 text-green-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        <span class="font-medium text-green-900">Portraits Submitted Successfully</span>
    </div>
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

  document.querySelector('form#order-form')?.addEventListener('submit', function (e) {
    // Clear previous hidden inputs
    const form = this;
    form.querySelectorAll('input[name^="quantities["]').forEach(el => el.remove());

    const selections = JSON.parse(localStorage.getItem('portraitSelections') || '{}');

    for (const id in selections) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `quantities[${id}]`;
        input.value = selections[id];
        form.appendChild(input);
    }

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




function scrollCarousel(direction) {
    const carousel = document.getElementById('portraitCarousel');
    const scrollAmount = carousel.clientWidth * 0.8 * direction;
    carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
}

//fullscreenImage
 function openModal(src) {
    const modal = document.getElementById('fullscreenModal');
    const image = document.getElementById('fullscreenImage');
    image.src = src;
    modal.classList.remove('hidden');
    history.pushState({ modalOpen: true }, ''); // Enable back button
  }

  function closeModal() {
    const modal = document.getElementById('fullscreenModal');
    if (modal) modal.classList.add('hidden');
  }

  // Close modal when clicking outside the image
  document.getElementById('fullscreenModal').addEventListener('click', function (e) {
    const container = document.getElementById('fullscreenContainer');
    if (!container.contains(e.target)) closeModal();
  });

  // Close on Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeModal();
  });

  // Close on Back button
  window.addEventListener('popstate', (e) => {
    const modal = document.getElementById('fullscreenModal');
    if (modal && !modal.classList.contains('hidden')) {
      closeModal();
    }
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

document.addEventListener('DOMContentLoaded', () => {

    const cartButton = document.getElementById('cartButton');
    const cartOverlay = document.getElementById('cart-overlay');
    const cartSidebar = document.getElementById('cartSidebar');
    const closeCartButton = document.getElementById('closeCart');
    const orderButton = document.getElementById('orderButton');
    const userDetailsModal = document.getElementById('user-details-modal');

    if(orderButton) {
        orderButton.addEventListener('click', () => {
                closeCart(); // Close the sidebar first
    setTimeout(() => {
        showUserDetailsModal(); // Open modal after cart closes
    }, 400); // Matches cart transition delay
        });
    }

    // --- These helpers now toggle the classes from YOUR CSS ---
    const openCart = () => {
        if(cartOverlay && cartSidebar) {
            cartOverlay.classList.remove('hidden');
            cartOverlay.classList.add('active'); // Use .active for overlay
            cartSidebar.classList.add('open');   // Use .open for sidebar
            document.body.style.overflow = 'hidden';
        }
    };

    const closeCart = () => {
        if(cartOverlay && cartSidebar) {
            cartOverlay.classList.remove('active');
            cartSidebar.classList.remove('open');
            // Hide the overlay after the transition finishes (400ms)
            setTimeout(() => cartOverlay.classList.add('hidden'), 400);
            document.body.style.overflow = '';
        }
    };

    // --- Event Listeners (no changes needed here) ---
    if(cartButton) cartButton.addEventListener('click', openCart);
    if(closeCartButton) closeCartButton.addEventListener('click', closeCart);
    if(cartOverlay) cartOverlay.addEventListener('click', (event) => {
        if (event.target === cartOverlay) {
            closeCart();
        }
    });

    // Close the user details modal with its cancel button
   document.querySelectorAll('#user-details-modal .close-modal-button').forEach(btn => {
    btn.addEventListener('click', () => {
        const overlay = document.getElementById('user-details-overlay');
        const modal = document.getElementById('user-details-modal');

       modal.classList.remove('show');
        if (overlay) overlay.classList.remove('active');

        setTimeout(() => {
            modal.classList.add('hidden');
            if (overlay) overlay.classList.add('hidden');
            document.body.style.overflow = '';
            window.location.reload(); // 🔄 Refresh the page after modal closes
        }, 300);

    });
});


});




 function showUserDetailsModal() {
  const inputs = document.querySelectorAll("input[name^='quantities']");
  let totalSelected = 0;
  inputs.forEach(input => totalSelected += parseInt(input.value) || 0);

  if (totalSelected === 0) {
    alert("Please select at least one portrait before proceeding.");
    return;
  }

  const overlay = document.getElementById('user-details-overlay');
  const modal = document.getElementById('user-details-modal');
  if (overlay && modal) {
    overlay.classList.remove('hidden');
    overlay.classList.add('active');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    modal.querySelector('input[name="name"]').focus();
  }
}


</script>

    
    
<style>
#cartSidebar {
    box-shadow: -8px 0 40px rgba(0, 0, 0, 0.2);
    transform: translateX(110%);
    transition: all 0.5s cubic-bezier(0.33, 1, 0.68, 1);
    will-change: transform, box-shadow;
    border-radius: 16px 0 0 16px;
    overflow: hidden;
    background: #ffffff;
    border-left: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    background-color: rgba(255, 255, 255, 0.95);
}

#cartSidebar.open {
    transform: translateX(0);
    box-shadow: -12px 0 50px rgba(0, 0, 0, 0.25);
}

#cartSidebar.closing {
    transform: translateX(110%);
    transition-timing-function: cubic-bezier(0.32, 0, 0.67, 0);
}

.cart-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to right, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.5));
    z-index: 40;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.5s cubic-bezier(0.33, 1, 0.68, 1), 
                backdrop-filter 0.5s ease;
    backdrop-filter: blur(0px);
    will-change: opacity, backdrop-filter;
}

.cart-overlay.active {
    opacity: 1;
    pointer-events: auto;
    backdrop-filter: blur(5px);
}

/* Optional: Add these for extra polish */
@keyframes subtleBounce {
    0% { transform: translateX(110%); }
    90% { transform: translateX(-3%); }
    100% { transform: translateX(0); }
}

#cartSidebar.open {
    animation: subtleBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

/* Add smooth scaling effect for items inside */
.cart-item {
    transition: transform 0.3s ease, opacity 0.3s ease;
    transform-origin: center;
}

.cart-item:hover {
    transform: scale(1.02);
    opacity: 0.9;
}

  #user-details-overlay {
    position: fixed;
    inset: 0;
    background: linear-gradient(to right, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.5));
    backdrop-filter: blur(5px);
    z-index: 50;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.4s ease, backdrop-filter 0.4s ease;
  }

  #user-details-overlay.active {
    opacity: 1;
    pointer-events: auto;
  }

  #user-details-modal {
    transform: translateY(40px);
    opacity: 0;
    transition: transform 0.4s ease, opacity 0.4s ease;
  }

  #user-details-modal.show {
    transform: translateY(0);
    opacity: 1;
  }

 #fullscreenModal {
    backdrop-filter: blur(10px);
    background-color: rgba(0, 0, 0, 0.6);
    transition: opacity 0.4s ease;
  }

  #fullscreenModal:not(.hidden) {
    animation: fadeInModal 0.3s ease-out forwards;
  }

  #fullscreenModal.hidden {
    animation: fadeOutModal 0.2s ease-in forwards;
  }

  @keyframes fadeInModal {
    from {
      opacity: 0;
    }
    to {
      opacity: 1;
    }
  }

  @keyframes fadeOutModal {
    from {
      opacity: 1;
    }
    to {
      opacity: 0;
    }
  }

  #fullscreenContainer img {
    transition: transform 0.4s ease;
    z-index: 10;
  }

  #fullscreenContainer img:hover {
    transform: scale(1.015);
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