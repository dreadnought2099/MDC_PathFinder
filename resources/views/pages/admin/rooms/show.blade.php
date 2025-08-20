@extends('layouts.app')

@section('content')
    <div class="min-h-screen">
        <div class="container mx-auto p-8 max-w-6xl">
            <x-floating-actions />

            {{-- Header Section --}}
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-slate-200">
                {{-- Name --}}
                <div class="text-center mb-6">
                    <h1 class="text-5xl font-bold mb-4 text-primary">
                        {{ $room->name }}
                    </h1>
                </div>

                {{-- Description --}}
                @if ($room->description)
                    <div class="max-w-4xl mx-auto text-center">
                        <p class="text-xl text-slate-700 leading-relaxed font-light">{{ $room->description }}</p>
                    </div>
                @endif
            </div>

            {{-- Cover Image Section --}}
            @if ($room->image_path && Storage::disk('public')->exists($room->image_path))
                <section class="mb-12">
                    <div class="text-center mb-8">
                        <h3 class="text-3xl font-bold text-slate-800 mb-2">Cover Image</h3>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-200">
                        <div class="overflow-hidden rounded-xl">
                            <img src="{{ Storage::url($room->image_path) }}" alt="Cover Image"
                                class="w-full max-h-[500px] object-cover cursor-pointer hover:scale-105 transition-all duration-500 ease-out"
                                onclick="openModal(this.src)" />
                        </div>
                    </div>
                </section>
            @endif

            {{-- Video Section --}}
            @if ($room->video_path && Storage::disk('public')->exists($room->video_path))
                <section class="mb-12">
                    <div class="text-center mb-8">
                        <h3 class="text-3xl font-bold text-slate-800 mb-2">Video</h3>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-200">
                        <div class="overflow-hidden rounded-xl">
                            <video controls class="w-full rounded-xl shadow-md max-h-[500px]">
                                <source src="{{ Storage::url($room->video_path) }}" type="video/mp4" />
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                </section>
            @endif

            {{-- Carousel Images Section --}}
            @if ($room->images && $room->images->count())
                <section class="mb-12">
                    <div class="text-center mb-8">
                        <h3 class="text-3xl font-bold text-slate-800 mb-2">Image Gallery</h3>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach ($room->images as $image)
                                @if (Storage::disk('public')->exists($image->image_path))
                                    <div class="relative group">
                                        <div
                                            class="overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer 
                                            {{ $image->trashed() ? 'border-2 border-red-300 opacity-60' : 'border border-slate-200' }}
                                            transform hover:scale-105">
                                            <img src="{{ Storage::url($image->image_path) }}" alt="Gallery Image"
                                                class="w-full h-48 object-cover" onclick="openModal(this.src)" />
                                            @if ($image->trashed())
                                                <div
                                                    class="absolute top-2 left-2 bg-red-500 text-white rounded-lg px-3 py-1 text-xs font-medium shadow-lg">
                                                    Deleted
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            {{-- Staff Section --}}
            @if ($room->staff->isNotEmpty())
                <section class="mb-12">
                    <div class="text-center mb-8">
                        <h3 class="text-3xl font-bold text-slate-800 mb-2">Assigned Staff</h3>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach ($room->staff as $member)
                                <div
                                    class="bg-gradient-to-br from-slate-50 to-white rounded-xl shadow-md hover:shadow-xl border border-slate-200 overflow-hidden group transform hover:scale-105 transition-all duration-300">
                                    <div class="cursor-pointer overflow-hidden"
                                        onclick="openModal('{{ Storage::url($member->photo_path ?? 'images/profile.jpeg') }}')">
                                        <img src="{{ Storage::url($member->photo_path ?? 'images/default.jpg') }}"
                                            alt="{{ $member->name }}"
                                            class="w-full h-56 object-cover group-hover:scale-110 transition-transform duration-500">
                                    </div>
                                    <div class="p-6 text-center">
                                        <a href="{{ route('staff.show', $member->id) }}"
                                            class="block text-xl font-bold text-slate-800 hover:text-primary transition-colors duration-300 mb-2">
                                            {{ $member->full_name }}
                                        </a>
                                        <p class="text-slate-600 font-medium">
                                            {{ $member->position ?? 'No position assigned' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @else
                <div class="text-center py-8">
                    <div class="bg-slate-200 rounded-2xl p-8 border border-slate-200 flex flex-col items-center">
                        <!-- SVG Icon: Centered using flexbox (items-center) on parent -->
                        <svg class="w-8 h-8 text-primary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <!-- Text: Follows SVG, centered by parent flexbox -->
                        <p class="text-slate-600 text-lg font-medium">No staff assigned to this room yet.</p>
                    </div>
                </div>
            @endif

            {{-- For Debugging purposes --}}
            {{-- <pre>{{ $room->formatted_office_hours }}</pre>  --}}

            {{-- Office Hours --}}
            <div class="mt-6">
                <h3 class="text-lg font-semibold mb-2">Office Hours</h3>
                <div class="whitespace-pre-line bg-gray-50 p-4 rounded border">
                    {!! nl2br(e($room->formatted_office_hours)) !!}
                </div>
            </div>

            {{-- QR Code Section --}}
            @if ($room->qr_code_path && Storage::disk('public')->exists($room->qr_code_path))
                <div class="mt-12">
                    <div class="bg-white rounded-2xl shadow-lg p-8 text-center border border-slate-200">
                        <h3 class="text-2xl font-bold text-slate-800 mb-6">Room QR Code</h3>
                        <div class="inline-block bg-slate-50 p-6 rounded-xl border-2 border-slate-200 mb-6">
                            <img src="{{ Storage::url($room->qr_code_path) }}" alt="QR Code for {{ $room->name }}"
                                class="max-w-[200px] mx-auto" />
                        </div>
                        <div>
                            <a href="{{ route('room.print-qrcode', $room->id) }}" target="_blank"
                                class="inline-flex items-center gap-2 text-white bg-primary border border-primary hover:text-primary hover:bg-white px-4 py-4 text-sm rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                    </path>
                                </svg>
                                Print QR Code
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Modal Markup (unchanged functionality) --}}
        <div id="imageModal"
            class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-4 z-50 backdrop-blur-sm">
            <div class="absolute top-5 right-5 flex items-center space-x-8">
                <!-- Download button -->
                <a id="downloadBtn" href="#" download title="Download Image"
                    class="p-2 rounded-xl transition-all hover:scale-120 ease-in-out duration-300 mt-6">
                    <img src="{{ asset('icons/download-button.png') }}" alt="Download Image" class="w-10 h-10">
                </a>

                <!-- Close button -->
                <button onclick="closeModal()"
                    class="p-2 rounded-xl transition-all hover:scale-120 ease-in-out duration-300 mt-6 cursor-pointer"
                    title="Close Modal">
                    <img src="{{ asset('icons/exit.png') }}" alt="Close Modal" class="w-10 h-10">
                </button>
            </div>

            <!-- Image -->
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

        window.openModal = openModal;
        window.closeModal = closeModal;

        const modal = document.getElementById('imageModal');
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    });
</script>
