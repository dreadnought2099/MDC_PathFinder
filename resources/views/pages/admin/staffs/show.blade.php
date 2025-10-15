@extends('layouts.app')

@section('content')
    <!-- Floating Actions for admin access only -->
    <x-floating-actions />
    <x-staff.show :staff="$staff" />
@endsection
