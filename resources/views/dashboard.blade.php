<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="translate-x-full opacity-0"
                     x-transition:enter-end="translate-x-0 opacity-100"
                     x-transition:leave="transition ease-in duration-300 transform"
                     x-transition:leave-start="translate-x-0 opacity-100"
                     x-transition:leave-end="translate-x-full opacity-0"
                     class="fixed top-6 right-6 bg-green-600 text-white px-4 py-2 rounded shadow z-50">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="translate-x-full opacity-0"
                     x-transition:enter-end="translate-x-0 opacity-100"
                     x-transition:leave="transition ease-in duration-300 transform"
                     x-transition:leave-start="translate-x-0 opacity-100"
                     x-transition:leave-end="translate-x-full opacity-0"
                     class="fixed top-6 right-6 bg-red-600 text-white px-4 py-2 rounded shadow z-50">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('portraits.store') }}" enctype="multipart/form-data" class="bg-white shadow p-6 rounded-xl space-y-6">
                @csrf
                <div x-data="{ previewUrl: null }" class="space-y-2">
                    <label for="portrait" class="block text-sm font-medium text-gray-700 mb-2">Upload Portrait</label>
                    <label for="portrait" class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <template x-if="previewUrl">
                                <img :src="previewUrl" class="h-40 w-40 object-cover rounded shadow border border-gray-300">
                            </template>
                            <template x-if="!previewUrl">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-9 mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 2H15C16.1 2 17 2.9 17 4V5H18C19.1 5 20 5.9 20 7V20C20 21.1 19.1 22 18 22H6C4.9 22 4 21.1 4 20V7C4 5.9 4.9 5 6 5H7V4C7 2.9 7.9 2 9 2Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V10M12 10L9 13M12 10L15 13" />
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                                </div>
                            </template>
                        </div>
                        <input id="portrait" name="portrait" type="file" class="hidden" required @change="const file = $event.target.files[0]; if (file) previewUrl = URL.createObjectURL(file);" />
                    </label>
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (KSh)</label>
                    <input type="number" name="price" step="0.01" value="250" required class="w-full border-gray-300 rounded-xl px-4 py-2">
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold text-lg shadow-md flex items-center justify-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2m-4-4l-4-4m0 0l-4 4m4-4v12" />
                    </svg>
                    <span>Upload Portrait</span>
                </button>
            </form>

          

            <!-- Edit Modal -->
            <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                <div class="bg-white p-6 rounded-lg w-full max-w-md">
                    <h3 class="text-xl font-semibold mb-4">Edit Portrait</h3>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="editPrice" class="block text-gray-700 mb-2">Price (KSh)</label>
                            <input type="number" id="editPrice" name="price" class="w-full px-3 py-2 border rounded" required min="0" step="0.01">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Update</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function openEditModal(id, price) {
                    const modal = document.getElementById('editModal');
                    const form = document.getElementById('editForm');
                    const priceInput = document.getElementById('editPrice');
                    form.action = `/portraits/${id}`;
                    priceInput.value = price;
                    modal.classList.remove('hidden');
                }
                function closeEditModal() {
                    document.getElementById('editModal').classList.add('hidden');
                }
            </script>
        </div>
    </div>
</x-app-layout>
