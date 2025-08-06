@extends('layouts.app')

@section('content')
    <div class="p-6">
        <h1 class="text-2xl text-center mb-4"><span class="text-primary">Staff</span> List</h1>

        <x-floating-actions />

        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffs as $staff)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $staff->name }}</td>
                            <td class="px-4 py-2 flex space-x-4 items-center">
                                <a href="{{ route('staff.show', $staff->id) }}"
                                    class="text-primary hover-underline hover:scale-105 transform transition duration-200">
                                    View
                                </a>
                                <a href="{{ route('staff.edit', $staff->id) }}"
                                    class="text-edit hover-underline-edit hover:scale-105 transform transition duration-200">
                                    Edit
                                </a>
                                <button class="text-secondary hover-underline-delete hover:scale-105 transition duration-200 cursor-pointer"
                                        onclick="openModal('{{ $staff->id }}', '{{ $staff->name }}')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-4 text-center text-gray-500">No staff found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 bg-black/50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-sm shadow-lg">
            <h2 class="text-xl mb-4">Confirm Deletion</h2>
            <p class="mb-4">Are you sure you want to delete <span id="modalStaffName" class="text-primary   "></span>?</p>

            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2 bg-gray-300 hover:text-white hover:bg-gray-400 rounded transition-all duration-300 cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-secondary text-white rounded hover:bg-white hover:text-secondary border border-secondary cursor-pointer">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id, name) {
            const modal = document.getElementById('deleteModal');
            const nameSpan = document.getElementById('modalStaffName');
            const form = document.getElementById('deleteForm');

            nameSpan.textContent = name;
            form.action = '{{ route('staff.destroy', '__ID__') }}'.replace('__ID__', id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        // Optional: Close modal on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });
    </script>
@endsection