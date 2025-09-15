@extends('layouts.app')

@section('content')
    <x-floating-actions />
    <div class="space-y-4">
        <div class="mt-4 flex justify-center">
            {{ $users->appends(request()->query())->links('pagination::tailwind') }}
        </div>

        <!-- Room Filter -->
        <div class="flex justify-start">
            <select onchange="window.location='?roomId=' + this.value"
                class="border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">All Office Users</option>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}" {{ $roomId == $room->id ? 'selected' : '' }}>
                        {{ $room->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Staff Table -->
        <div class="overflow-x-auto bg-white rounded-lg shadow-md border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Username
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Room</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Position
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 font-sofia">
                    @foreach ($users as $u)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 font-medium">{{ $u->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $u->username }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $u->room->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                {{ $u->getRoleNames()->implode(', ') ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                <a href="{{ route('room-user.edit', $u->id) }}"
                                    class="text-blue-600 hover:text-blue-800">Edit</a>
                                <form action="{{ route('room-user.destroy', $u->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
