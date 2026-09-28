@extends('layouts.base')

@section('content')
<main class="w-full max-w-6xl mx-auto py-16 px-4 min-h-screen">
  <!-- Hero Section Added -->
  <section class="bg-gradient-to-r from-green-600 to-green-800 text-white py-8 md:py-12 mb-8 rounded-xl">
    <div class="container mx-auto px-4 text-center">
     
    </div>
  </section>

<div class="bg-white rounded-xl shadow-lg overflow-hidden p-4 sm:p-6 lg:p-8">







<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full">

  <!-- Cart Items - Left Column -->
  <div class="w-full bg-gray-50 rounded-lg shadow-sm">

    <form id="cart-order-form" method="POST" action="{{ route('cart.store') }}" class="space-y-6">
      @csrf

      <input type="hidden" name="currency" id="currencyInput">

  <div class=" space-y-6">
    <!-- Portraits Card - Improved -->
<div class="w-full bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
    <div class="bg-green-600 text-white px-4 sm:px-6 py-3">
        <div class="flex items-center">
            <i class="fas fa-portrait text-white mr-2 sm:mr-3 text-lg"></i>
            <h3 class="text-base sm:text-lg font-semibold">Portraits</h3>
        </div>
    </div>
    
    <div class="p-4 sm:p-6">
        <div class="overflow-x-auto">
            <table class="w-full min-w-max md:w-full">
                <thead>
                    <tr class="bg-green-50 text-green-800 text-sm sm:text-base">
                        <th class="text-left py-2 px-2 sm:px-4 w-[40%]">Portrait</th>
                        <th class="text-center py-2 px-1 w-[15%]">Qty</th>
                        <th class="text-right py-2 px-2 sm:px-3 w-[15%]">Price</th>
                        <th class="text-right py-2 px-2 sm:px-3 w-[15%]">Subtotal</th>
                        <th class="text-right py-2 px-2 sm:px-4 w-[15%]"></th>
                    </tr>
                </thead>
                <tbody id="checkout-summary-body" class="divide-y divide-green-100">
                    <!-- Empty State -->
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-6 sm:py-8">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-box-open text-3xl sm:text-4xl text-gray-300 mb-2 sm:mb-3"></i>
                                <p class="text-sm sm:text-base">Your portrait cart is empty</p>
                                <a href="" 
                                   class="mt-3 text-sm text-green-600 hover:text-green-800 font-medium flex items-center">
                                    <i class="fas fa-arrow-left mr-2"></i> Browse Portraits
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

   <!-- Decor Card - Improved -->
<div class="w-full bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
    <div class="bg-green-600 text-white px-4 sm:px-6 py-3">
        <div class="flex items-center">
            <i class="fas fa-images text-white mr-2 sm:mr-3 text-lg"></i>
            <h3 class="text-base sm:text-lg font-semibold">Decor</h3>
           
        </div>
    </div>
    
    <div class="p-4 sm:p-6">
        <div class="overflow-x-auto">
            <table class="w-full min-w-max md:w-full">
                <thead>
                    <tr class="bg-green-50 text-green-800 text-sm sm:text-base">
                        <th class="text-left py-2 px-2 sm:px-4">Decor</th>
                        <th class="text-center py-2 px-1">Qty</th>
                        <th class="text-right py-2 px-2 sm:px-3">Price</th>
                        <th class="text-right py-2 px-2 sm:px-3">Subtotal</th>
                        <th class="text-right py-2 px-2 sm:px-4">Action</th>
                    </tr>
                </thead>
                <tbody id="checkout-decor-body" class="divide-y divide-green-100">
                    <!-- Empty State -->
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-6 sm:py-8">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-images text-3xl sm:text-4xl text-gray-300 mb-2 sm:mb-3"></i>
                                <p class="text-sm sm:text-base mb-2">No decor in your cart</p>
                                <a href="" 
                                   class="text-sm text-green-600 hover:text-green-800 font-medium flex items-center">
                                    <i class="fas fa-plus-circle mr-2"></i> Add Decor
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

      <input type="hidden" name="portraitSelections" id="portraitSelectionsInput">
      <input type="hidden" name="decorSelections" id="decorSelectionsInput">
   
  </div>

