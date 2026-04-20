@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-50">
    <div class="text-center">
        <div class="flex justify-center">
            <h1 class="text-4xl font-bold text-indigo-600">Axia Orto</h1>
        </div>
        <p class="mt-4 text-xl text-gray-600">Beyond Limitations</p>
        
        {{-- <div class="mt-8 flex space-x-4">
            <a href="{{ route('login') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                Login
            </a>
            <a href="{{ route('register') }}" class="px-6 py-3 bg-white text-indigo-600 border border-indigo-600 rounded-lg hover:bg-gray-50 transition-colors">
                Register
            </a>
        </div> --}}
    </div>

    <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl">
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <div class="text-4xl font-bold text-indigo-600 mb-2">🚀</div>
            <h3 class="text-xl font-semibold mb-2">Modern Framework</h3>
            <p class="text-gray-600">Built with Laravel 10 and Livewire</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <div class="text-4xl font-bold text-indigo-600 mb-2">🎨</div>
            <h3 class="text-xl font-semibold mb-2">Beautiful UI</h3>
            <p class="text-gray-600">Powered by Tailwind CSS</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <div class="text-4xl font-bold text-indigo-600 mb-2">⚡</div>
            <h3 class="text-xl font-semibold mb-2">Fast Performance</h3>
            <p class="text-gray-600">Optimized for speed and user experience</p>
        </div>
    </div>
</div>
@endsection