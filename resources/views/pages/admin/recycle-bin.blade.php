@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">

    <x-recycle-bin-table
        :items="$rooms"
        route-prefix="room"
        title="Trashed Rooms"
        empty-message="No trashed rooms found."
    />

    <x-recycle-bin-table
        :items="$staffs"
        route-prefix="staff"
        title="Trashed Staff Members"
        empty-message="No trashed staff members found."
    />

</div>

<script>
    function showModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function hideModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function closeModal(event, modal) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    }
</script>
@endsection
