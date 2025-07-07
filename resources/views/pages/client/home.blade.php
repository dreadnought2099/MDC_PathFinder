@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex flex-col justify-between items-center px-4 py-10 bg-gray-100">
        <!-- Centered text -->
        <div class="text-center mt-20">
            <h3 class="text-3xl font-semibold">Welcome to</h3>
            <h1 class="text-5xl font-bold mt-2">MDC PathFinder</h1>
        </div>

        <!-- Button at the bottom -->
        <a href="{{ route('ar.view') }}"
            class="bg-primary hover:bg-white hover:text-primary text-white py-2 px-6 rounded shadow-lg transition-all duration-300">
            Start AR Navigation
        </a>
    </div>
@endsection
