@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto p-4">
        <h1 class="text-2xl font-bold">{{ $room->name }}</h1>
        <p class="mt-2 text-gray-700">{{ $room->description }}</p>
        @if ($room->qr_code_path)
            <div class="mt-6">
                <h2 class="text-xl font-semibold">Room QR Code:</h2>
                <img src="{{ asset('storage/' . $room->qr_code_path) }}" alt="QR Code for {{ $room->name }}"
                    class="mt-2 w-48 h-48">
            </div>
        @endif

        <div class="mt-4">
            <h2 class="text-xl font-semibold">Staff in this Room:</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-2">
                @foreach ($room->staff as $member)
                    <div class="bg-white p-4 rounded shadow">
                        <img src="{{ asset('storage/' . $member->photo_path) }}" alt="{{ $member->name }}"
                            class="w-full h-40 object-cover mb-2 rounded">
                        <h3 class="text-lg font-bold">{{ $member->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $member->position }}</p>
                        @if ($member->email)
                            <p class="text-sm text-blue-600">{{ $member->email }}</p>
                        @endif
                        @if ($member->bio)
                            <p class="mt-1 text-sm">{{ $member->bio }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
