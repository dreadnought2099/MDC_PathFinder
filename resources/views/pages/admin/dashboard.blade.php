@extends('layouts.app')

@section('content')
    <div class="text-center text-5xl">
        <h1>Admin Dashboard</h1>

        <a href="{{ route('room.create')}}">
            <button type="button" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-white hover:text-primary border-2 border-primary transition-all duration-300 cursor-pointer">
                Create Room/Office
            </button>
        </a>
        <a href="{{ route('staff.create')}}">
            <button type="button" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-white hover:text-primary border-2 border-primary transition-all duration-300 cursor-pointer">
                Add Staff
            </button>
        </a>
    </div>
@endsection
