@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Upload Path Image</h1>

    <a href="{{ route('path_images.index') }}" 
       class="bg-gray-500 text-white px-4 py-2 rounded mb-4 inline-block">
       Back to List
    </a>

    <form action="{{ route('path_images.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
        @csrf

        <div class="mb-4">
            <label for="path_id" class="block font-semibold">Select Path</label>
            <select name="path_id" id="path_id" class="w-full border px-3 py-2 rounded">
                <option value="">-- Choose Path --</option>
                @foreach($paths as $path)
                    <option value="{{ $path->id }}">
                        {{ $path->fromRoom->name }} → {{ $path->toRoom->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="file" class="block font-semibold">Upload Image</label>
            <input type="file" name="file" id="file" 
                   class="w-full border px-3 py-2 rounded">
        </div>

        <button type="submit" 
                class="bg-blue-600 text-white px-4 py-2 rounded">
            Upload
        </button>
    </form>
</div>
@endsection
