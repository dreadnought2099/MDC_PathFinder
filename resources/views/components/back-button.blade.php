@props([
    'path' => null,
    'roomFallback' => 'room.index',
    'staffFallback' => 'staff.index',
    'pathFallback' => 'path.index',
    'roomUserFallback' => 'room-user.index',
    'landing' => 'admin.dashboard',
    'tab' => 'rooms',
])

@php
    $currentRouteName = Route::currentRouteName();
    $backUrl = null;

    // Index pages → dashboard
    if (str_ends_with($currentRouteName, '.index')) {
        $backUrl = route($landing);
    }

    // Create pages → index
    elseif (str_ends_with($currentRouteName, '.create')) {
        if ($currentRouteName === 'room.create') {
            $backUrl = route('room.index');
        } elseif ($currentRouteName === 'staff.create') {
            $backUrl = route('staff.index');
        } elseif ($currentRouteName === 'path-image.create') {
            $backUrl = route('path.index');
        } elseif ($currentRouteName === 'room-user.create') {
            $backUrl = route('room-user.index');
        }
    }

    // Edit pages → fallback to index or path.show
    elseif (str_ends_with($currentRouteName, '.edit')) {
        if ($currentRouteName === 'room.edit') {
            $backUrl = route('room.index');
        } elseif ($currentRouteName === 'staff.edit') {
            $backUrl = route('staff.index');
        } elseif ($currentRouteName === 'room-user.edit') {
            $backUrl = route('room-user.index');
        } elseif ($currentRouteName === 'path-image.edit') {
            $backUrl = $path ? route('path.show', $path) : route('path.index');
        }
    }

    // Show pages → index
    elseif (str_ends_with($currentRouteName, '.show')) {
        if ($currentRouteName === 'room.show') {
            $backUrl = route('room.index');
        } elseif ($currentRouteName === 'staff.show') {
            $backUrl = route('staff.index');
        } elseif ($currentRouteName === 'path.show') {
            $backUrl = route('path.index');
        } elseif ($currentRouteName === 'room-user.show') {
            $backUrl = route('room-user.index');
        }
    }

    // Recycle bin pages → dynamic based on active tab
    elseif (str_contains($currentRouteName, 'recycle-bin')) {
        // Default URLs for each tab
        $tabUrls = [
            'rooms' => route('room.index'),
            'staff' => route('staff.index'),
            'users' => route('room-user.index'),
        ];

        // Use the current tab or default to 'rooms'
        $backUrl = $tabUrls[$tab] ?? $tabUrls['rooms'];
    }

    // Assign pages → room.index
    elseif (str_contains($currentRouteName, 'assign')) {
        $backUrl = route('room.index');
    }

    // Profile → dashboard
    elseif ($currentRouteName === 'admin.profile') {
        $backUrl = route($landing);
    }
@endphp

@if ($backUrl)
    <div x-data="{
        currentTab: '{{ $tab }}',
        backUrl: '{{ $backUrl }}'
    }" x-init="// Listen for tab changes from recycle bin page
    window.addEventListener('tab-changed', (e) => {
        if ('{{ str_contains($currentRouteName, 'recycle-bin') ? 'true' : 'false' }}' === 'true') {
            currentTab = e.detail.tab;
            const tabUrls = {
                'rooms': '{{ route('room.index') }}',
                'staff': '{{ route('staff.index') }}',
                'users': '{{ route('room-user.index') }}'
            };
            backUrl = tabUrls[currentTab] || tabUrls['rooms'];
        }
    });">
        <a :href="backUrl"
            class="flex items-center space-x-2 focus:outline-none cursor-pointer hover:text-primary transition-colors duration-200 dark:text-gray-300"
            title="Go back">
            <div class="flex items-center justify-center h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </div>
            <span class="text-sm font-medium">Back</span>
        </a>
    </div>
@endif