<!-- Summary & Customer Info - Right Column -->
  <div class="w-full">
    <div class="sticky top-4 space-y-4 sm:space-y-6">
        <!-- Customer Information Card -->
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4 flex items-center">
                <i class="fas fa-user text-green-600 mr-2 sm:mr-3"></i>
                Your Details
            </h3>
            <div class="space-y-3 sm:space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 sm:mb-2" for="name">Full Name *</label>
                    <input class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" 
                           required name="name" id="name" type="text" >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 sm:mb-2" for="phone">Phone Number *</label>
                    <input class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" 
                           required name="phone" id="phone" type="tel" >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 sm:mb-2" for="location">Delivery Location *</label>
                    <input class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" 
                           required name="location" id="location" type="text" >
                </div>
            </div>
        </div>

        <!-- Order Summary Card -->
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4 flex items-center">
                <i class="fas fa-receipt text-green-600 mr-2 sm:mr-3"></i>
                Order Summary
            </h3>

            <div class="space-y-2 sm:space-y-3 mb-3 sm:mb-4">
                <div class="flex justify-between text-gray-700 text-sm sm:text-base">
                    <span>Portraits Subtotal:</span>
                    <span id="summary-portraits-total" class="font-medium">KSh 0</span>
                </div>
                <div class="flex justify-between text-gray-700 text-sm sm:text-base">
                    <span>Decor Subtotal:</span>
                    <span id="summary-decor-total" class="font-medium">KSh 0</span>
                </div>
                <div class="flex justify-between text-gray-700 text-sm sm:text-base">
                    <span>Delivery Fee:</span>
                    <span id="delivery-fee" class="font-medium">KSh 0</span>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-3 sm:pt-4 mb-4 sm:mb-5">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-gray-800 text-base sm:text-lg">Total Amount:</span>
                    <span id="total" class="text-lg sm:text-xl font-bold text-green-600">KSh 0</span>
                </div>
            </div>

            <button type="submit" form="cart-order-form" 
                    class="w-full px-4 sm:px-6 py-2 sm:py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition flex items-center justify-center gap-2 sm:gap-3">
                <i class="fas fa-check-circle"></i>
                <span>Confirm Order</span>
            </button>
            
            <!-- Added security notice -->
            <p class="text-xs text-gray-500 mt-3 text-center">
                <i class="fas fa-lock mr-1"></i> Your information is secure and will not be shared
            </p>
        </div>
    </div>
</div>
 </form> <!-- Form closing tag now in correct position -->

