@extends('layouts.app')

@section('content')
    <div class="p-4">
        <h2 class="text-xl font-bold mb-4">Room List</h2>

        <a href="{{ route('room.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Add Room</a>

        <table class="w-full border-collapse border">
            <thead>
                <tr>
                    <th class="border p-2">Name</th>
                    <th class="border p-2">Description</th>
                    <th class="border p-2">Marker ID</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rooms as $room)
                    <tr>
                        <td class="border p-2">{{ $room->name }}</td>
                        <td class="border p-2">{{ $room->description }}</td>
                        <td class="border p-2">{{ $room->marker_id }}</td>
                        <td class="border p-2">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
