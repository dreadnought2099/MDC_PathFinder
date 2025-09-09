@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Server Error'))

@section('content')
    <div class="mt-8">
        <a href="{{ url('/') }}"
            onclick="event.preventDefault(); history.length > 1 ? history.back() : window.location='{{ url('/') }}';"
            class="w-full bg-primary text-white px-4 py-2 rounded-full border-2 border-primary duration-300 transition-all ease-in-out mt-4 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer hover:bg-white hover:text-primary dark:hover:bg-gray-800">
            Go Back
        </a>
    </div>
@endsection