@extends('layouts.app')

@section('content')
    <x-floating-actions />
    
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Paths</h1>

        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">ID</th>
                    <th class="border p-2">From Room</th>
                    <th class="border p-2">To Room</th>
                    <th class="border p-2">Angle</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paths as $path)
                    <tr>
                        <td class="border p-2">{{ $path->id }}</td>
                        <td class="border p-2">{{ $path->fromRoom->name ?? 'N/A' }}</td>
                        <td class="border p-2">{{ $path->toRoom->name ?? 'N/A' }}</td>
                        <td class="border p-2">{{ $path->angle ?? '-' }}</td>
                        <td class="border p-2">
                            <form action="{{ route('paths.destroy', $path) }}" method="POST"
                                onsubmit="return confirm('Delete this path?')">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-600 text-white px-3 py-1 rounded">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="border p-2 text-center text-gray-500">No paths available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
