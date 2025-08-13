@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Assign Staff to Room</h1>

        @if (session('success'))
            <div class="bg-green-200 text-green-800 p-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('room.assignStaff') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block font-medium">Select Room:</label>
                <select name="room_id" class="w-full border rounded p-2">
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}"
                            {{ isset($selectedRoom) && $selectedRoom->id == $room->id ? 'selected' : '' }}>
                            {{ $room->name }}
                        </option>
                    @endforeach
                </select>

            </div>

            <div class="mb-4">
                <label class="block font-medium">Select Staff:</label>
                <div class="space-y-2">
                    @foreach ($staff as $member)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="staff_ids[]" value="{{ $member->id }}"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span>{{ $member->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Assign</button>
        </form>
    </div>
@endsection
