@extends('layouts.print')

@section('title', 'Print QR Code - ' . $room->name)

@section('content')
    <div
        class="w-full max-w-2xl mx-auto bg-white p-4 sm:p-6 md:p-8 lg:p-10 rounded-2xl shadow-xl relative border-2 border-primary print:border print-border-primary print:shadow-none dark:bg-gray-800">
        <!-- Print Button -->
        <button onclick="window.print()"
            class="absolute top-2 right-2 sm:top-4 sm:right-4 bg-primary text-white 
            px-3 py-1 sm:px-4 sm:py-2 rounded-md
            hover:bg-white hover:text-primary hover:scale-105
            border-2 border-primary 
            transition-all duration-300 ease-in-out cursor-pointer print:hidden dark:hover:bg-gray-800 dark:hover:text-gray-300 focus:outline-none">
            Print
        </button>

        <!-- Room Name -->
        <div
            class="text-primary font-extrabold text-2xl sm:text-3xl md:text-4xl text-center mb-2 sm:mb-4 truncate drop-shadow-md">
            {{ $room->name }}
        </div>

        <!-- QR Code -->
        <div
            class="w-full max-w-[250px] sm:max-w-[300px] md:max-w-[330px] h-auto mx-auto p-3 sm:p-4 md:p-5 bg-white rounded-2xl border-4 border-primary shadow-[0_0_15px_2px_rgba(21,126,225,0.35)] hover:shadow-[0_0_25px_4px_rgba(21,126,225,0.55)] transition">
            <img src="{{ asset('storage/' . $room->qr_code_path) }}" alt="QR Code for {{ $room->name }}"
                class="w-full h-auto object-contain select-none">
        </div>

        <!-- Scan Phrase -->
        <div
            class="mt-4 sm:mt-6 md:mt-8 text-base sm:text-lg md:text-xl leading-relaxed text-center max-w-full sm:max-w-md mx-auto border border-primary rounded-lg p-3 sm:p-4 bg-gray-50 text-gray-900 dark:bg-gray-800 dark:text-gray-300 print:bg-white print:text-black print:border-black">
            Visit
            <a href="{{ config('app.url') }}" target="_blank"
                class="text-primary font-semibold hover:underline dark:text-gray-300">
                {{ config('app.url') }}
            </a>
            and scan the QR Code above to know more about this office.
        </div>
    </div>
@endsection