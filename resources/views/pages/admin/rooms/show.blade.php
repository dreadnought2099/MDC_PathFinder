@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6 max-w-5xl">
        <x-floating-actions />

        {{-- Name --}}
        <h1 class="text-4xl font-extrabold mb-6 text-gray-900 border-b-2 border-primary pb-2">
            {{ $room->name }}
        </h1>

        {{-- Description --}}
        @if ($room->description)
            <p class="mb-6 text-gray-700 leading-relaxed text-lg">{{ $room->description }}</p>
        @endif

        {{-- Office Hours --}}
        @if ($room->office_days && $room->office_hours_start && $room->office_hours_end)
            @php
                $daysFormatted = str_replace(',', ', ', $room->office_days);
                $start = \Carbon\Carbon::parse($room->office_hours_start)->format('H:i');
                $end = \Carbon\Carbon::parse($room->office_hours_end)->format('H:i');
            @endphp
            <div
                class="mb-6 p-4 bg-gradient-to-tr from-blue-400 to-white rounded-lg shadow-md max-w-sm">
                <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <img src="{{ asset('icons/calendar.png')}}" alt="Calendar" class="h-10 w-10 object-contain">
                    Office Hours
                </h4>
                <p class="text-sm text-gray-700 mb-1">
                    <span class="font-medium text-gray-900">Days:</span> {{ $daysFormatted }}
                </p>
                <p class="text-sm text-gray-700">
                    <span class="font-medium text-gray-900">Time:</span> {{ $start }} - {{ $end }}
                </p>
            </div>
        @endif

        {{-- Cover Image --}}
        @if ($room->image_path)
            <section class="mb-10">
                <h3 class="text-2xl font-semibold mb-4 text-gray-800">Cover Image</h3>
                <img src="{{ asset('storage/' . $room->image_path) }}" alt="Cover Image"
                    class="rounded-lg shadow-lg w-full max-h-[400px] object-cover border border-gray-300" />
            </section>
        @endif

        {{-- Video --}}
        @if ($room->video_path)
            <section class="mb-10">
                <h3 class="text-2xl font-semibold mb-4 text-gray-800">Video</h3>
                <video controls class="w-full rounded-lg shadow-lg border border-gray-300 max-h-[400px]">
                    <source src="{{ asset('storage/' . $room->video_path) }}" type="video/mp4" />
                    Your browser does not support the video tag.
                </video>
            </section>
        @endif

        {{-- Carousel Images --}}
        @if ($room->images && $room->images->count())
            <section class="mb-10">
                <h3 class="text-2xl font-semibold mb-6 text-gray-800">Carousel Images</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                    @foreach ($room->images as $image)
                        <div
                            class="overflow-hidden rounded-lg shadow-md hover:scale-105 transition-transform duration-300 cursor-pointer border border-gray-200">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Carousel Image"
                                class="w-full h-40 object-cover" data-image="{{ asset('storage/' . $image->image_path) }}"
                                onclick="openModal(this.dataset.image)" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
        <!-- Modal Markup -->
        <div id="imageModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-4 hidden z-50">
            <button onclick="closeModal()"
                class="absolute top-5 right-5 text-gray-300 text-6xl hover:text-red-600 cursor-pointer">&times;</button>
            <img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-full rounded shadow-lg" />
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', () => {
        function openModal(src) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = src;
            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = '';
            modal.classList.add('hidden');
        }

        // Expose functions globally for inline onclick
        window.openModal = openModal;
        window.closeModal = closeModal;

        // Close modal when clicking outside the image
        const modal = document.getElementById('imageModal');
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    });
</script>
