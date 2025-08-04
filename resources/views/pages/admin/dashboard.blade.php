@extends('layouts.app')

@section('content')
    <div class="text-center text-5xl">
        <h1>Admin Dashboard</h1>

        <x-floating-actions />

        <div class="mb-4">
            <a href="{{ route('staff.index') }}" class="text-primary">
                Manage Staff
            </a>
        </div>
    </div>
@endsection
