@extends('layouts.app')

@section('content')
    <div class="w-full min-h-screen max-w-4xl mx-auto text-center text-5xl border-2 border-primary rounded-lg p-12 mb-10">
        <h1><span class="text-primary">Admin</span> Dashboard</h1>

        <x-floating-actions />

        <div class="flex space-x-8 justify-start items-center mt-16">
            <a href="{{ route('staff.index') }}"
                class="text-primary inline-flex items-center space-x-2 hover-underline transform transition-all duration-300 hover:scale-105 border-t-2 border-l-2 border-r-2 border-primary px-4 py-2 rounded">
                <img src="{{ asset('icons/manager-1.png') }}" alt="Manage Staff" class="h-10 w-10 object-contain" />
                <span class="text-base">Manage Staff</span>
            </a>

            <a href="{{ route('room.index') }}"
                class="text-primary inline-flex items-center space-x-2 hover-underline transform transition-all duration-300 hover:scale-105 border-t-2 border-l-2 border-r-2 border-primary px-4 py-2 rounded">
                <img src="{{ asset('icons/manage-office1.png') }}" alt="Manage Room/Office"
                    class="h-10 w-10 object-contain" />
                <span class="text-base">Manage Room/Office</span>
            </a>

            <a href="{{ route('room.recycle-bin') }}"
                class="text-primary inline-flex items-center space-x-2 hover-underline transform transition-all duration-300 hover:scale-105 border-t-2 border-l-2 border-r-2 border-primary px-4 py-2 rounded">
                <img src="{{ asset('icons/recycle-bin.png') }}" alt="Recycle Bin" class="h-10 w-10 object-contain" />
                <span class="text-base">Recycle Bin</span>
            </a>
        </div>
    </div>
@endsection
