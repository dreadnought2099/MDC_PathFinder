@props(['staff'])

<div class="min-h-screen bg-white py-8 px-4 dark:bg-gray-900">
    <div class="max-w-2xl mx-auto">

        <!-- Profile Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border-b-2 border-l-2 border-r-2 border-primary">

            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                <h3 class="text-2xl text-white">
                    {{ $staff->first_name }}
                    @if ($staff->middle_name)
                        {{ $staff->middle_name }}
                    @endif
                    {{ $staff->last_name }}
                    @if ($staff->suffix)
                        {{ $staff->suffix }}
                    @endif
                    @if ($staff->credentials)
                        , {{ $staff->credentials }}
                    @endif
                </h3>
            </div>

            <!-- Content -->
            <div class="p-8 dark:bg-gray-800">
                <div class="flex flex-col lg:flex-row gap-8">

                    {{-- @if ($staff->photo_path) --}}
                    <div class="mt-4">
                        <img src="{{ $staff->photo_path ? Storage::url($staff->photo_path) : asset('images/mdc-logo.png') }}"
                            alt="Photo of {{ $staff->name }}" title="Click image to expand"
                            class="w-full h-40 object-cover rounded hover:scale-110 transition-transform duration-300 cursor-pointer"
                            onclick="openModal('{{ $staff->photo_path ? Storage::url($staff->photo_path) : asset('images/mdc-logo.png') }}')">
                    </div>
                    {{-- @endif --}}

                    <!-- Information Section -->
                    <div class="flex-1 space-y-6">

                        <!-- Bio -->
                        <div
                            class="border border-primary rounded-xl p-4 hover:scale-110 transition-all duration-250 ease-in-out">
                            <div class="flex items-start space-x-3">
                                <div class="bg-blue-100 p-2 rounded-lg">
                                    <img src="{{ asset('icons/user.png') }}" alt="User" class="h-6 w-6">
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 mb-1 dark:text-gray-300">Bio</p>
                                    <p class="font-sofia text-gray-600 leading-relaxed dark:text-gray-300">{{ $staff->bio ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div
                            class="border border-primary rounded-xl p-4 hover:scale-110 transition-all duration-250 ease-in-out">
                            <div class="flex items-center space-x-3">
                                <div class="bg-orange-100 p-2 rounded-lg">
                                    <img src="{{ asset('icons/email.png') }}" alt="User" class="h-6 w-6">
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-gray-300">Email</p>
                                    <p class="font-sofia text-gray-600 dark:text-gray-300">{{ $staff->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Position -->
                        <div
                            class="border border-primary rounded-xl p-4 hover:scale-110 transition-all duration-250 ease-in-out">
                            <div class="flex items-center space-x-3">
                                <div class="bg-purple-200 p-2 rounded-lg">
                                    <img src="{{ asset('icons/hierarchy.png') }}" alt="User" class="h-6 w-6">
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-gray-300">Position</p>
                                    <p class="font-sofia text-gray-600 dark:text-gray-300">{{ $staff->position ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div
                            class="border border-primary rounded-xl p-4 hover:scale-110 transition-all duration-250 ease-in-out">
                            <div class="flex items-center space-x-3">
                                <div class="bg-green-100 p-2 rounded-lg">
                                    <img src="{{ asset('icons/phone.png') }}" alt="User" class="h-6 w-6">
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-gray-300">Phone</p>
                                    <p class="font-sofia text-gray-600 dark:text-gray-300">{{ $staff->phone_num ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
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
                    class="p-2 rounded-xl transition-all hover:scale-120 ease-in-out duration-300 mt-6"
                    title="Close Modal">
                    <img src="{{ asset('icons/exit.png') }}" alt="Close Modal" class="w-10 h-10">
                </button>
            </div>

            <!-- Image -->
            <img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-full rounded shadow-lg" />
        </div>
    </div>
</div>

@push('scripts')
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
@endpush
