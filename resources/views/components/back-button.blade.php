<div>
    <!-- Live as if you were to die tomorrow. Learn as if you were to live forever. - Mahatma Gandhi -->

    @props([
        'roomFallback' => 'room.index',
        'staffFallback' => 'staff.index',
        'landing' => 'admin.dashboard',
    ])

    @php
        $previous = url()->previous();
        $current = url()->current();

        // Detect if we're on an index page for rooms or staff
        $onRoomIndex = Route::is($roomFallback);
        $onStaffIndex = Route::is($staffFallback);

        // Always go to landing if we're on an index page
        if ($onRoomIndex || $onStaffIndex) {
            $previous = route($landing);
        }
        // Prevent loops back into edit/create pages
        elseif ($previous === $current || preg_match('/\/(edit|create|store)/', $previous)) {
            if (Route::is('staff.*')) {
                $previous = route($staffFallback);
            } else {
                $previous = route($roomFallback);
            }
        }
    @endphp

    @php
        $hideRoutes = ['admin.dashboard'];

        if (Route::is($hideRoutes)) {
            return;
        }
    @endphp

    <a href="{{ $previous }}"
        class="flex items-center text-black hover:text-[#157ee1] focus:outline-none cursor-pointer">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="ml-1">Back</span>
    </a>
</div>
