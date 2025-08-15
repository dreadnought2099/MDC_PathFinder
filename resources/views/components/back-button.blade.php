@props([
    'roomFallback' => 'room.index',
    'staffFallback' => 'staff.index',
    'trashedRoomFallback' => 'room.recycle-bin',
    'trashedStaffFallback' => 'staff.recycle-bin',
    'landing' => 'admin.dashboard',
])

@php
    $current = url()->current();
    $previous = url()->previous();
    $currentRouteName = Route::currentRouteName();

    // Define route patterns based on your actual routes
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
        'room_assignment_routes' => ['room.assign', 'room.assign.update', 'room.staff.remove'],
        'recycle_bin_routes' => ['room.recycle-bin', 'staff.recycle-bin'],
        'index_routes' => ['room.index', 'staff.index'],
        'show_routes' => ['room.show', 'staff.show'],
        'edit_routes' => ['room.edit', 'staff.edit'],
        'create_routes' => ['room.create', 'staff.create'],
    ];

    // Helper function to check if current route matches any pattern
    $isCurrentRoute = function ($routes) use ($currentRouteName) {
        return in_array($currentRouteName, $routes);
    };

    // Helper function to check if previous URL contains problematic patterns
    $previousContains = function ($patterns) use ($previous) {
        foreach ($patterns as $pattern) {
            if (strpos($previous, $pattern) !== false) {
                return true;
            }
        }
        return false;
    };

    // Determine the correct back URL based on current route
    $backUrl = null;

    // Dashboard - hide back button
    if ($currentRouteName === 'admin.dashboard') {
        $backUrl = null;
    }
    // Profile page - go to dashboard
    elseif ($currentRouteName === 'admin.profile') {
        $backUrl = route($landing);
    }
    // Index pages and recycle bin - always go to dashboard
    elseif ($isCurrentRoute($routePatterns['index_routes']) || $isCurrentRoute($routePatterns['recycle_bin_routes'])) {
        $backUrl = route($landing);
    }
    // Room assignment pages - go to room index
    elseif ($isCurrentRoute($routePatterns['room_assignment_routes'])) {
        $backUrl = route($roomFallback);
    }
    // Create pages - go to respective index
    elseif ($currentRouteName === 'room.create') {
        $backUrl = route($roomFallback);
    } elseif ($currentRouteName === 'staff.create') {
        $backUrl = route($staffFallback);
    }
    // Edit pages - go to show page if we have the model, otherwise index
    elseif ($currentRouteName === 'room.edit') {
        $backUrl = isset($room) ? route('room.show', $room) : route($roomFallback);
    } elseif ($currentRouteName === 'staff.edit') {
        $backUrl = isset($staff) ? route('staff.show', $staff) : route($staffFallback);
    }
    // Show pages - go to index
    elseif ($currentRouteName === 'room.show') {
        $backUrl = route($roomFallback);
    } elseif ($currentRouteName === 'staff.show') {
        $backUrl = route($staffFallback);
    }
    // Print QR Code - go to room show page
    elseif ($currentRouteName === 'room.print-qrcode') {
        $backUrl = isset($room) ? route('room.show', $room) : route($roomFallback);
    }
    // Restore and force delete actions - go back to recycle bin
    elseif ($currentRouteName === 'room.restore' || $currentRouteName === 'room.forceDelete') {
        $backUrl = route($trashedRoomFallback);
    } elseif ($currentRouteName === 'staff.restore' || $currentRouteName === 'staff.forceDelete') {
        $backUrl = route($trashedStaffFallback);
    }
    // Carousel image removal - go back to room show
    elseif ($currentRouteName === 'room.carousel.remove') {
        $backUrl = isset($room) ? route('room.show', $room) : route($roomFallback);
    }
    // Fallback logic for edge cases
    else {
        // Prevent loops and problematic previous URLs
        $problematicPatterns = ['/create', '/edit', '/store', '/update', '/delete', '/restore'];

        if ($previous === $current || $previousContains($problematicPatterns)) {
            // Determine fallback based on current route context
            if (strpos($currentRouteName, 'room.') === 0) {
                $backUrl = route($roomFallback);
            } elseif (strpos($currentRouteName, 'staff.') === 0) {
                $backUrl = route($staffFallback);
            } else {
                $backUrl = route($landing);
            }
        } else {
            // Use previous URL if it's safe
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
