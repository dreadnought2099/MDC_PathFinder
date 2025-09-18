@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div class="max-w-7xl mx-auto mt-8">
        <!-- Page Title -->
        <div class="text-center mb-8">
            <h1 class="text-3xl lg:text-3xl font-bold text-gray-800 dark:text-white">
                <span class="text-primary">Office User</span> Management
            </h1>
            <p class="text-base lg:text-lg text-gray-600 dark:text-gray-300">
                Manage users across different offices and rooms.
            </p>
        </div>

        <!-- Filter Dropdown -->
        <div class="flex justify-start mb-6" x-data="{
            roomId: '{{ $roomId }}',
            fetchUsers() {
                window.showSpinner();
                let url = `{{ route('room-user.index') }}?roomId=${this.roomId}`;
        
                // Push to browser history (so it stays on reload)
                window.history.replaceState({}, '', url);
        
                fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.text())
                    .then(html => {
                        let parser = new DOMParser();
                        let doc = parser.parseFromString(html, 'text/html');
                        let table = doc.querySelector('#users-table');
                        if (table) {
                            document.querySelector('#users-table').innerHTML = table.innerHTML;
                        }
                    })
                    .catch(err => console.error(err))
                    .finally(() => window.hideSpinner());
            }
        }">
            <select x-model="roomId" @change="fetchUsers"
                class="border border-primary dark:bg-gray-800 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-primary dark:text-gray-300">
                <option value="">All Office Users</option>
                @if (auth()->user()->hasRole('Admin') || auth()->user()->can('view room users'))
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" {{ $roomId == $room->id ? 'selected' : '' }}>
                            {{ $room->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <!-- Table (dynamic reload zone) -->
        <div id="users-table">
            @include('pages.admin.room-users.partials.table', ['users' => $users])
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function openUserModal(id, username) {
            const modal = document.getElementById('userDeleteModal');
            const nameSpan = document.getElementById('userName');
            const form = document.getElementById('userDeleteForm');

            // Set username in modal
            nameSpan.textContent = username;

            // Set form action using Laravel route helper
            form.action = "{{ route('room-user.destroy', ':id') }}".replace(':id', id);

            // Show modal with smooth scale and opacity animation
            modal.classList.remove('hidden', 'opacity-0');
            const content = modal.querySelector('.bg-white');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');

            // Prevent background scrolling while modal is open
            document.body.style.overflow = 'hidden';
        }

        function closeUserModal() {
            const modal = document.getElementById('userDeleteModal');
            const content = modal.querySelector('.bg-white');

            // Animate out: scale down and fade
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');

            // Hide after animation completes
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 310); // duration matches CSS transition
        }

        function openToggleModal(id, username, action) {
            // Update modal text
            document.getElementById('toggleUserName').textContent = username;
            document.getElementById('toggleActionLabel').textContent = action.charAt(0).toUpperCase() + action.slice(1);
            document.getElementById('toggleActionText').textContent = action;

            // Use Laravel route helper instead of hardcoded URL
            const form = document.getElementById('userToggleForm');
            form.action = `/admin/room-users/${id}/toggle-status`;
            // form.action = "{{ route('room-user.toggle-status', ['user' => ':id']) }}".replace(':id', id);


            // Show modal with smooth scale and opacity animation
            const modal = document.getElementById('userToggleModal');
            modal.classList.remove('hidden', 'opacity-0');
            const content = modal.querySelector('.bg-white');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');

            // Prevent background scrolling
            document.body.style.overflow = 'hidden';
        }

        function closeToggleModal() {
            const modal = document.getElementById('userToggleModal');
            const content = modal.querySelector('.bg-white');

            // Animate out: scale down and fade
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');

            // Hide after animation completes
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 300); // duration matches CSS transition
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeUserModal();
                closeToggleModal();
            }
        });
    </script>
@endpush
