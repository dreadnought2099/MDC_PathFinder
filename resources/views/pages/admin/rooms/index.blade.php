@extends('layouts.app')

@section('content')
    <div class="p-4">

        <x-floating-actions />

        <h2 class="text-xl text-center font-bold mb-4">Room List</h2>

        <table class="w-full border-collapse border">
            <thead>
                <tr>
                    <th class="border p-2">Name</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rooms as $room)
                    <tr>
                        <td class="border p-2">{{ $room->name }}</td>
                        <td class="border p-2">
                            <a href="{{ route('room.edit', $room->id) }}"
                                class="bg-blue-500 text-white px-3 py-1 rounded">Edit</a>

                            <div x-data="() => ({ showModal: false })" class="inline">
                                <button @click="showModal = true"
                                    class="text-secondary hover-underline-delete hover:scale-105 transform transition duration-200 cursor-pointer">
                                    Delete
                                </button>

                                {{-- Modal --}}
                                <div x-show="showModal"
                                    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                    <div @click.away="showModal = false"
                                        class="bg-white rounded-lg p-6 w-full max-w-sm shadow-lg">
                                        <h2 class="text-xl mb-4">Confirm Deletion</h2>
                                        <p class="mb-4">Are you sure you want to delete
                                            <span class="text-primary">{{ $room->name }}</span>?
                                        </p>

                                        <form method="POST" action="{{ route('room.destroy', $room->id) }}">
                                            @csrf
                                            @method('DELETE')

                                            <div class="flex justify-end space-x-2">
                                                <button type="button" @click="showModal = false"
                                                    class="px-4 py-2 bg-gray-300 hover:text-white hover:bg-gray-400 rounded transition-all duration-300 cursor-pointer">
                                                    Cancel
                                                </button>
                                                <button type="submit"
                                                    class="px-4 py-2 bg-secondary text-white rounded hover:bg-white hover:text-secondary border-2 border-secondary transition-all duration-300 cursor-pointer">
                                                    Confirm
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
