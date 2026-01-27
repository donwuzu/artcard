@extends('layouts.base')

@section('title', 'Welcome')

@section('content')







<div class="relative mt-12">
    <div class="flex justify-between items-start p-4">
        <div class="flex-grow text-center md:text-left">
            <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">SAMPLE PORTRAITS</h2>
            <p class="text-gray-600">Explore our gallery and order custom portraits directly via Whatsapp.</p>
        </div>


        
    </div>
</div>


@auth
    <div style="
        width: 140px;
        margin: auto;
        padding: 10px;
        background: #0d9488;
        color: #ffffff;
        border-radius: 6px;
        text-align: center;
        font-family: Arial, sans-serif;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    "
    onmouseover="this.style.transform='scale(1.25)'; this.style.boxShadow='0 12px 25px rgba(0,0,0,0.3)'"
    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none'"
    >

        <!-- Avatar -->
        <div style="
            width: 36px;
            height: 36px;
            margin: auto;
            border-radius: 50%;
            background: #14b8a6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
        ">
            {{ substr(auth()->user()->name, 0, 1) }}
        </div>

        <!-- Name -->
        <p style="margin-top: 6px; font-size: 12px; font-weight: bold;">
            {{ auth()->user()->name }}
        </p>

        <!-- Role -->
        <p style="margin-top: 2px; font-size: 10px;">
            {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
        </p>

        <!-- Date -->
        <p style="margin-top: 4px; font-size: 9px; opacity: 0.8;">
            {{ date('M j, Y') }}
        </p>

    </div>
@endauth


<div class="w-full flex justify-end p-4">
    @guest
        <a href="{{ route('login') }}"
           class="inline-flex items-center
                  px-4 py-2 rounded-lg
                  text-sm sm:text-base font-bold
                  border border-green-700
                  text-white bg-green-600 shadow-md
                  transition-all duration-300 ease-out
                  hover:bg-green-700 hover:text-green-100 hover:border-green-800
                  hover:scale-105 hover:shadow-lg
                  active:scale-95
                  focus:outline-none focus:ring-4 focus:ring-green-400 focus:ring-offset-1">
            Log In
        </a>
    @else
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="inline-flex items-center
                       px-4 py-2 rounded-lg
                       text-sm sm:text-base font-bold
                       border border-red-700
                       text-white bg-red-600 shadow-md
                       transition-all duration-300 ease-out
                       hover:bg-red-700 hover:text-red-100 hover:border-red-800
                       hover:scale-105 hover:shadow-lg
                       active:scale-95
                       focus:outline-none focus:ring-4 focus:ring-red-400 focus:ring-offset-1">
                Log Out
            </button>
        </form>
    @endguest
</div>









<!-- Updated Carousel View -->
<!-- Image Grid -->
<div id="gridView" class="px-4 py-6">
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
        @foreach ($sampleImages as $image)
            <div
                class="group bg-white rounded-2xl shadow-md overflow-hidden
                       transition-all duration-300 hover:shadow-2xl hover:-translate-y-1
                       cursor-pointer"
                onclick="openModal('{{ Storage::url($image->image_path) }}')"
            >
                <div class="relative">
                    <img
                        src="{{ Storage::url($image->image_path) }}"
                        alt="Sample Image {{ $image->id }}"
                        class="w-full h-56 sm:h-64 object-cover
                               transition-transform duration-300 group-hover:scale-105"
                    />

                    <!-- Hover overlay -->
                    <div class="absolute inset-0 bg-black/10 opacity-0
                                group-hover:opacity-100 transition pointer-events-none"></div>
                </div>

          




            </div>
        @endforeach
    </div>
</div>


<!-- Shared Styles -->
<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .snap-x { scroll-snap-type: x mandatory; }
    .snap-center { scroll-snap-align: center; }
</style>




<!-- Pagination -->
<div class="mt-8 mb-8 px-4 flex justify-center">
    {{ $sampleImages->links() }}
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



    
  <div style="
    background: linear-gradient(135deg, #f8fafc, #eef2f7);
    padding: 24px 16px;
    text-align: center;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 13px;
    color: #6b7280;
    border-top: 1px solid #e5e7eb;
">
    <span style="display: inline-block; letter-spacing: 0.3px;">
        &copy;
        <script>document.write(new Date().getFullYear())</script>
        <strong style="color:#374151;">ARTCARD Company</strong>.
        All rights reserved.
    </span>
</div>


<script>

document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('currency-select');
    const hiddenInput = document.getElementById('currency');
    const codeSpan = document.getElementById('currency-code');
    const flagImg = document.getElementById('currency-flag');
    const prices = document.querySelectorAll('.price');

    const currencyConfig = {
        KES: { flag: 'ke', symbol: 'KSh', rate: 1 },
        UGX: { flag: 'ug', symbol: 'UGX', rate: 96 },
        TZS: { flag: 'tz', symbol: 'TZS', rate: 17 },
        RWF: { flag: 'rw', symbol: 'RWF', rate: 8 },
    };

    function applyCurrency(currency) {
        const config = currencyConfig[currency];

        hiddenInput.value = currency;
        codeSpan.textContent = currency;

        flagImg.src = `https://flagcdn.com/w20/${config.flag}.png`;
        flagImg.srcset = `https://flagcdn.com/w40/${config.flag}.png 2x`;
        flagImg.alt = currency;

        prices.forEach(priceEl => {
            const basePrice = parseFloat(priceEl.dataset.basePrice);
            const converted = Math.round(basePrice * config.rate);
            priceEl.textContent = `${config.symbol} ${converted.toLocaleString()}`;
        });
    }

    // ✅ Init with saved or default UGX
    const savedCurrency = localStorage.getItem('preferredCurrency') || 'UGX';
    select.value = savedCurrency;
    applyCurrency(savedCurrency);

    select.addEventListener('change', function () {
        localStorage.setItem('preferredCurrency', this.value);
        applyCurrency(this.value);
    });
});





