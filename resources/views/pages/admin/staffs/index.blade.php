@extends('layouts.app') {{-- Adjust if your layout filename is different --}}

@section('content')
    <div class="p-6">
        <h1 class="text-2xl text-center mb-4">Staff List</h1>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif


        {{-- Staff Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffs as $staff)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $staff->name }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('staff.show', $staff->id) }}" class="text-blue-600 hover-underline">
                                    View
                                </a>
                            </td>
                            <td class="px-4 py-2">
                                <a href="{{ route('staff.edit', $staff->id) }}" class="text-blue-600 hover:underline">
                                    Edit
                                </a>
                            </td>
                            <form action="{{ route('staff.destroy', $staff->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-secondary">Delete</button>
                            </form>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">No staff found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
