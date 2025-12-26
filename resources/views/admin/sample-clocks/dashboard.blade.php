<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sample Clocks Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Success Message --}}
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

            {{-- Error Message --}}
            @if($errors->any())
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="fixed top-6 right-6 bg-red-600 text-white px-4 py-2 rounded shadow z-50">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Upload Form --}}
            <form method="POST"
                  action="{{ route('admin.sample-clocks.store') }}"
                  enctype="multipart/form-data"
                  class="bg-white shadow p-6 rounded-xl space-y-6">
                @csrf

                <div x-data="{ previewUrl: null }" class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Sample Clock
                    </label>

                    <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <template x-if="previewUrl">
                            <img :src="previewUrl" class="h-40 w-40 object-cover rounded shadow border">
                        </template>

                        <template x-if="!previewUrl">
                            <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-10 h-9 mb-3 text-gray-400"
                                     fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2m-4-4l-4-4m0 0l-4 4m4-4v12" />
                                </svg>
                                <p class="text-sm text-gray-500">
                                    <span class="font-semibold">Click to upload</span>
                                </p>
                                <p class="text-xs text-gray-500">PNG, JPG, WEBP</p>
                            </div>
                        </template>

                        <input type="file"
                               name="image"
                               class="hidden"
                               required
                               @change="previewUrl = URL.createObjectURL($event.target.files[0])">
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Price (KSh)
                    </label>
                    <input type="number"
                           name="price"
                           step="0.01"
                           required
                           class="w-full border-gray-300 rounded-xl px-4 py-2">
                </div>

                <button type="submit"
                        class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold text-lg shadow-md">
                    Upload Sample Clock
                </button>
            </form>

            {{-- Gallery --}}
            <h3 class="text-lg font-bold mt-6">Sample Clocks</h3>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                @foreach($sampleClocks as $clock)
                    <div class="bg-white rounded-xl shadow p-3">
                        <img src="{{ Storage::url($clock->clock_path) }}"
                             class="w-full h-40 object-cover rounded mb-2">

                        <p class="text-sm font-semibold mb-2">
                            KSh {{ number_format($clock->price, 2) }}
                        </p>

                        <div class="flex justify-between gap-2">
                            <button  class="flex-1 bg-blue-600 text-white py-1 rounded hover:bg-blue-700">
                                Edit
                            </button>

                            <form method="POST"
                                  action="{{ route('admin.sample-clocks.destroy', $clock) }}"
                                  onsubmit="return confirm('Delete this clock?')">
                                @csrf
                                @method('DELETE')
                                <button class="flex-1 bg-red-600 text-white py-1 rounded hover:bg-red-700">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Edit Modal --}}
            <div id="editModal"
                 class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                <div class="bg-white p-6 rounded-lg w-full max-w-md">
                    <h3 class="text-xl font-semibold mb-4">Edit Price</h3>

                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')

                        <input type="number"
                               id="editPrice"
                               name="price"
                               step="0.01"
                               class="w-full border rounded px-3 py-2 mb-4">

                        <div class="flex justify-end gap-3">
                            <button type="button"
                                    onclick="closeEditModal()"
                                    class="px-4 py-2 bg-gray-300 rounded">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function openEditModal(id, price) {
                    document.getElementById('editModal').classList.remove('hidden');
                    document.getElementById('editPrice').value = price;
                    document.getElementById('editForm').action = `/admin/sample-clocks/${id}`;
                }

                function closeEditModal() {
                    document.getElementById('editModal').classList.add('hidden');
                }
            </script>

        </div>
    </div>
</x-app-layout>