function setupAjaxPagination() {
    document.querySelectorAll('.pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            fetchPortraits(this.href);
        });
    });
}

function setupAjaxPagination() {
    document.querySelectorAll('.pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            fetchPortraits(this.href);
        });
    });
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



</script>

    
    
<style>
/* --- Sidebar Styles --- */
#cartSidebar {
  position: fixed;
  top: 80px; /* leave space for fixed navbar */
  right: 0;
  width: 100%;
  max-width: 380px;

    /* ✅ Flexible height */
  max-height: calc(100vh - 120px); /* leaves breathing room at bottom */
  height: auto;                   /* grow with content */

  display: flex;
  flex-direction: column;

  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);

  border-left: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 16px 0 0 16px;
  overflow: hidden;

  transform: translateX(110%);
  box-shadow: -8px 0 40px rgba(0, 0, 0, 0.15);
  transition: transform 0.55s cubic-bezier(0.33, 1, 0.68, 1),
              box-shadow 0.4s ease;
  will-change: transform, box-shadow;
}

/* ✅ Scroll only inside the cart content if needed */
#cartSidebar .cart-content {
  flex: 1;
  overflow-y: auto;
  padding: 1rem;
  min-height: 100px; /* prevents collapsing too much when empty */
    max-height: calc(100vh - 280px); /* keeps footer visible, avoids bottom */
}

#cartSidebar.open {
  transform: translateX(0);
  box-shadow: -12px 0 50px rgba(0, 0, 0, 0.25);
  animation: subtleBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

#cartSidebar.closing {
  transform: translateX(110%);
  transition-timing-function: cubic-bezier(0.32, 0, 0.67, 0);
}

/* --- Overlay Styles --- */
.cart-overlay {
  position: fixed;
  inset: 0;
  z-index: 40;

  background: linear-gradient(
    to right,
    rgba(0, 0, 0, 0.25),
    rgba(0, 0, 0, 0.55)
  );

  opacity: 0;
  pointer-events: none;

  backdrop-filter: blur(0px);
  -webkit-backdrop-filter: blur(0px);

  transition: opacity 0.5s cubic-bezier(0.33, 1, 0.68, 1),
              backdrop-filter 0.5s ease;
  will-change: opacity, backdrop-filter;
}

.cart-overlay.active {
  opacity: 1;
  pointer-events: auto;
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
}

/* --- Animations --- */
@keyframes subtleBounce {
  0%   { transform: translateX(110%); }
  60%  { transform: translateX(-12px); }
  80%  { transform: translateX(6px); }
  100% { transform: translateX(0); }
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





  /* Animation for the continuous pulsing glow */
    @keyframes pulse-glow {
        0%, 100% {
            box-shadow: 0 0 15px 5px rgba(37, 211, 102, 0.6);
        }
        50% {
            box-shadow: 0 0 25px 10px rgba(37, 211, 102, 0.4);
        }
    }

    /* Base style for the floating button container */
    .glowing-button {
        position: fixed;
        bottom: 1.5rem; /* 24px */
        right: 1.5rem;  /* 24px */
        z-index: 30;
        /* Applying the glow animation here */
        animation: pulse-glow 2.5s infinite ease-in-out;
        border-radius: 9999px; /* Make the glow match the button shape */
    }
    
    /* The visible content part of the button (icon and text) */
    .glowing-button-content {
        display: flex;
        align-items: center;
        background-color: #25D366; /* WhatsApp Green */
        color: white;
        padding: 0.75rem 1rem; /* 12px 16px */
        border-radius: 9999px; /* Fully rounded pill shape */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    /* Hover effect for the button content */
    .glowing-button:hover .glowing-button-content {
        background-color: #128C7E; /* Darker WhatsApp Green */
        transform: translateY(-2px); /* Slight lift effect */
    }
    
    /* Style for the icon inside the button */
    .glowing-button-icon {
        font-size: 1.25rem; /* 20px */
        height: 1.25rem; /* 20px */
        width: 1.25rem;  /* 20px */
    }
    
    /* The text that appears on hover */
    .glowing-button .text {
        margin-left: 0.5rem; /* 8px */
        max-width: 0;
        overflow: hidden;
        white-space: nowrap;
        transition: max-width 0.4s ease, opacity 0.3s ease 0.1s;
        opacity: 0;
        font-weight: 600;
    }
    
    /* When the main container is hovered, expand the text */
    .glowing-button:hover .text {
        max-width: 200px; /* Max width for the text to expand to */
        opacity: 1;
    }
</style>







        

@endsection