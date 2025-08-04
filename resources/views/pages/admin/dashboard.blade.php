@extends('layouts.app')

@section('content')
    <div class="text-center text-5xl">
        <h1>Admin Dashboard</h1>

        <div x-data="{ open: false }" @click.away="open = false"
            class="fixed bottom-6 right-6 flex flex-col items-end space-y-2">
            <!-- Buttons -->
            <template x-if="open">
                <div class="flex flex-col space-y-2 mb-2">

                    <a href="{{ route('room.create') }}" class="group flex items-center space-x-2">
                        <div
                            class="flex items-center justify-center w-10 h-10 bg-primary text-white rounded-full hover:bg-white hover:text-primary border-2 border-primary transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 21h18M5 21V5a1 1 0 011-1h12a1 1 0 011 1v16M9 21v-4h6v4M9 9h.01M9 13h.01M12 9h.01M12 13h.01M15 9h.01M15 13h.01" />
                            </svg>
                        </div>
                        <span class="text-sm text-gray-700">Create Room/Offices</span>
                    </a>
                    <a href="{{ route('staff.create') }}" class="group flex items-center space-x-2">
                        <div
                            class="flex items-center justify-center w-10 h-10 bg-primary text-white rounded-full hover:bg-white hover:text-primary border-2 border-primary transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A8 8 0 0112 15a8 8 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="text-sm text-gray-700">Add Staff Member</span>
                    </a>
                </div>
            </template>

            <!-- Floating + Button -->
            <button @click="open = !open"
                class="w-12 h-12 rounded-full bg-primary text-white text-4xl flex items-center justify-center shadow-lg hover:bg-white hover:text-primary border-2 border-primary transition-all duration-300 cursor-pointer">
                <span x-text="open ? '-' : '+'"></span>
            </button>
        </div>

        <div class="mb-4">
            <a href="{{ route('staff.index') }}" class="text-primary">
                Manage Staff
            </a>
        </div>
    </div>
@endsection
