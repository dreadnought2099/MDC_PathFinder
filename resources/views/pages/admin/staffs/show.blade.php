@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8 px-4">
        <div class="max-w-2xl mx-auto">

            <!-- Floating Actions -->
            <div class="mb-8">
                <x-floating-actions />
            </div>

            <!-- Profile Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">

                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                    <h1 class="text-3xl font-bold text-white">{{ $staff->name }}</h1>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <div class="flex flex-col lg:flex-row gap-8">

                        @if ($staff->photo_path)
                            <div class="mt-4">
                                <img src="{{ Storage::url($staff->photo_path) }}" alt="Photo of {{ $staff->name }}"
                                    title="Click image to expand"
                                    class="w-full h-40 object-cover rounded hover:scale-110 transition-transform duration-300 cursor-pointer"
                                    onclick="openModal('{{ Storage::url($staff->photo_path) }}')">
                            </div>
                        @endif

                        <!-- Information Section -->
                        <div class="flex-1 space-y-6">

                            <!-- Email -->
                            <div
                                class="border border-gray-200 rounded-xl p-4 hover:border-primary hover:bg-primary-10 transition-all duration-300">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-blue-100 p-2 rounded-lg">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 7.89a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">Email</p>
                                        <p class="text-gray-600">{{ $staff->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Position -->
                            <div
                                class="border border-gray-200 rounded-xl p-4 hover:border-primary hover:bg-primary-10 transition-all duration-300">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-purple-100 p-2 rounded-lg">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 00-2 2H10a2 2 0 00-2-2V6m8 0h2a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">Position</p>
                                        <p class="text-gray-600">{{ $staff->position ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div
                                class="border border-gray-200 rounded-xl p-4 hover:border-primary hover:bg-primary-10 transition-all duration-300">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-green-100 p-2 rounded-lg">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">Phone</p>
                                        <p class="text-gray-600">{{ $staff->phone_num ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Bio -->
                            @if ($staff->bio && $staff->bio !== 'N/A')
                                <div
                                    class="border border-gray-200 rounded-xl p-4 hover:border-primary hover:bg-primary-10 transition-all duration-300">
                                    <div class="flex items-start space-x-3">
                                        <div class="bg-orange-100 p-2 rounded-lg">
                                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 mb-1">Bio</p>
                                            <p class="text-gray-600 leading-relaxed">{{ $staff->bio }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div id="imageModal"
                class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-4 z-50 backdrop-blur-sm">
                <button onclick="closeModal()"
                    class="absolute top-5 right-5 text-white/80 text-6xl hover:text-red-400 cursor-pointer transition-colors duration-300 z-10">&times;</button>
                <img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-full rounded-xl shadow-2xl" />
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');

            if (!modal || !modalImage) return;

            window.openModal = function(src) {
                modalImage.src = src;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            };

            window.closeModal = function() {
                modal.classList.add('hidden');
                modalImage.src = '';
                document.body.style.overflow = 'auto';
            };


            // Close by clicking outside the image
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            // Close with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        });
    </script>
@endsection
