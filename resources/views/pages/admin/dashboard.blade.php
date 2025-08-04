@extends('layouts.app')

@section('content')
    <div class="w-full max-w-4xl mx-auto text-center text-5xl border-2 border-primary rounded-lg p-6">
        <h1>Admin Dashboard</h1>

        <x-floating-actions />

        <div class="flex space-x-4 justify-start mt-6">
            <div>
                <a href="{{ route('staff.index') }}"
                    class="text-primary inline-flex items-center space-x-2 hover-underline-hyperlink border-t-2 border-l-2 border-r-2 border-primary px-4 py-2 rounded">
                    <img src="{{ asset('icons/manage-staff.png') }}" alt="Manage Staff" class="h-10 w-10 object-contain" />
                    <span class="text-base">Manage Staff</span>
                </a>
            </div>
        </div>
    </div>
@endsection
