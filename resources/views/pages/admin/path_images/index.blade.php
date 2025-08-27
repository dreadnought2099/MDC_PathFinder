@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Path Images</h1>

    <a href="{{ route('path_images.create') }}" 
       class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">
       Upload New Image
    </a>

    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th class="px-4 py-2 border">#</th>
                <th class="px-4 py-2 border">Path</th>
                <th class="px-4 py-2 border">Image</th>
                <th class="px-4 py-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($images as $image)
                <tr>
                    <td class="px-4 py-2 border">{{ $image->id }}</td>
                    <td class="px-4 py-2 border">
                        {{ $image->path->fromRoom->name }} → {{ $image->path->toRoom->name }}
                    </td>
                    <td class="px-4 py-2 border">
                        <img src="{{ asset('storage/' . $image->file_path) }}" 
                             alt="Path Image" class="h-20 w-auto">
                    </td>
                    <td class="px-4 py-2 border">
                        <form action="{{ route('path_images.destroy', $image->id) }}" 
                              method="POST" onsubmit="return confirm('Delete this image?')">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-600 text-white px-3 py-1 rounded">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
