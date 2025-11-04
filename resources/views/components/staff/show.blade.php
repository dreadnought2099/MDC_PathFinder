@props(['staff'])

<div class="min-h-screen py-8 px-4">
    <div class="max-w-2xl mx-auto">

        <!-- Profile Card -->
        <div class="font-sofia bg-white rounded-2xl shadow-lg overflow-hidden border border-primary dark:bg-gray-800">

            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-700 px-6 py-4 md:px-8 md:py-6">
                <h3 class="text-xl md:text-2xl text-white font-semibold break-words">
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
            <div class="p-6 md:p-8">
                <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">

                    <!-- Photo -->
                    <div class="flex-shrink-0">
                        <img src="{{ $staff->photo_path ? Storage::url($staff->photo_path) : asset('images/pathfinder-bannerv2.png') }}"
                            alt="Photo of {{ $staff->name }}" title="Click image to expand"
                            class="w-full lg:w-48 h-40 object-cover rounded-lg hover:scale-105 transition-transform duration-300 cursor-pointer"
                            onclick="openModal('{{ $staff->photo_path ? Storage::url($staff->photo_path) : asset('images/pathfinder-bannerv2.png') }}')">
                    </div>

                    <!-- Info Section -->
                    <div class="flex-1 space-y-6">

                        <!-- Bio -->
                        <div class="border border-primary rounded-xl p-4 transition-all hover:scale-105 duration-300">
                            <div class="flex items-start space-x-3">
                                <div class="bg-blue-100 p-2 rounded-lg flex-shrink-0">
                                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/user.png"
                                        alt="User" class="h-6 w-6">
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 mb-1 dark:text-gray-300">Bio</p>
                                    <p id="bioText"
                                        class="text-gray-600 leading-relaxed dark:text-gray-300 break-words break-all whitespace-pre-line max-w-full overflow-hidden">
                                        {{ $staff->bio ?? 'N/A' }}
                                    </p>
                                    <button id="toggleBio"
                                        class="hidden text-primary text-sm mt-2 hover-underline cursor-pointer">
                                        See more
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div
                            class="border border-primary rounded-xl p-4 hover:scale-105 transition-all duration-250 ease-in-out">
                            <div class="flex items-center space-x-3">
                                <div class="bg-orange-100 p-2 rounded-lg">
                                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/email.png"
                                        alt="User" class="h-6 w-6">
                                </div>
                                <div class="min-w-0"> <!-- ensure flex item shrinks -->
                                    <p class="font-semibold text-gray-800 dark:text-gray-300">Email</p>
                                    <p
                                        class="text-gray-600 dark:text-gray-300 break-words whitespace-normal">
                                        {{ $staff->email ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Position -->
                        <div class="border border-primary rounded-xl p-4 transition-all hover:scale-105 duration-300">
                            <div class="flex items-center space-x-3">
                                <div class="bg-purple-200 p-2 rounded-lg flex-shrink-0">
                                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/hierarchy.png"
                                        alt="Position" class="h-6 w-6">
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-gray-300">Position</p>
                                    <p class="text-gray-600 dark:text-gray-300">
                                        {{ $staff->position ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="border border-primary rounded-xl p-4 transition-all hover:scale-105 duration-300">
                            <div class="flex items-center space-x-3">
                                <div class="bg-green-100 p-2 rounded-lg flex-shrink-0">
                                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/phone.png"
                                        alt="Phone" class="h-6 w-6">
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-gray-300">Phone</p>
                                    <p class="text-gray-600 dark:text-gray-300">
                                        {{ $staff->phone_num ?? 'N/A' }}</p>
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
                <!-- Download -->
                <a id="downloadBtn" href="#" title="Download Image"
                    class="p-2 rounded-xl hover:scale-110 transition-all duration-300 cursor-pointer">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/download-button.png"
                        alt="Download Image" class="w-10 h-10">
                </a>
                <!-- Close -->
                <button onclick="closeModal()" class="p-2 rounded-xl hover:scale-110 transition-all duration-300"
                    title="Close Modal">
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/exit.png"
                        alt="Close Modal" class="w-10 h-10">
                </button>
            </div>
            <img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-full rounded shadow-lg" />
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const downloadBtn = document.getElementById('downloadBtn');
            const bioText = document.getElementById('bioText');
            const toggleBio = document.getElementById('toggleBio');

            // Open modal
            window.openModal = function(src) {
                modalImage.src = src;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                // Handle download (convert to PNG)
                downloadBtn.onclick = async (e) => {
                    e.preventDefault();
                    try {
                        const img = new Image();
                        img.crossOrigin = 'anonymous';
                        img.src = src;
                        await img.decode();

                        const canvas = document.createElement('canvas');
                        canvas.width = img.width;
                        canvas.height = img.height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0);

                        const pngUrl = canvas.toDataURL('image/png');
                        const a = document.createElement('a');
                        a.href = pngUrl;
                        a.download = src.split('/').pop().replace(/\.\w+$/, '.png');
                        a.click();
                    } catch (err) {
                        alert('Failed to download image. It may be blocked by CORS.');
                    }
                };
            };

            // Close modal
            window.closeModal = function() {
                modal.classList.add('hidden');
                modalImage.src = '';
                document.body.style.overflow = 'auto';
            };

            // Click outside or Escape closes modal
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
            });

            // Bio See More/Less
            if (bioText && toggleBio) {
                const maxLength = 150;
                const fullText = bioText.textContent.trim();
                if (fullText.length > maxLength) {
                    const shortText = fullText.slice(0, maxLength) + '...';
                    bioText.textContent = shortText;
                    toggleBio.classList.remove('hidden');

                    toggleBio.addEventListener('click', () => {
                        if (bioText.textContent === shortText) {
                            bioText.textContent = fullText;
                            toggleBio.textContent = 'See less';
                        } else {
                            bioText.textContent = shortText;
                            toggleBio.textContent = 'See more';
                        }
                    });
                }
            }
        });
    </script>
@endpush
