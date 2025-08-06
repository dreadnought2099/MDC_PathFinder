@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold text-center mb-6">Trashed <span class="text-primary">Rooms</span></h2>

        <x-floating-actions />

        {{-- <div class="mb-4">
        <a href="{{ route('room.index') }}"
           class="bg-primary text-white px-4 py-2 rounded hover:bg-white hover:text-primary border border-primary transition-all">
            ← Back to Room List
        </a>
    </div> --}}

        @if ($rooms->isEmpty())
            <div class="text-center text-gray-600">No trashed rooms found.</div>
        @else
            <div class="bg-white rounded-lg shadow-md p-6">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-100 text-sm">
                        <tr>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Deleted At</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($rooms as $room)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">{{ $room->name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $room->deleted_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-4 py-2 space-x-2">
                                    <!-- Restore -->
                                    <form action="{{ route('room.restore', $room->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="button" onclick="showModal('restoreModal-{{ $room->id }}')"
                                            class="text-primary hover-underline cursor-pointer">
                                            Restore
                                        </button>
                                    </form>

                                    {{-- Restore Modal --}}
                                    <div id="restoreModal-{{ $room->id }}"
                                        class="modal hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center"
                                        onclick="closeModal(event, this)">
                                        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full"
                                            onclick="event.stopPropagation()">
                                            <h2 class="text-lg font-semibold mb-4">Restore Room</h2>
                                            <p class="mb-4 text-gray-700">Are you sure you want to restore
                                                <strong>{{ $room->name }}</strong>?
                                            </p>
                                            <form action="{{ route('room.restore', $room->id) }}" method="POST">
                                                @csrf
                                                <div class="flex justify-end gap-2">
                                                    <button type="button"
                                                        onclick="hideModal('restoreModal-{{ $room->id }}')"
                                                        class="px-4 py-2 bg-gray-300 hover:text-white hover:bg-gray-400 rounded transition-all duration-300 cursor-pointer">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-primary text-white rounded hover:bg-white hover:text-primary border hover:border-primary transition-all duration-300 cursor-pointer">
                                                        Restore
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Permanently Delete Button -->
                                    <button type="button" onclick="showModal('deleteModal-{{ $room->id }}')"
                                        class="text-secondary hover-underline-delete cursor-pointer">
                                        Delete Permanently
                                    </button>

                                    <!-- Delete Modal -->
                                    <div id="deleteModal-{{ $room->id }}"
                                        class="modal hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center"
                                        onclick="closeModal(event, this)">
                                        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full"
                                            onclick="event.stopPropagation()">
                                            <h2 class="text-lg mb-4 text-secondary">Confirm Permanent Deletion
                                            </h2>
                                            <p class="mb-4 text-gray-700">Are you sure you want to permanently delete
                                                <span class="text-secondary">{{ $room->name }}</span>? This action cannot be undone.</p>
                                            <form action="{{ route('room.forceDelete', $room->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="flex justify-end gap-2">
                                                    <button type="button"
                                                        onclick="hideModal('deleteModal-{{ $room->id }}')"
                                                        class="px-4 py-2 bg-gray-300 hover:text-white hover:bg-gray-400 rounded transition-all duration-300 cursor-pointer">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-secondary text-white rounded hover:bg-white hover:text-secondary border-2 border-secondary transition-all duration-300 cursor-pointer">
                                                        Delete
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

<script>
    function showModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function hideModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function closeModal(event, modal) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    }
</script>
