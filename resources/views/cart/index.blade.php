@extends('layouts.base')

@section('content')
<main class="w-full max-w-6xl mx-auto py-16 px-4 min-h-screen">
  <!-- Hero Section Added -->
  <section class="bg-gradient-to-r from-green-600 to-green-800 text-white py-8 md:py-12 mb-8 rounded-xl">
    <div class="container mx-auto px-4 text-center">
     
    </div>
  </section>

<div class="bg-white rounded-xl shadow-lg overflow-hidden p-4 sm:p-6 lg:p-8">
    <!-- Cart Header -->
   <div class="bg-green-600 text-white px-4 sm:px-6 py-4">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
    <h2 class="text-xl font-bold">Order Details</h2>
    <p class="text-green-100 text-sm sm:text-base">Review your cart items and complete your information</p>
  </div>
</div>




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

   <!-- Clocks Card - Improved -->
<div class="w-full bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
    <div class="bg-green-600 text-white px-4 sm:px-6 py-3">
        <div class="flex items-center">
            <i class="fas fa-clock text-white mr-2 sm:mr-3 text-lg"></i>
            <h3 class="text-base sm:text-lg font-semibold">Clocks</h3>
           
        </div>
    </div>
    
    <div class="p-4 sm:p-6">
        <div class="overflow-x-auto">
            <table class="w-full min-w-max md:w-full">
                <thead>
                    <tr class="bg-green-50 text-green-800 text-sm sm:text-base">
                        <th class="text-left py-2 px-2 sm:px-4">Clock</th>
                        <th class="text-center py-2 px-1">Qty</th>
                        <th class="text-right py-2 px-2 sm:px-3">Price</th>
                        <th class="text-right py-2 px-2 sm:px-3">Subtotal</th>
                        <th class="text-right py-2 px-2 sm:px-4">Action</th>
                    </tr>
                </thead>
                <tbody id="checkout-clocks-body" class="divide-y divide-green-100">
                    <!-- Empty State -->
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-6 sm:py-8">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-clock text-3xl sm:text-4xl text-gray-300 mb-2 sm:mb-3"></i>
                                <p class="text-sm sm:text-base mb-2">No clocks in your cart</p>
                                <a href="" 
                                   class="text-sm text-green-600 hover:text-green-800 font-medium flex items-center">
                                    <i class="fas fa-plus-circle mr-2"></i> Add Clocks
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
      <input type="hidden" name="clockSelections" id="clockSelectionsInput">
   
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
                           required name="name" id="name" type="text" placeholder="John Doe">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 sm:mb-2" for="phone">Phone Number *</label>
                    <input class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" 
                           required name="phone" id="phone" type="tel" placeholder="0712 345 678">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 sm:mb-2" for="location">Delivery Location *</label>
                    <input class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" 
                           required name="location" id="location" type="text" placeholder="Nairobi, Westlands">
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
                    <span>Clocks Subtotal:</span>
                    <span id="summary-clocks-total" class="font-medium">KSh 0</span>
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
  const portraitSelections = JSON.parse(localStorage.getItem('portraitSelections') || '{}');
  const clockSelections = JSON.parse(localStorage.getItem('clockSelections') || '{}');

  const portraitBody = document.getElementById('checkout-summary-body');
  const clockBody = document.getElementById('checkout-clocks-body');

  const portraitInput = document.getElementById('portraitSelectionsInput');
  const clockInput = document.getElementById('clockSelectionsInput');

  const subtotalEl = document.getElementById('summary-portraits-total');
  const deliveryFeeEl = document.getElementById('delivery-fee');
  const totalEl = document.getElementById('total');
  const clocksSubtotalEl = document.getElementById('summary-clocks-total');

  // ✅ 1. Get current currency + config
  const currency = localStorage.getItem('preferredCurrency') || 'KES';
  document.getElementById('currencyInput').value = currency; // hidden input for Laravel

  const pricing = {
    KES: { symbol: "KSh", portraits: { tier1: 250, tier2: 190 }, clocks: { tier1: 700, tier2: 500 }, delivery: 300 },
    UGX: { symbol: "UGX", portraits: { tier1: 20000, tier2: 15000 }, clocks: { tier1: 50000, tier2: 38000 }, delivery: 10000 },
    TZS: { symbol: "TSh", portraits: { tier1: 5000, tier2: 4000 }, clocks: { tier1: 45000, tier2: 32000 }, delivery: 3000 },
    RWF: { symbol: "FRw", portraits: { tier1: 2500, tier2: 2000 }, clocks: { tier1: 20000, tier2: 12000 }, delivery: 1500 }
  };
  const cfg = pricing[currency];

  // Reset tables
  portraitBody.innerHTML = '';
  clockBody.innerHTML = '';

  /** ---------------- PORTRAITS ---------------- */
  const portraitTotalUnits = Object.values(portraitSelections).reduce((sum, qty) => sum + parseInt(qty, 10), 0);
  const portraitUnitPrice = portraitTotalUnits >= 5 ? cfg.portraits.tier2 : cfg.portraits.tier1;

  let portraitSubtotal = 0;
  for (const [id, qtyStr] of Object.entries(portraitSelections)) {
    const qty = parseInt(qtyStr, 10);
    if (qty > 0) {
      const rowSub = qty * portraitUnitPrice;
      portraitSubtotal += rowSub;

      const row = `
     <tr class="hover:bg-green-50 transition-colors border-b border-green-100">
  <!-- Portrait Name -->
  <td class="px-3 py-2 sm:px-4 sm:py-3 text-left">
    <div class="text-gray-800 font-medium line-clamp-1 text-sm sm:text-base">
      Portrait #${id}
    </div>
  </td>

  <!-- Quantity -->
  <td class="px-1 py-2 text-center">
    <span class="inline-block bg-gray-100 text-gray-700 rounded-full px-2 sm:px-3 py-1 text-xs sm:text-sm font-semibold min-w-[2rem]">
      ${qty}
    </span>
  </td>

  <!-- Unit Price -->
  <td class="px-2 py-2 sm:px-3 sm:py-3 text-right">
    <span class="text-gray-700 font-medium whitespace-nowrap text-sm sm:text-base">
      ${cfg.symbol} ${portraitUnitPrice.toLocaleString()}
    </span>
  </td>

  <!-- Subtotal -->
  <td class="px-2 py-2 sm:px-3 sm:py-3 text-right">
    <span class="text-gray-900 font-semibold whitespace-nowrap text-sm sm:text-base">
      ${cfg.symbol} ${rowSub.toLocaleString()}
    </span>
  </td>

  <!-- Remove Button -->
  <td class="px-2 py-2 sm:px-3 sm:py-3">
    <div class="flex justify-center sm:justify-end">
      <button
        type="button"
        onclick="removePortrait('${id}')"
        class="text-red-600 hover:text-red-800 font-medium px-2 py-1 rounded-md hover:bg-red-50 transition text-xs sm:text-sm inline-flex items-center"
        aria-label="Remove Portrait #${id}"
        title="Remove portrait"
      >
        <i class="fas fa-times"></i>
        <span class="ml-1 hidden sm:inline">Remove</span>
      </button>
    </div>
  </td>
</tr>
`;
      portraitBody.insertAdjacentHTML('beforeend', row);
    }
  }
  if (portraitSubtotal === 0) {
    portraitBody.innerHTML = '<tr><td colspan="5">Your portrait cart is empty.</td></tr>';
  }

  /** ---------------- CLOCKS ---------------- */
  const clockTotalUnits = Object.values(clockSelections).reduce((sum, qty) => sum + parseInt(qty, 10), 0);
  const clockUnitPrice = clockTotalUnits >= 5 ? cfg.clocks.tier2 : cfg.clocks.tier1;

  let clockSubtotal = 0;
  for (const [id, qtyStr] of Object.entries(clockSelections)) {
    const qty = parseInt(qtyStr, 10);
    if (qty > 0) {
      const rowSub = qty * clockUnitPrice;
      clockSubtotal += rowSub;

      const row = `
     <tr class="hover:bg-green-50 transition-colors border-b border-green-100">
  <!-- Clock Name -->
  <td class="px-3 py-2 sm:px-4 sm:py-3 text-left">
    <div class="text-gray-800 font-medium line-clamp-1 text-sm sm:text-base">
      Clock #${id}
    </div>
  </td>

  <!-- Quantity -->
  <td class="px-1 py-2 text-center">
    <span class="inline-block bg-gray-100 text-gray-700 rounded-full px-2 sm:px-3 py-1 text-xs sm:text-sm font-semibold min-w-[2rem]">
      ${qty}
    </span>
  </td>

  <!-- Unit Price -->
  <td class="px-2 py-2 sm:px-3 sm:py-3 text-right">
    <span class="text-gray-700 font-medium whitespace-nowrap text-sm sm:text-base">
      ${cfg.symbol} ${clockUnitPrice.toLocaleString()}
    </span>
  </td>

  <!-- Subtotal -->
  <td class="px-2 py-2 sm:px-3 sm:py-3 text-right">
    <span class="text-gray-900 font-semibold whitespace-nowrap text-sm sm:text-base">
      ${cfg.symbol} ${rowSub.toLocaleString()}
    </span>
  </td>

  <!-- Remove Button -->
  <td class="px-2 py-2 sm:px-3 sm:py-3">
    <div class="flex justify-center sm:justify-end">
      <button
        type="button"
        onclick="removeClock('${id}')"
        class="text-red-600 hover:text-red-800 font-medium px-2 py-1 rounded-md hover:bg-red-50 transition text-xs sm:text-sm inline-flex items-center"
        aria-label="Remove Clock #${id}"
        title="Remove clock"
      >
        <i class="fas fa-times"></i>
        <span class="ml-1 hidden sm:inline">Remove</span>
      </button>
    </div>
  </td>
</tr>
`;
      clockBody.insertAdjacentHTML('beforeend', row);
    }
  }
  if (clockSubtotal === 0) {
    clockBody.innerHTML = '<tr><td colspan="5">Your clock cart is empty.</td></tr>';
  }

  /** ---------------- TOTALS ---------------- */
  const totalUnits = portraitTotalUnits + clockTotalUnits;
  const deliveryFee = totalUnits > 0 ? cfg.delivery : 0;
  const fullSubtotal = portraitSubtotal + clockSubtotal;
  const finalTotal = fullSubtotal + deliveryFee;

  subtotalEl.textContent = `${cfg.symbol} ${portraitSubtotal.toLocaleString()}`;
  clocksSubtotalEl.textContent = `${cfg.symbol} ${clockSubtotal.toLocaleString()}`;
  deliveryFeeEl.textContent = `${cfg.symbol} ${deliveryFee.toLocaleString()}`;
  totalEl.textContent = `${cfg.symbol} ${finalTotal.toLocaleString()}`;

  // ✅ Pass data to backend
  portraitInput.value = JSON.stringify(portraitSelections);
  clockInput.value = JSON.stringify(clockSelections);
});


function removePortrait(id) {
  const selections = JSON.parse(localStorage.getItem('portraitSelections') || '{}');
  delete selections[id];
  localStorage.setItem('portraitSelections', JSON.stringify(selections));
  location.reload();
}

function removeClock(id) {
  const selections = JSON.parse(localStorage.getItem('clockSelections') || '{}');
  delete selections[id];
  localStorage.setItem('clockSelections', JSON.stringify(selections));
  location.reload();
}
</script>


@endsection

