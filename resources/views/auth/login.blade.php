@extends('layouts.new')

@section('form')
    <h2 class="text-2xl font-semibold mb-6">Login to Your Account</h2>
    @if(session('status'))
        <div class="mb-4 text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium">Email</label>
            <input id="email" type="email" name="email" required class="w-full mt-1 p-2 rounded-md border border-gray-400 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium">Password</label>
            <input id="password" type="password" name="password" required class="w-full mt-1 p-2 rounded-md border border-gray-400 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>

        <div class="flex items-center justify-between">
            <label class="inline-flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300">
                <span class="ml-2 text-sm">Remember me</span>
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">Forgot?</a>
        </div>

        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Login</button>

      
    </form>
@endsection