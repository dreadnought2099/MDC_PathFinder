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

    @if($rooms->isEmpty())
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
                    @foreach($rooms as $room)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $room->name }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $room->deleted_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-2 space-x-2">
                                <!-- Restore -->
                                <form action="{{ route('room.restore', $room->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-primary hover-underline cursor-pointer">
                                        Restore
                                    </button>
                                </form>

                                <!-- Permanently Delete -->
                                <form action="{{ route('room.forceDelete', $room->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Permanently delete this room? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-secondary hover-underline-delete cursor-pointer">
                                        Delete Permanently
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
