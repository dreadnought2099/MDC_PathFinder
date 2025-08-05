@extends('layouts.app')

@section('content')
    <x-floating-actions />
    
    <div class="max-w-4xl mx-auto p-4">
        <h1 class="text-2xl font-bold">{{ $room->name }} QR Code</h1>
        <p class="mt-2 text-gray-700">{{ $room->description }}</p>
        @if ($room->qr_code_path)
            <div class="mt-6">
                
                <div class="flex flex-col items-center w-full space-y-4">
                    <div id="qr-code-container">
                        @if (Str::endsWith($room->qr_code_path, '.svg'))
                            <div class="w-60 h-60 flex items-center justify-center">
                                {!! file_get_contents(public_path($room->qr_code_path)) !!}
                            </div>
                        @else
                            <img src="{{ asset('storage/' . $room->qr_code_path) }}" alt="QR Code for {{ $room->name }}"
                                class="w-48 h-48 mx-auto">
                        @endif
                    </div>
                    
                    <button onclick="printQRCode()" 
                            class="bg-primary text-white hover:bg-white hover:text-primary px-4 py-2 rounded-lg transition-all duration-300 flex items-center space-x-2 cursor-pointer border-1 border-primary mt-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>Print QR Code</span>
                    </button>
                </div>
            </div>
        @endif

        <div class="space-y-12">
            {{-- <h2 class="text-xl font-semibold">Staff in this Room:</h2> --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-2">
                @foreach ($room->staff as $member)
                    <div class="bg-white p-4 rounded shadow">
                        <img src="{{ asset('storage/' . $member->photo_path) }}" alt="{{ $member->name }}"
                            class="w-full h-40 object-cover mb-2 rounded">
                        <h3 class="text-lg font-bold">{{ $member->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $member->position }}</p>
                        @if ($member->email)
                            <p class="text-sm text-blue-600">{{ $member->email }}</p>
                        @endif
                        @if ($member->bio)
                            <p class="mt-1 text-sm">{{ $member->bio }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #print-section, #print-section * {
                visibility: visible;
            }
            #print-section {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                text-align: center;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <!-- Print Section (Hidden by default) -->
    <div id="print-section" style="position: absolute; left: -9999px; top: -9999px; visibility: hidden; display: none;">
        <div class="p-8 text-center">
            <h1 class="text-3xl font-bold mb-4">{{ $room->name }}</h1>
            <p class="text-lg mb-6">{{ $room->description }}</p>
            
            <div class="flex justify-center mb-6">
                @if (Str::endsWith($room->qr_code_path, '.svg'))
                    <div class="w-64 h-64">
                        {!! file_get_contents(public_path($room->qr_code_path)) !!}
                    </div>
                @else
                    <img src="{{ asset('storage/' . $room->qr_code_path) }}" alt="QR Code for {{ $room->name }}"
                        class="w-64 h-64">
                @endif
            </div>
            
            <div class="text-sm text-gray-600">
                <p>Scan this QR code to view room information</p>
                <p class="mt-2">Room ID: {{ $room->id }}</p>
                <p class="mt-4">Generated on: {{ now()->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>

    <script>
        function printQRCode() {
            const printSection = document.getElementById('print-section');
            
            // Keep the print section hidden but make it available for print media query
            printSection.style.position = 'absolute';
            printSection.style.left = '-9999px';
            printSection.style.top = '-9999px';
            printSection.style.visibility = 'hidden';
            printSection.style.display = 'block';
            
            // Trigger print - the CSS @media print will handle showing the content
            window.print();
            
            // Hide the print section after printing
            setTimeout(() => {
                printSection.style.display = 'none';
            }, 100);
        }
    </script>
@endsection
