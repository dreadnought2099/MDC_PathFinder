@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Create Path</h1>

        <form action="{{ route('path.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block font-medium">From Room</label>
                <select name="from_room_id" class="border rounded w-full p-2">
                    <option value="">-- Select Room --</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" {{ old('from_room_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium">To Room</label>
                <select name="to_room_id" class="border rounded w-full p-2">
                    <option value="">-- Select Room --</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" {{ old('to_room_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
                <a href="{{ route('path.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</a>
            </div>
        </form>
    </div>
@endsection
