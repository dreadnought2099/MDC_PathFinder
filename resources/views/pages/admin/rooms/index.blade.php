@extends('layouts.app')

@section('content')
    <div class="p-4">
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
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