</div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {

  /* ================================
     1️⃣ SAFE CONFIG LOADING
  ================================= */

  if (!window.APP_PRICING || !window.APP_PRICING.currencies) {
    console.error('Pricing configuration missing.');
    return;
  }

  const PRICING = window.APP_PRICING.currencies;
  const BULK_THRESHOLD = window.APP_PRICING.bulk_threshold || 5;

  let currency = localStorage.getItem('preferredCurrency') || 'KES';

  if (!PRICING[currency]) {
    console.warn('Invalid currency detected. Falling back to KES.');
    currency = 'KES';
  }

  const cfg = PRICING[currency];

  document.getElementById('currencyInput').value = currency;


  /* ================================
     2️⃣ LOAD CART DATA
  ================================= */

  const portraitSelections = JSON.parse(localStorage.getItem('portraitSelections') || '{}');
  const decorSelections = JSON.parse(localStorage.getItem('decorSelections') || '{}');

  const portraitBody = document.getElementById('checkout-summary-body');
  const decorBody = document.getElementById('checkout-decor-body');

  const portraitInput = document.getElementById('portraitSelectionsInput');
  const decorInput = document.getElementById('decorSelectionsInput');

  const subtotalEl = document.getElementById('summary-portraits-total');
  const decorSubtotalEl = document.getElementById('summary-decor-total');
  const deliveryFeeEl = document.getElementById('delivery-fee');
  const totalEl = document.getElementById('total');

  portraitBody.innerHTML = '';
  decorBody.innerHTML = '';


  /* ================================
     3️⃣ PORTRAITS CALCULATION
  ================================= */

  const portraitTotalUnits = Object.values(portraitSelections)
    .reduce((sum, qty) => sum + parseInt(qty, 10), 0);

  const portraitUnitPrice = portraitTotalUnits >= BULK_THRESHOLD
    ? cfg.portraits.tier2
    : cfg.portraits.tier1;

  let portraitSubtotal = 0;

  for (const [id, qtyStr] of Object.entries(portraitSelections)) {
    const qty = parseInt(qtyStr, 10);

    if (qty > 0) {
      const rowSub = qty * portraitUnitPrice;
      portraitSubtotal += rowSub;

      const row = `
<tr class="hover:bg-green-50 transition-colors border-b border-green-100">
  <td class="px-3 py-2 text-left">
    <div class="text-gray-800 font-medium text-sm">Portrait #${id}</div>
  </td>
  <td class="px-2 py-2 text-center">
    <span class="bg-gray-100 rounded-full px-3 py-1 text-xs font-semibold">${qty}</span>
  </td>
  <td class="px-3 py-2 text-right">
    ${cfg.symbol} ${portraitUnitPrice.toLocaleString()}
  </td>
  <td class="px-3 py-2 text-right font-semibold">
    ${cfg.symbol} ${rowSub.toLocaleString()}
  </td>
  <td class="px-3 py-2 text-right">
    <button type="button"
      data-remove-portrait="${id}"
      class="remove-portrait-btn text-red-600 hover:text-red-800 text-sm">
      Remove
    </button>
  </td>
</tr>
`;
      portraitBody.insertAdjacentHTML('beforeend', row);
    }
  }

  if (portraitSubtotal === 0) {
    portraitBody.innerHTML =
      '<tr><td colspan="5" class="text-center py-6 text-gray-500">Your portrait cart is empty.</td></tr>';
  }


  /* ================================
     4️⃣ DECOR CALCULATION
  ================================= */

  const decorTotalUnits = Object.values(decorSelections)
    .reduce((sum, qty) => sum + parseInt(qty, 10), 0);

  const decorPricing = cfg.decors || cfg.clocks;

  const decorUnitPrice = decorTotalUnits >= BULK_THRESHOLD
    ? decorPricing.tier2
    : decorPricing.tier1;

  let decorSubtotal = 0;

  for (const [id, qtyStr] of Object.entries(decorSelections)) {
    const qty = parseInt(qtyStr, 10);

    if (qty > 0) {
      const rowSub = qty * decorUnitPrice;
      decorSubtotal += rowSub;

      const row = `
<tr class="hover:bg-green-50 transition-colors border-b border-green-100">
  <td class="px-3 py-2 text-left">
    <div class="text-gray-800 font-medium text-sm">Decor #${id}</div>
  </td>
  <td class="px-2 py-2 text-center">
    <span class="bg-gray-100 rounded-full px-3 py-1 text-xs font-semibold">${qty}</span>
  </td>
  <td class="px-3 py-2 text-right">
    ${cfg.symbol} ${decorUnitPrice.toLocaleString()}
  </td>
  <td class="px-3 py-2 text-right font-semibold">
    ${cfg.symbol} ${rowSub.toLocaleString()}
  </td>
  <td class="px-3 py-2 text-right">
    <button type="button"
      data-remove-decor="${id}"
      class="remove-decor-btn text-red-600 hover:text-red-800 text-sm">
      Remove
    </button>
  </td>
</tr>
`;
      decorBody.insertAdjacentHTML('beforeend', row);
    }
  }

  if (decorSubtotal === 0) {
    decorBody.innerHTML =
      '<tr><td colspan="5" class="text-center py-6 text-gray-500">Your decor cart is empty.</td></tr>';
  }


  /* ================================
     5️⃣ TOTALS
  ================================= */

  const totalUnits = portraitTotalUnits + decorTotalUnits;
  const deliveryFee = totalUnits > 0 ? cfg.delivery : 0;

  const fullSubtotal = portraitSubtotal + decorSubtotal;
  const finalTotal = fullSubtotal + deliveryFee;

  subtotalEl.textContent = `${cfg.symbol} ${portraitSubtotal.toLocaleString()}`;
  decorSubtotalEl.textContent = `${cfg.symbol} ${decorSubtotal.toLocaleString()}`;
  deliveryFeeEl.textContent = `${cfg.symbol} ${deliveryFee.toLocaleString()}`;
  totalEl.textContent = `${cfg.symbol} ${finalTotal.toLocaleString()}`;


  /* ================================
     6️⃣ PASS DATA TO BACKEND
  ================================= */

  portraitInput.value = JSON.stringify(portraitSelections);
  decorInput.value = JSON.stringify(decorSelections);


  /* ================================
     7️⃣ REMOVE BUTTON HANDLING
  ================================= */

  document.addEventListener('click', function (e) {

    const portraitBtn = e.target.closest('.remove-portrait-btn');
    if (portraitBtn) {
      const id = portraitBtn.dataset.removePortrait;
      removeItem('portraitSelections', id);
    }

    const decorBtn = e.target.closest('.remove-decor-btn');
    if (decorBtn) {
      const id = decorBtn.dataset.removeDecor;
      removeItem('decorSelections', id);
    }

  });

});


/* ================================
   REUSABLE REMOVE FUNCTION
================================= */

function removeItem(storageKey, id) {
  const selections = JSON.parse(localStorage.getItem(storageKey) || '{}');
  delete selections[id];
  localStorage.setItem(storageKey, JSON.stringify(selections));
  location.reload(); // safe reload to re-render
}
</script>


@endsection
