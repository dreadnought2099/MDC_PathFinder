@extends('layouts.app')

@section('content')
    <div class="p-6">
        <h1 class="text-2xl text-center mb-4">Staff List</h1>

        <x-floating-actions />

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Staff Table --}}
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
                            <td class="px-4 py-2 flex space-x-4">
                                <a href="{{ route('staff.show', $staff->id) }}"
                                    class="text-primary hover-underline hover:scale-105 transform transition duration-200">
                                    View
                                </a>
                                <a href="{{ route('staff.edit', $staff->id) }}"
                                    class="text-edit hover-underline-edit hover:scale-105 transform transition duration-200">
                                    Edit
                                </a>

                                <div x-data="{ showModal: false }" class="inline">
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
                                                <span class="text-primary">{{ $staff->name }}</span>?</p>

                                            <form method="POST" action="{{ route('staff.destroy', $staff->id) }}">
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
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-4 text-center text-gray-500">No staff found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
