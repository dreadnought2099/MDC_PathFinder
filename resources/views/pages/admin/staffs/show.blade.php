@extends('layouts.app')

@section('content')
    <!-- Floating Actions for admin access only -->
    <div class="mb-8">
        <x-floating-actions />
    </div>

    <x-staff.show :staff="$staff" />
@endsection
