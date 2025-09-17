<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Users Management') }}
            </h2>
            <div class="text-sm text-gray-600">
                Total Users: <span class="font-bold">{{ $users->total() }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">

                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="mb-6 px-4 py-3 rounded-lg border border-green-200 bg-green-50 text-green-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Search and Filter Bar --}}
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <form method="GET" action="{{ route('users.index') }}" class="flex flex-1 max-w-md">
                            <div class="relative flex-1">
                                <input type="text" name="search" value="{{ $search ?? '' }}"
                                    placeholder="Search by name, email, or type..."
                                    class="w-full border border-gray-300 rounded-l-lg px-4 py-2.5 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2.5 rounded-r-lg hover:bg-blue-700 transition flex items-center">
                                <span>Search</span>
                            </button>
                        </form>
                        
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Filter by:</span>
                            <a href="{{ route('users.index', ['filter' => 'all']) }}" 
                               class="px-3 py-1.5 text-sm rounded-lg {{ (!request('filter') || request('filter') == 'all') ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                All
                            </a>
                            <a href="{{ route('users.index', ['filter' => 'admin']) }}" 
                               class="px-3 py-1.5 text-sm rounded-lg {{ request('filter') == 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                Admins
                            </a>
                            <a href="{{ route('users.index', ['filter' => 'client']) }}" 
                               class="px-3 py-1.5 text-sm rounded-lg {{ request('filter') == 'client' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                Clients
                            </a>
                        </div>
                    </div>

                    {{-- Users Table --}}
                    <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-gray-50 transition group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-medium">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                    <div class="text-xs text-gray-500">ID: {{ $user->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form method="POST" action="{{ route('users.update', $user) }}" class="inline-flex">
                                                @csrf
                                                @method('PUT')
                                                <div class="relative">
                                                    <select name="user_type" onchange="this.form.submit()" 
                                                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md font-medium
                                                        {{ $user->user_type === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                                        <option value="admin" {{ $user->user_type === 'admin' ? 'selected' : '' }}>Admin</option>
                                                        <option value="client" {{ $user->user_type === 'client' ? 'selected' : '' }}>Client</option>
                                                    </select>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center space-x-2">
                                                <a href="#" class="text-blue-600 hover:text-blue-900 transition p-1.5 rounded-md hover:bg-blue-50">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                </a>
                                                <form method="POST" action="{{ route('users.destroy', $user) }}" 
                                                      onsubmit="return confirm('Are you sure you want to delete {{ $user->name }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 transition p-1.5 rounded-md hover:bg-red-50">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <h3 class="mt-4 text-sm font-medium text-gray-900">No users found</h3>
                                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter to find what you're looking for.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>