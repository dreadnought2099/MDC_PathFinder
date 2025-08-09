@extends('layouts.app')

@section('content')
    <x-floating-actions />
    <x-modal-scripts />
    <div x-data="{ tab: 'rooms' }" class="container mx-auto p-4">
        <nav class="mb-4 flex space-x-4 border-b border-primary">
            <button @click="tab = 'rooms'" :class="{ 'text-primary': tab === 'rooms' }"
                class="pb-2 cursor-pointer hover-underline">
                Trashed Rooms
            </button>
            <button @click="tab = 'staff'" :class="{ 'text-primary': tab === 'staff' }"
                class="pb-2 cursor-pointer hover-underline">
                Trashed Staff
            </button>
        </nav>

        <div x-show="tab === 'rooms'">
            <x-recycle-bin-table :items="$rooms" route-prefix="room" title="Trashed Rooms"
                empty-message="No trashed rooms found." />
        </div>

        <div x-show="tab === 'staff'">
            <x-recycle-bin-table :items="$staffs" route-prefix="staff" title="Trashed Staff Members"
                empty-message="No trashed staff members found." />
        </div>
    </div>
@endsection
