@props([
    'roomFallback' => 'room.index',
    'staffFallback' => 'staff.index',
    'pathFallback' => 'path.index',
    'trashedRoomFallback' => 'room.recycle-bin',
    'trashedStaffFallback' => 'staff.recycle-bin',
    'trashedPathFallback' => 'path.recycle-bin',
    'landing' => 'admin.dashboard',
])

@php
    // Get current route name
    $currentRouteName = Route::currentRouteName();

    // Fallback route names
    $roomFallback = 'room.index';
    $staffFallback = 'staff.index';
    $pathFallback = 'path.index';

    // Back URL default
    $backUrl = url()->previous();

    // Index pages: go back to dashboard
    if (str_ends_with($currentRouteName, '.index')) {
        $backUrl = route('admin.dashboard');
    }

    // Create pages: go back to index
    elseif (str_ends_with($currentRouteName, '.create')) {
        if ($currentRouteName === 'room.create') {
            $backUrl = route('room.index');
        } elseif ($currentRouteName === 'staff.create') {
            $backUrl = route('staff.index');
        } elseif ($currentRouteName === 'path.create') {
            $backUrl = route('path.index');
        }
    }

    // Edit pages...
    elseif (str_ends_with($currentRouteName, '.edit')) {
        if (isset($room) && $currentRouteName === 'room.edit') {
            $backUrl = route('room.show', $room);
        } elseif (isset($staff) && $currentRouteName === 'staff.edit') {
            $backUrl = route('staff.show', $staff);
        } elseif (isset($path) && $currentRouteName === 'path.edit') {
            $backUrl = route('path.show', $path);
        } else {
            $backUrl = route($roomFallback);
        }
    }

    // Show pages: go back to index
    elseif (str_ends_with($currentRouteName, '.show')) {
        if ($currentRouteName === 'room.show') {
            $backUrl = route('room.index');
        } elseif ($currentRouteName === 'staff.show') {
            $backUrl = route('staff.index');
        } elseif ($currentRouteName === 'path.show') {
            $backUrl = route('path.index');
        }
    }

    // Assign pages: avoid loop
    elseif (str_contains($currentRouteName, 'assign')) {
        $backUrl = route('room.index'); // or dashboard
    }
@endphp

{{-- In your back-button.blade.php component --}}
@if ($backUrl)
    <a href="{{ $backUrl }}"
        class="flex items-center space-x-2 focus:outline-none cursor-pointer hover:text-primary transition-colors duration-200 dark:text-gray-300"
        title="Go back">
        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </div>
        <span class="text-sm font-medium">Back</span>
    </a>
@endif
