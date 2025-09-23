@extends('layouts.app')

@section('content')
    @if (auth()->user()->hasRole('Admin'))
        <x-floating-actions />

        <div class="max-w-7xl mx-auto mt-8">
            <!-- Page Title -->
            <div class="text-center mb-8">
                <h1 class="text-3xl lg:text-3xl font-bold text-gray-800 dark:text-white">
                    <span class="text-primary">Assign</span> Staff to Room
                </h1>
                <p class="text-base lg:text-lg text-gray-600 dark:text-gray-300">
                    Select a room and assign staff members to it.
                </p>
            </div>

            <!-- Room Selection Dropdown -->
            <div class="flex justify-center mb-6" x-data="{
                roomId: '{{ $selectedRoom->id ?? '' }}',
                fetchRoomData() {
                    if (!this.roomId) {
                        document.querySelector('#staff-content').innerHTML = '<div class=\'text-center py-12 text-gray-500 dark:text-gray-400\'>Select a room to assign staff</div>';
                        return;
                    }
            
                    window.showSpinner();
                    let url = `{{ route('room.assign') }}?roomId=${this.roomId}`;
            
                    window.history.replaceState({}, '', url);
            
                    fetch(url, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(res => res.text())
                        .then(html => {
                            let parser = new DOMParser();
                            let doc = parser.parseFromString(html, 'text/html');
                            let content = doc.querySelector('#staff-content');
                            if (content) {
                                document.querySelector('#staff-content').innerHTML = content.innerHTML;
                            }
                        })
                        .catch(err => console.error(err))
                        .finally(() => window.hideSpinner());
                }
            }">
                <select x-model="roomId" @change="fetchRoomData()"
                    class="border border-primary dark:bg-gray-800 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-primary dark:text-gray-300">
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}"
                            {{ isset($selectedRoom) && $selectedRoom->id == $room->id ? 'selected' : '' }}>
                            {{ $room->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Staff Content Area -->
            <div id="staff-content">
                @if (isset($selectedRoom))
                    @include('pages.admin.rooms.partials.staff-assignment', [
                        'selectedRoom' => $selectedRoom,
                        'staff' => $staff,
                    ])
                @endif
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        let currentStaffId = null;

        function openModal(staffId, staffName) {
            const modal = document.getElementById('confirmModal');
            const nameSpan = document.getElementById('modalMessage');
            const unassignForm = document.getElementById('unassignForm');
            const currentPage = new URLSearchParams(window.location.search).get('page') || '1';

            currentStaffId = staffId;
            nameSpan.textContent = staffName;
            unassignForm.action = `/admin/rooms/staff/${staffId}/remove?page=${currentPage}`;

            modal.classList.remove('hidden', 'opacity-0');
            const content = modal.querySelector('.bg-white');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');

            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('confirmModal');
            const content = modal.querySelector('.bg-white');

            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                currentStaffId = null;
            }, 300);
        }

        // Handle checkbox clicks for unassignment confirmation
        document.addEventListener('click', function(e) {
            if (e.target.type === 'checkbox' && e.target.hasAttribute('data-staff-id')) {
                if (!e.target.checked) {
                    e.preventDefault();
                    e.target.checked = true;
                    const staffCard = e.target.closest('.staff-card');
                    const staffName = staffCard.querySelector('h3').textContent.trim();
                    openModal(e.target.dataset.staffId, staffName);
                }
            }
        });

        // Escape key closes modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
@endpush