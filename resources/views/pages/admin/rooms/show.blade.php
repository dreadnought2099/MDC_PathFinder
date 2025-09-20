@extends('layouts.app')

@section('content')
    <div class="min-h-screen">
        <div class="container mx-auto p-8 max-w-6xl">
            <x-floating-actions />

            {{-- Header Section --}}
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border-2 border-primary dark:bg-gray-800">
                {{-- Name --}}
                <div class="text-center mb-6">
                    <h1 class="text-5xl font-bold mb-4 text-primary">
                        {{ $room->name }}
                    </h1>
                </div>

                {{-- Description --}}
                @if ($room->description)
                    <div class="max-w-4xl mx-auto text-center">
                        <p class="text-xl text-slate-700 leading-relaxed font-light dark:text-gray-300">
                            {{ $room->description }}</p>
                    </div>
                @endif
            </div>

            {{-- Cover Image Section --}}
            @if ($room->image_path && Storage::disk('public')->exists($room->image_path))
                <section class="mb-12">
                    <div class="text-center mb-8">
                        <h3 class="text-3xl font-bold text-slate-800 mb-2 dark:text-gray-300">Cover Image</h3>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-primary dark:bg-gray-800">
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
                        <h3 class="text-3xl font-bold text-slate-800 mb-2 dark:text-gray-300">Video</h3>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-primary dark:bg-gray-800">
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
                        <h3 class="text-3xl font-bold text-slate-800 mb-2 dark:text-gray-300">Image Gallery</h3>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-primary dark:bg-gray-800">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach ($room->images as $image)
                                @if (Storage::disk('public')->exists($image->image_path))
                                    <div class="relative group">
                                        <div
                                            class="overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer 
                                            {{ $image->trashed() ? 'border-2 border-red-300 opacity-60' : ' border-2 border-primary' }}
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
                        <h3 class="text-3xl font-bold text-slate-800 mb-2 dark:text-gray-300">Assigned Staff</h3>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-primary dark:bg-gray-800">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach ($room->staff as $member)
                                <div
                                    class="bg-gradient-to-br from-slate-50 to-white rounded-xl shadow-md hover:shadow-xl border-2 border-primary overflow-hidden group transform hover:scale-105 transition-all duration-300">
                                    <div class="cursor-pointer overflow-hidden"
                                        onclick="openModal(' {{ $member->photo_path ? Storage::url($member->photo_path) : asset('images/mdc-logo.png') }} ')">
                                        <img src="{{ $member->photo_path ? Storage::url($member->photo_path) : asset('images/mdc-logo.png') }}"
                                            alt="{{ $member->name }}"
                                            class="w-full h-56 object-cover group-hover:scale-110 transition-transform duration-500">
                                    </div>
                                    <div class="p-6 text-center dark:bg-gray-700">
                                        <a href="{{ route('staff.show', $member->id) }}" target="_blank" rel="noopener noreferrer"
                                            class="block text-xl font-bold text-slate-800 hover:text-primary transition-colors duration-300 mb-2 dark:text-gray-300">
                                            {{ $member->full_name }}
                                        </a>
                                        <p class="text-slate-600 font-medium dark:text-gray-300">
                                            {{ $member->position ?? 'No position assigned' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @else
                <div class="text-center py-8">
                    <div class="rounded-2xl p-8 border-2 border-primary flex flex-col items-center dark:bg-gray-800">
                        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/group.png" alt="Assigned Staff Icon" class="w-10 h-8">
                        <p class="text-slate-500 text-lg font-medium">No staff assigned to this room yet.</p>
                    </div>
                </div>
            @endif

            {{-- For Debugging purposes --}}
            {{-- <pre>{{ $room->formatted_office_hours }}</pre>  --}}

            {{-- Office Hours --}}
            <div class="mt-6">
                <h3 class="text-3xl font-bold text-slate-800 mb-2 text-center dark:text-gray-300">Office Hours</h3>
                <div class="bg-gray-50 p-4 rounded-2xl dark:bg-gray-800 border-2 border-primary">

                    @if ($room->grouped_office_hours && count($room->grouped_office_hours) > 0)
                        <div class="space-y-3">
                            @foreach ($room->grouped_office_hours as $timeRange => $days)
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                    <div class="font-medium text-gray-800 min-w-0 flex-1 dark:text-gray-300">
                                        {{ $room->formatDaysGroup($days) }}
                                    </div>
                                    <div class="text-sm sm:text-right">
                                        @if (strtolower($timeRange) === 'closed')
                                            <span class="text-red-600 font-medium">Closed</span>
                                        @else
                                            <span class="text-gray-600 dark:text-gray-300">{{ $timeRange }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-gray-500 italic">No office hours specified</div>
                    @endif

                </div>
            </div>

            {{-- QR Code Section --}}
            @if ($room->qr_code_path && Storage::disk('public')->exists($room->qr_code_path))
                <div class="mt-12">
                    <div class="bg-white rounded-2xl shadow-lg p-8 text-center border-2 border-primary dark:bg-gray-800">
                        <h3 class="text-2xl font-bold text-slate-800 mb-6 dark:text-gray-300">{{ $room->name }} QR Code
                        </h3>
                        <div class="inline-block bg-slate-50 p-6 rounded-xl border-2 border-primary mb-6">
                            <img src="{{ Storage::url($room->qr_code_path) }}" alt="QR Code for {{ $room->name }}"
                                class="max-w-[200px] mx-auto" />
                        </div>
                        <div>
                            <a href="{{ route('room.print-qrcode', $room->id) }}" target="_blank"
                                class="inline-flex items-center gap-2 text-white bg-primary border border-primary hover:text-primary hover:bg-white px-4 py-4 text-sm rounded-md transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 dark:hover:bg-gray-800">
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
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/download-button.png" alt="Download Image" class="w-10 h-10">
                </a>

                <!-- Close button -->
                <button onclick="closeModal()"
                    class="p-2 rounded-xl transition-all hover:scale-120 ease-in-out duration-300 mt-6 cursor-pointer"
                    title="Close Modal">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/exit.png" alt="Close Modal" class="w-10 h-10">
                </button>
            </div>

            <!-- Image -->
            <img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-full rounded shadow-lg" />
        </div>
    </div>
@endsection

@push('scripts')
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
@endpush
