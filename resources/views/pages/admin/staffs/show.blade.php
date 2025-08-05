@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow border-2 border-primary">
        <x-floating-actions />

        <h2 class="text-2xl font-bold mb-4">{{ $staff->name }}</h2>
        <p><strong>Position:</strong> {{ $staff->position ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $staff->email ?? 'N/A' }}</p>
        <p><strong>Phone:</strong> {{ $staff->phone_num ?? 'N/A' }}</p>
        <p><strong>Bio:</strong> {{ $staff->bio ?? 'N/A' }}</p>

        @if ($staff->photo_path)
            <div class="mt-4">
                <img src="{{ asset('storage/' . $staff->photo_path) }}" alt="Photo of {{ $staff->name }}"
                    class="w-40 h-40 object-cover rounded">
            </div>
        @endif
    </div>
@endsection
