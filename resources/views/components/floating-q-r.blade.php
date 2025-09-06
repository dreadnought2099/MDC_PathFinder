<div>
    <!-- Very little is needed to make a happy life. - Marcus Aurelius -->

    @props([
        'href' => '#',
        'icon' => null,
        'alt' => 'Icon',
        'title' => null,
    ])

    <div class="fixed bottom-6 right-6 flex flex-col items-end space-y-2 z-50">
        <a href="{{ route('scan.index') }}"
            class="group inline-flex items-center shadow-primary-hover justify-center p-3 sm:p-4 lg:p-5 rounded-full bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out border-2 border-primary hover:border-primary">
            <img src="{{ asset('icons/qr-code.png') }}" alt="Scan Office"
                class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 xl:w-14 xl:h-14 group-hover:scale-110 transition-all duration-300 ease-in-out filter group-hover:brightness-110"
                title="Scan office to know more">
        </a>
    </div>
</div>
