@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow border-2 border-primary">

        <x-floating-actions />

        <h2 class="text-2xl font-bold mb-4">{{ $staff->name }}</h2>
        <p><strong>Position:</strong> {{ $staff->position ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $staff->email ?? 'N/A' }}</p>
        <p><strong>Phone:</strong> {{ $staff->phone_num ?? 'N/A' }}</p>
        <p><strong>Bio:</strong> {{ $staff->bio ?? 'N/A' }}</p>

        @if ($staff->photo_path)
            <div class="mt-4">
                <img src="{{ Storage::url($staff->photo_path) }}" alt="Photo of {{ $staff->name }}"
                    class="w-40 h-40 object-cover rounded cursor-pointer"
                    onclick="openModal('{{ Storage::url($staff->photo_path) }}')">
            </div>
        @endif

        <!-- Modal -->
        <div id="imageModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-4 z-50">
            <!-- Close button -->
            <button onclick="closeModal()"
                class="absolute top-5 right-5 text-gray-300 text-6xl hover:text-red-600 cursor-pointer">
                &times;
            </button>
            <!-- Full image -->
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

        // Expose functions for inline onclick
        window.openModal = openModal;
        window.closeModal = closeModal;

        // Close when clicking outside image
        const modal = document.getElementById('imageModal');
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    });
</script>