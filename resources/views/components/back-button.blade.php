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
    $current = url()->current();
    $previous = url()->previous();
    $currentRouteName = Route::currentRouteName();

    // Define route patterns
    $routePatterns = [
        'room_routes' => [
            'room.index',
            'room.create',
            'room.store',
            'room.show',
            'room.edit',
            'room.update',
            'room.destroy',
            'room.print-qrcode',
            'room.restore',
            'room.forceDelete',
            'room.carousel.remove',
        ],
        'staff_routes' => [
            'staff.index',
            'staff.create',
            'staff.store',
            'staff.show',
            'staff.edit',
            'staff.update',
            'staff.destroy',
            'staff.restore',
            'staff.forceDelete',
        ],
        'path_routes' => [
            'path.index',
            'path.create',
            'path.store',
            'path.show',
            'path.edit',
            'path.update',
            'path.destroy',
            'path.restore',
            'path.forceDelete',
        ],
        'room_assignment_routes' => ['room.assign', 'room.assign.update', 'room.staff.remove'],
        'recycle_bin_routes' => ['room.recycle-bin', 'staff.recycle-bin', 'path.recycle-bin'],
        'index_routes' => ['room.index', 'staff.index', 'path.index'],
        'show_routes' => ['room.show', 'staff.show', 'path.show'],
        'edit_routes' => ['room.edit', 'staff.edit', 'path.edit'],
        'create_routes' => ['room.create', 'staff.create', 'path.create'],
    ];

    // Helper: check if current route matches list
    $isCurrentRoute = fn($routes) => in_array($currentRouteName, $routes);

    // Helper: check if previous URL contains problematic patterns
    $previousContains = fn($patterns) => collect($patterns)->contains(
        fn($pattern) => strpos($previous, $pattern) !== false,
    );

    $backUrl = null;

    // Dashboard page: hide back button
    if ($currentRouteName === 'admin.dashboard') {
        $backUrl = null;
    }
    // Profile page: always go to dashboard
    elseif ($currentRouteName === 'admin.profile') {
        $backUrl = route($landing);
    }
    // Index pages and recycle bin: always go to dashboard
    elseif ($isCurrentRoute($routePatterns['index_routes']) || $isCurrentRoute($routePatterns['recycle_bin_routes'])) {
        $backUrl = route($landing);
    }
    // Room assignment pages: go to room index
    elseif ($isCurrentRoute($routePatterns['room_assignment_routes'])) {
        $backUrl = route($roomFallback);
    }
    // Create pages: go back to respective index
    elseif (str_ends_with($currentRouteName, '.create')) {
        if (str_starts_with($currentRouteName, 'room.')) {
            $backUrl = route($roomFallback);
        } elseif (str_starts_with($currentRouteName, 'staff.')) {
            $backUrl = route($staffFallback);
        } elseif (str_starts_with($currentRouteName, 'path.')) {
            $backUrl = route($pathFallback);
        }
    }
    // Edit pages: go to show page if model exists, otherwise index
    elseif (str_ends_with($currentRouteName, '.edit')) {
        if (isset($room) && $currentRouteName === 'room.edit') {
            $backUrl = route('room.show', $room);
        } elseif (isset($staff) && $currentRouteName === 'staff.edit') {
            $backUrl = route('staff.show', $staff);
        } elseif (isset($path) && $currentRouteName === 'path.edit') {
            $backUrl = route('path.show', $path);
        } else {
            $backUrl = $roomFallback;
        } // fallback if model not set
    }
    // Show pages: go to respective index
    elseif (str_ends_with($currentRouteName, '.show')) {
        if (str_starts_with($currentRouteName, 'room.')) {
            $backUrl = route($roomFallback);
        } elseif (str_starts_with($currentRouteName, 'staff.')) {
            $backUrl = route($staffFallback);
        } elseif (str_starts_with($currentRouteName, 'path.')) {
            $backUrl = route($pathFallback);
        }
    }
    // Print QR code page: go back to room show page
    elseif (str_ends_with($currentRouteName, '.print-qrcode')) {
        $backUrl = isset($room) ? route('room.show', $room) : route($roomFallback);
    }
    // Restore or force delete actions: go back to recycle bin
    elseif (str_ends_with($currentRouteName, '.restore') || str_ends_with($currentRouteName, '.forceDelete')) {
        if (str_starts_with($currentRouteName, 'room.')) {
            $backUrl = route($trashedRoomFallback);
        } elseif (str_starts_with($currentRouteName, 'staff.')) {
            $backUrl = route($trashedStaffFallback);
        } elseif (str_starts_with($currentRouteName, 'path.')) {
            $backUrl = route($trashedPathFallback);
        }
    }
    // Carousel image removal: go back to room show
    elseif ($currentRouteName === 'room.carousel.remove') {
        $backUrl = isset($room) ? route('room.show', $room) : route($roomFallback);
    }
    // Fallback logic for edge cases
    else {
        $problematicPatterns = ['/create', '/edit', '/store', '/update', '/delete', '/restore'];

        if ($previous === $current || $previousContains($problematicPatterns)) {
            if (str_starts_with($currentRouteName, 'room.')) {
                $backUrl = route($roomFallback);
            } elseif (str_starts_with($currentRouteName, 'staff.')) {
                $backUrl = route($staffFallback);
            } elseif (str_starts_with($currentRouteName, 'path.')) {
                $backUrl = route($pathFallback);
            } else {
                $backUrl = route($landing);
            }
        } else {
            $backUrl = $previous;
        }
    }
@endphp

@if ($backUrl)
    <a href="{{ $backUrl }}"
        class="flex items-center text-black hover:text-[#157ee1] focus:outline-none cursor-pointer transition-colors duration-200"
        title="Go back">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="ml-1">Back</span>
    </a>
@endif
