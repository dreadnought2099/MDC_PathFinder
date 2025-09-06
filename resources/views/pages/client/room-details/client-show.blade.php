@extends('layouts.guest')

@section('content')
    <x-staff.show :staff="$staff" />
@endsection
