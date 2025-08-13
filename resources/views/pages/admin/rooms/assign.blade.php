@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Assign Staff to Room</h1>

        @if (session('success'))
            <div class="bg-green-200 text-green-800 p-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Room selection GET form for reloading page --}}
        <form method="GET" action="{{ route('room.assign') }}" class="mb-4">
            <select name="roomId" onchange="this.form.submit()" class="w-full border rounded p-2">
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}"
                        {{ isset($selectedRoom) && $selectedRoom->id == $room->id ? 'selected' : '' }}>
                        {{ $room->name }}
                    </option>
                @endforeach
            </select>
        </form>

        {{-- Staff assignment POST form --}}
        <form action="{{ route('room.assignStaff') }}" method="POST">
            @csrf
            {{-- Add hidden input to keep selected room --}}
            <input type="hidden" name="room_id" value="{{ $selectedRoom->id ?? '' }}">

            {{-- Staff checkboxes --}}
            <div class="mb-4">
                <label class="block font-medium">Select Staff:</label>
                <div class="space-y-2">
                    @foreach ($staff as $member)
                        @php
                            $assignedRoomId = $member->room_id;
                            $isSelectedRoom = isset($selectedRoom) && $assignedRoomId == $selectedRoom->id;
                            $isAssignedOtherRoom = $assignedRoomId && !$isSelectedRoom;
                        @endphp
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="staff_ids[]" value="{{ $member->id }}"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                @if ($assignedRoomId) checked @endif
                                @if ($isAssignedOtherRoom) disabled @endif>
                            <span>{{ $member->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit"
                class="bg-primary text-white px-4 py-2 rounded hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer">
                Assign
            </button>
        </form>
    </div>
@endsection
