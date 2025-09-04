@extends('layouts.print')

@section('title', 'Print QR Code - ' . $room->name)

@section('content')
    <div
        class="max-w-lg mx-auto bg-white p-10 rounded-2xl shadow-xl relative print:border print-border-primary print:shadow-none">
        <!-- Print Button -->
        <button onclick="window.print()"
            class="absolute top-4 right-4 bg-primary text-white 
       px-4 py-2 rounded-lg
       hover:bg-white hover:text-primary hover:scale-110
       border-2 border-primary 
       transition-all duration-300 ease-in-out cursor-pointer print:hidden">
            Print
        </button>

        <!-- Room Name -->
        <div class="text-primary font-extrabold text-4xl sm:text-4xl text-center mb-4 truncate drop-shadow-md">
            {{ $room->name }}
        </div>

        <!-- QR Code -->
        <div
            class="w-[330px] h-[330px] mx-auto p-5 bg-white rounded-2xl border-4 border-primary shadow-[0_0_25px_4px_rgba(21,126,225,0.35)] hover:shadow-[0_0_40px_6px_rgba(21,126,225,0.55)] transition">
            <img src="{{ asset('storage/' . $room->qr_code_path) }}" alt="QR Code for {{ $room->name }}"
                class="w-full h-full object-contain rounded-xl select-none">
        </div>

        <!-- Scan Phrase -->
        <div
            class="mt-8 text-lg sm:text-xl leading-relaxed text-center max-w-md mx-auto border border-primary rounded-lg p-4 bg-gray-50 border-gray-300">
            Visit
            <a href="{{ config('app.url') }}" target="_blank" class="text-primary font-semibold hover-underline">
                {{ config('app.url') }}
            </a>
            and scan the QR Code above to know more about this office.
        </div>
    </div>
@endsection
