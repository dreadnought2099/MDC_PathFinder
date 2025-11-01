@props(['showBackButton' => false])

<nav class="fixed top-0 left-0 right-0 z-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <div class="flex-shrink-0">
                @if ($showBackButton)
                    <x-guest.guest-back-button />
                @endif
            </div>

            <div class="flex items-center gap-4 sm:gap-6 h-16">
                <x-dark-mode-toggle />
                <x-guest.about-page />
                <x-guest.team-modal />
            </div>
        </div>
    </div>
</nav>