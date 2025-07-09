@extends('layouts.guest')

@section('content')
    <div class="min-h-screen py-10 px-4">
        <div class="max-w-xl mx-auto bg-white border border-primary shadow-md rounded-lg p-6">
            <h2 class="text-2xl text-center mb-6">Available Rooms</h2>

            <ul class="space-y-4">
                @foreach ($rooms as $room)
                    @if ($room->marker_id !== $markerId)
                        <li class="border border-primary rounded px-4 py-3 hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <span>{{ $room->name }}</span>
                                <a href="{{ route('ar.navigate', ['sourceMarkerId' => $markerIdentifier, 'roomId' => $room->id]) }}"
                                    class="text-blue-600 hover:underline">
                                    Navigate →
                                </a>
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>

            <div class="mt-6 text-center">
                <a href="/" class="text-gray-500 hover:text-gray-800 text-sm">Back to Home</a>
            </div>
        </div>
    </div>
@endsection
