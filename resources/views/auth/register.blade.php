@extends('layouts.new')

@section('form')
    <h2 class="text-2xl font-semibold mb-6">Create Your Account</h2>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium">Name</label>
            <input id="name" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name" 
                   class="w-full mt-1 p-2 rounded-md border border-gray-400 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            @error('name')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium">Email</label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autocomplete="username" 
                   class="w-full mt-1 p-2 rounded-md border border-gray-400 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            @error('email')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium">Password</label>
            <input id="password" 
                   type="password" 
                   name="password" 
                   required 
                   autocomplete="new-password" 
                   class="w-full mt-1 p-2 rounded-md border border-gray-400 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            @error('password')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium">Confirm Password</label>
            <input id="password_confirmation" 
                   type="password" 
                   name="password_confirmation" 
                   required 
                   autocomplete="new-password" 
                   class="w-full mt-1 p-2 rounded-md border border-gray-400 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            @error('password_confirmation')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">
                Already registered?
            </a>
            <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">
                Register
            </button>
        </div>
    </form>
@endsection
